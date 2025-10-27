<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-12 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h2><i class="fas fa-chart-bar"></i> Ringkasan Sesi</h2>
          <h5 class="text-muted"><?= e($periode['nama_periode']) ?></h5>
        </div>
        <a href="<?= url('lo/dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Pilih Periode</a>
      </div>
      <?php
        $sessionColors = ['#2ecc71', '#e74c3c', '#f39c12', '#3498db', '#9b59b6', '#1abc9c'];
        $selectedIndex = 0;
        if (!empty($sesi_list)) {
          foreach ($sesi_list as $idx => $sx) {
            if ($sesi && (int)$sx['id'] === (int)$sesi['id']) { $selectedIndex = $idx; break; }
          }
        }
        $currentColor = $sessionColors[$selectedIndex % count($sessionColors)];
      ?>
      <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
        :root { --current-color: <?= $currentColor ?>; }
        body { background-color: #f8f9fa; color: #333; font-family: 'Poppins', sans-serif !important; }
        .header-container { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 10px; padding: 15px; margin-top: 10px; margin-bottom: 20px; max-width: 950px; margin-left: auto; margin-right: auto; }
        .session-btn { font-weight: 500; letter-spacing: 0.5px; transition: all 0.3s; margin-bottom: 10px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); color:#fff; }
        .session-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.12); }
        .menu-btn { background-color: #34495e; color: white; }
        .map-container { background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); padding: 20px; margin-bottom: 20px; overflow-x: auto; max-width: 950px; margin-left: auto; margin-right: auto; }
        .map-title { text-align: center; margin-bottom: 16px; color: #2c3e50; font-weight: 600; position: relative; }
        .map-title:after { content: ''; display: block; width: 80px; height: 4px; background: linear-gradient(90deg, var(--current-color), transparent); margin: 10px auto 0; border-radius: 2px; }
        .seat-map { width: auto; border-collapse: separate; border-spacing: 5px; margin: 0 auto; table-layout: fixed; }
        .seat-map tr { display: table-row; }
        .seat-map td, .seat-map th { display: table-cell; }
        .seat-map th { background-color: var(--current-color); color: white; padding: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; border-radius: 5px; }
        .seat-map td { text-align: center; vertical-align: middle; font-weight: 500; border-radius: 5px; transition: all 0.3s; position: relative; width: 44px; min-width: 44px; height: 36px; padding: 2px; line-height: 1.05; word-break: break-word; background: #e9eef2; border: 1px solid #fff; }
        .map-scroll { overflow-x: auto; display: flex; justify-content: center; }
        .seat-map td.available-seat { background-color: #fff; color: #7f8c8d; box-shadow: 0 2px 5px rgba(0,0,0,0.05); cursor: default; }
        .seat-map td.available-seat:hover { transform: none; box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
        .seat-map td.occupied-seat { background-color: #e74c3c; color: #fff; }
        .aisle { background-color: #e9eef2; }
        .legend { display: flex; justify-content: center; margin-top: 12px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; margin: 0 15px 10px; }
        .legend-color { width: 20px; height: 20px; border-radius: 4px; margin-right: 8px; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        @media (max-width: 768px) {
          .seat-map td { width: 34px; min-width: 34px; height: 30px; font-size: 9px; }
          .seat-map th { padding: 8px; font-size: 12px; }
          .session-btn { font-size: 13px; padding: 8px; }
        }
        @media (max-width: 576px) {
          .map-container { padding: 10px; }
          .seat-map td { width: 28px; min-width: 28px; height: 26px; font-size: 8px; }
        }
      </style>
      <div class="header-container">
        <div class="row">
          <?php if (empty($sesi_list)): ?>
            <div class="col-12"><div class="alert alert-warning mb-0">Belum ada sesi pada periode ini.</div></div>
          <?php else: ?>
            <?php foreach ($sesi_list as $idx => $s): $btnColor = $sessionColors[$idx % count($sessionColors)]; ?>
              <div class="col-12 col-sm-4">
                <a href="<?= url('lo/overview/' . $periode['id'] . '/' . $s['id']) ?>" class="btn form-control session-btn" style="background-color: <?= $btnColor ?>;<?= ($sesi && (int)$sesi['id']===(int)$s['id']) ? ' filter: brightness(0.9);' : '' ?>">
                  <i class="fas fa-calendar-day"></i> <?= e($s['nama_sesi']) ?>
                </a>
              </div>
            <?php endforeach; ?>
            <div class="col-12">
              <a href="<?= url('lo/dashboard') ?>" class="btn form-control session-btn menu-btn">
                <i class="fas fa-bars"></i> Menu Utama
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($sesi): ?>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card bg-primary text-white"><div class="card-body"><h6>Total Wisudawan</h6><h3><?= (int)($stats['total'] ?? 0) ?></h3></div></div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white"><div class="card-body"><h6>Sudah Hadir</h6><h3><?= (int)($stats['presensi_hadir'] ?? 0) ?></h3></div></div>
        </div>
        <div class="col-md-4">
          <div class="card bg-danger text-white"><div class="card-body"><h6>Tidak Hadir</h6><h3><?= (int)($tidak_hadir ?? 0) ?></h3></div></div>
        </div>
      </div>
      <div class="map-container mt-4">
        <h2 class="map-title">DENAH TEMPAT DUDUK – <?= e($sesi['nama_sesi']) ?></h2>
        <?php if (empty($rows)): ?>
          <div class="alert alert-warning">Belum ada denah untuk sesi ini.</div>
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
                          $norm = preg_replace_callback('/\d+/', function($m){ $v = ltrim($m[0], '0'); return $v === '' ? '0' : $v; }, $norm);
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
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
