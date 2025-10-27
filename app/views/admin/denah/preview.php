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

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Denah Kursi</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($rows)): ?>
                        <div class="alert alert-warning">Belum ada denah untuk sesi ini. Silakan impor denah pada halaman sebelumnya.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle" style="min-width: 700px;">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">Baris</th>
                                        <?php for ($c=1; $c <= $max_col; $c++): ?>
                                            <th style="width: 60px;"><?= $c ?></th>
                                        <?php endfor; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $r): ?>
                                        <tr>
                                            <th><?= $r ?></th>
                                            <?php for ($c=1; $c <= $max_col; $c++): ?>
                                                <?php $key = $r . '-' . $c; $label = $grid[$key] ?? ''; $isOcc = ($label !== '' && isset($occupied[$label])); ?>
                                                <td class="<?= $label!=='' ? ($isOcc ? 'table-danger' : 'table-success') : '' ?>" style="height:40px;">
                                                    <?= $label !== '' ? e($label) : '&nbsp;' ?>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex gap-3">
                            <span class="badge bg-success">Nomor tersedia</span>
                            <span class="badge bg-danger">Nomor terpakai</span>
                            <span class="badge bg-secondary">Kosong</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
