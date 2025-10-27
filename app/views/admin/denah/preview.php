<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        <div class="col-md-10 p-4">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-th"></i> Preview Denah</h2>
                    <h5 class="text-muted"><?= e($periode['nama_periode']) ?> — <?= e($sesi['nama_sesi']) ?></h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= url('sesi/manageDenah/' . $periode['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <?php foreach ($sesi_list as $s): ?>
                        <a class="btn <?= ($s['id']==$sesi['id']?'btn-primary':'btn-outline-primary') ?>" href="<?= url('sesi/previewDenah/' . $periode['id'] . '/' . $s['id']) ?>">Sesi <?= e($s['nama_sesi']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php
                $sessionColors = ['#2ecc71', '#e74c3c', '#f39c12', '#3498db', '#9b59b6', '#1abc9c'];
                $selectedIndex = 0;
                if (!empty($sesi_list)) {
                    foreach ($sesi_list as $idx => $sx) {
                        if ((int)$sx['id'] === (int)$sesi['id']) { $selectedIndex = $idx; break; }
                    }
                }
                $currentColor = $sessionColors[$selectedIndex % count($sessionColors)];
            ?>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
                :root { --current-color: <?= $currentColor ?>; }
                body { font-family: 'Poppins', sans-serif; }
                .map-container { background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 20px; max-width: 950px; margin-left: auto; margin-right: auto; }
                .map-title { text-align: center; margin-bottom: 16px; color: #2c3e50; font-weight: 600; position: relative; }
                .map-title:after { content: ''; display: block; width: 80px; height: 4px; background: linear-gradient(90deg, var(--current-color), transparent); margin: 10px auto 0; border-radius: 2px; }
                .map-scroll { overflow-x: auto; display: flex; justify-content: center; }
                .seat-map { width: auto; border-collapse: separate; border-spacing: 5px; margin: 0 auto; table-layout: fixed; }
                .seat-map th { background-color: var(--current-color); color: #fff; padding: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; border-radius: 5px; }
                .seat-map td { text-align: center; vertical-align: middle; font-weight: 500; border-radius: 5px; transition: all .3s; position: relative; width: 44px; min-width: 44px; height: 36px; padding: 2px; line-height: 1.05; word-break: break-word; background: #e9eef2; border: 1px solid #fff; }
                .seat-map td.available-seat { background-color: #fff; color: #7f8c8d; box-shadow: 0 2px 5px rgba(0,0,0,.05); }
                .seat-map td.occupied-seat { background-color: #e74c3c; color: #fff; }
                .aisle { background-color: #e9eef2; }
                .legend { display: flex; justify-content: center; margin-top: 12px; flex-wrap: wrap; }
                .legend-item { display: flex; align-items: center; margin: 0 15px 10px; }
                .legend-color { width: 20px; height: 20px; border-radius: 4px; margin-right: 8px; }
                @media (max-width: 768px) {
                    .seat-map td { width: 34px; min-width: 34px; height: 30px; font-size: 9px; }
                    .seat-map th { padding: 8px; font-size: 12px; }
                }
                @media (max-width: 576px) {
                    .seat-map td { width: 28px; min-width: 28px; height: 26px; font-size: 8px; }
                }
            </style>

            <div class="card">
                <div class="card-header text-center">
                    <h5 class="mb-0">DENAH TEMPAT DUDUK – <?= e($sesi['nama_sesi']) ?></h5>
                </div>
                <div class="card-body">
                    <div class="map-container">
                        <h2 class="map-title">DENAH TEMPAT DUDUK – <?= e($sesi['nama_sesi']) ?></h2>
                        <?php if (empty($rows)): ?>
                            <div class="alert alert-warning">Belum ada denah untuk sesi ini. Silakan impor denah pada halaman sebelumnya.</div>
                        <?php else: ?>
                            <div class="map-scroll">
                                <table class="seat-map" style="min-width: <?= max(1,(int)$max_col) * 55 ?>px;">
                                    <thead>
                                        <tr>
                                            <th colspan="<?= (int)$max_col ?>" style="text-align: center;">SENATOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $r): ?>
                                            <tr>
                                                <?php for ($c=1; $c <= $max_col; $c++): ?>
                                                    <?php 
                                                        $key = $r . '-' . $c; 
                                                        $label = $grid[$key] ?? ''; 
                                                        $isOcc = false;
                                                        if ($label !== '') {
                                                            $norm = strtoupper(preg_replace('/[^A-Z0-9]/','', $label));
                                                            $norm = preg_replace_callback('/\\d+/', function($m){ $v = ltrim($m[0], '0'); return $v === '' ? '0' : $v; }, $norm);
                                                            $isOcc = isset($occupied[$label]) || (isset($occupied_norm) && isset($occupied_norm[$norm]));
                                                        }
                                                    ?>
                                                    <?php if ($label === ''): ?>
                                                        <td class="aisle">&nbsp;</td>
                                                    <?php elseif ($isOcc): ?>
                                                        <td class="occupied-seat"><?= e($label) ?></td>
                                                    <?php else: ?>
                                                        <td class="available-seat"><?= e($label) ?></td>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="legend">
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #e74c3c;"></div>
                                    <span>Sudah Hadir</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color" style="background-color: #fff; border: 1px solid #dee2e6;"></div>
                                    <span>Belum Hadir</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
