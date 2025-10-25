<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-users"></i> Data Wisudawan</h2>
                <h5 class="text-muted"><?= e($sesi['nama_sesi']) ?></h5>
            </div>
            
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6>Total</h6>
                            <h3><?= $stats['total'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6>RSVP</h6>
                            <h3><?= $stats['rsvp_confirmed'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6>Toga</h6>
                            <h3><?= $stats['presensi_toga'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6>Gladi</h6>
                            <h3><?= $stats['presensi_gladi'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6>Hadir</h6>
                            <h3><?= $stats['presensi_hadir'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h6>Konsumsi</h6>
                            <h3><?= $stats['presensi_konsumsi'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Wisudawan List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Wisudawan</h5>
                    <div class="d-flex gap-2">
                        <a href="<?= url('wisudawan/upload/' . $sesi['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload"></i> Upload Excel
                        </a>
                        <a href="<?= url('wisudawan/export/' . $sesi['id']) ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Kursi</th>
                                    <th>RSVP</th>
                                    <th>Presensi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($wisudawan_list as $w): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= e($w['kode_unik']) ?></code></td>
                                    <td><?= e($w['nim']) ?></td>
                                    <td><?= e($w['nama_lengkap']) ?></td>
                                    <td><?= e($w['program_studi']) ?></td>
                                    <td><?= e($w['nomor_kursi'] ?: '-') ?></td>
                                    <td>
                                        <?php if ($w['status_rsvp'] === 'confirmed'): ?>
                                            <span class="badge bg-success">Confirmed</span>
                                        <?php elseif ($w['status_rsvp'] === 'declined'): ?>
                                            <span class="badge bg-danger">Declined</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($w['presensi_hadir_at']): ?>
                                            <span class="badge bg-success">Hadir</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('wisudawan/detail/' . $w['id'] . '/' . $sesi['periode_id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
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
