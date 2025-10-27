<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-map"></i> Denah Kehadiran</h2>
                <h5 class="text-muted"><?= e($sesi['nama_sesi']) ?></h5>
            </div>
            
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6>Total Wisudawan</h6>
                            <h3><?= $stats['total'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6>Sudah Hadir</h6>
                            <h3><?= $stats['presensi_hadir'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6>Belum Hadir</h6>
                            <h3><?= ($stats['total'] ?? 0) - ($stats['presensi_hadir'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>RSVP Confirmed</h6>
                            <h3><?= $stats['rsvp_confirmed'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Denah Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Denah Kursi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>No Kursi</th>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>Program Studi</th>
                                    <th>Status</th>
                                    <th>Waktu Hadir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($denah as $d): ?>
                                <tr class="<?= $d['status_kehadiran'] === 'hadir' ? 'table-success' : '' ?>">
                                    <td><strong><?= e($d['nomor_kursi']) ?></strong></td>
                                    <td><?= e($d['nama_lengkap']) ?></td>
                                    <td><?= e($d['nim']) ?></td>
                                    <td><?= e($d['program_studi']) ?></td>
                                    <td>
                                        <?php if ($d['status_kehadiran'] === 'hadir'): ?>
                                            <span class="badge bg-success">Hadir</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $d['presensi_hadir_at'] ? formatDateTimeIndo($d['presensi_hadir_at']) : '-' ?></td>
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
