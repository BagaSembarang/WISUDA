<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-0"><i class="fas fa-tshirt"></i> Laporan Pengambilan Toga</h2>
                    <div class="text-muted">Periode: <?= e($sesi['nama_periode']) ?> | Sesi: <?= e($sesi['nama_sesi']) ?> (<?= e($sesi['tanggal']) ?>)</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= url('laporan/exportPDF/' . $sesi['id'] . '/toga') ?>" class="btn btn-success">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <a href="<?= url('laporan/exportPDF/' . $sesi['id'] . '/presensi') ?>" class="btn btn-secondary">
                        <i class="fas fa-clipboard-list"></i> Laporan Presensi
                    </a>
                </div>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="text-uppercase small">Total Wisudawan</div>
                            <div class="fs-3 fw-bold">&nbsp;<?= $stats['total'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="text-uppercase small">Sudah Ambil Toga</div>
                            <div class="fs-3 fw-bold">&nbsp;<?= $stats['presensi_toga'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="text-uppercase small">Belum Ambil</div>
                            <div class="fs-3 fw-bold">&nbsp;<?= ($stats['total'] ?? 0) - ($stats['presensi_toga'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Wisudawan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Program Studi</th>
                                    <th>Ukuran Toga</th>
                                    <th>Ambil Toga</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($wisudawan_list as $w): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= e($w['nim']) ?></td>
                                    <td><?= e($w['nama_lengkap']) ?></td>
                                    <td><?= e($w['program_studi']) ?></td>
                                    <td><?= e($w['ukuran_toga']) ?></td>
                                    <td><?= $w['presensi_toga_at'] ? '<span class="badge bg-success">Sudah</span>' : '<span class="badge bg-secondary">Belum</span>' ?></td>
                                    <td><?= e($w['keterangan_toga'] ?: '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
