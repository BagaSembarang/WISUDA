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
                    <h2><i class="fas fa-chair"></i> Kelola Denah Periode</h2>
                    <h5 class="text-muted"><?= e($periode['nama_periode']) ?></h5>
                </div>
                <div>
                    <a href="<?= url('admin/viewPeriode/' . $periode['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-file-excel"></i> Impor Denah (Per Periode)</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="mb-1">Isi sel Excel sesuai nomor kursi pada posisi grid yang diinginkan.</div>
                                <div>Jumlah baris dan kolom mengikuti ukuran sheet yang berisi data.</div>
                            </div>
                            <form method="POST" action="<?= url('sesi/manageDenah/' . $periode['id']) ?>" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">File Excel</label>
                                    <input type="file" name="denah_excel" class="form-control" accept=".xls,.xlsx" required>
                                </div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-upload"></i> Unggah dan Terapkan ke Semua Sesi
                                </button>
                            </form>

                            <hr class="my-4">
                            <div class="d-flex align-items-end gap-2">
                                <form class="row g-2" method="GET" action="<?= BASE_URL . 'index.php' ?>">
                                    <input type="hidden" name="url" value="sesi/downloadDenahTemplate/<?= $periode['id'] ?>">
                                    <div class="col-auto">
                                        <label class="form-label">Baris</label>
                                        <input type="number" name="rows" class="form-control" value="20" min="1">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">Kolom</label>
                                        <input type="number" name="cols" class="form-control" value="12" min="1">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">Jenis Template</label>
                                        <select name="type" class="form-select">
                                            <option value="blank" selected>Blank</option>
                                            <option value="numeric">Numeric</option>
                                            <option value="vip">VIP + Numeric</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">Mulai Nomor</label>
                                        <input type="number" name="start" class="form-control" value="1" min="0">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">Padding</label>
                                        <input type="number" name="pad" class="form-control" value="3" min="1" max="6">
                                    </div>
                                    <div class="col-auto d-flex align-items-end">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fas fa-download"></i> Download Template Denah
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-layer-group"></i> Preview Denah per Sesi</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($sesi_list)): ?>
                                <div class="alert alert-warning">Belum ada sesi pada periode ini.</div>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($sesi_list as $s): ?>
                                        <a class="btn btn-outline-primary" href="<?= url('sesi/previewDenah/' . $periode['id'] . '/' . $s['id']) ?>">
                                            <i class="fas fa-eye"></i> <?= e($s['nama_sesi']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
