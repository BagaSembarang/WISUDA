<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-0">Detail Wisudawan</h2>
                    <div class="text-muted"><?= e($wisudawan['nama_lengkap']) ?></div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= url('wisudawan/edit/' . $wisudawan['id'] . '/' . $sesi['periode_id']) ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Data
                    </a>
                    <a href="<?= url('wisudawan/index/' . $sesi['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="<?= BASE_URL . '?i=' . e($wisudawan['kode_unik']) ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Buka Undangan
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Data Utama</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th width="220">NIM</th>
                                            <td>: <?= e($wisudawan['nim']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nama</th>
                                            <td>: <?= e($wisudawan['nama_lengkap']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Program Studi</th>
                                            <td>: <?= e($wisudawan['program_studi']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Fakultas</th>
                                            <td>: <?= e($wisudawan['fakultas']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>IPK</th>
                                            <td>: <?= e($wisudawan['ipk']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Predikat</th>
                                            <td>: <?= e($wisudawan['predikat']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>: <?= e($wisudawan['email']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>: <?= e($wisudawan['no_hp']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Ukuran Toga</th>
                                            <td>: <?= e($wisudawan['ukuran_toga']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Kursi</th>
                                            <td>: <?= e($wisudawan['nomor_kursi'] ?: '-') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi RSVP & Presensi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th width="220">Kode Unik</th>
                                            <td>: <code><?= e($wisudawan['kode_unik']) ?></code></td>
                                        </tr>
                                        <tr>
                                            <th>Status RSVP</th>
                                            <td>:
                                                <?php if ($wisudawan['status_rsvp'] === 'confirmed'): ?>
                                                    <span class="badge bg-success">Confirmed</span>
                                                <?php elseif ($wisudawan['status_rsvp'] === 'declined'): ?>
                                                    <span class="badge bg-danger">Declined</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Waktu RSVP</th>
                                            <td>: <?= !empty($wisudawan['rsvp_at']) ? formatDateTimeIndo($wisudawan['rsvp_at']) : '-' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Presensi Toga</th>
                                            <td>: <?= !empty($wisudawan['presensi_toga_at']) ? formatDateTimeIndo($wisudawan['presensi_toga_at']) : '-' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Presensi Gladi</th>
                                            <td>: <?= !empty($wisudawan['presensi_gladi_at']) ? formatDateTimeIndo($wisudawan['presensi_gladi_at']) : '-' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Presensi Hadir</th>
                                            <td>: <?= !empty($wisudawan['presensi_hadir_at']) ? formatDateTimeIndo($wisudawan['presensi_hadir_at']) : '-' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Presensi Konsumsi</th>
                                            <td>: <?= !empty($wisudawan['presensi_konsumsi_at']) ? formatDateTimeIndo($wisudawan['presensi_konsumsi_at']) : '-' ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">QR Code Undangan</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?= url('undangan/qrcode/' . $wisudawan['kode_unik']) ?>" alt="QR Code" style="max-width: 220px;">
                            <div class="mt-2">Kode: <strong><?= e($wisudawan['kode_unik']) ?></strong></div>
                            <div class="mt-2"><a href="<?= BASE_URL . '?i=' . e($wisudawan['kode_unik']) ?>" target="_blank"><?= BASE_URL . '?i=' . e($wisudawan['kode_unik']) ?></a></div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Sesi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th width="140">Sesi</th>
                                            <td>: <?= e($sesi['nama_sesi']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>: <?= formatDateIndo($sesi['tanggal']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Waktu</th>
                                            <td>: <?= date('H:i', strtotime($sesi['waktu_mulai'])) ?> - <?= date('H:i', strtotime($sesi['waktu_selesai'])) ?> WIB</td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi</th>
                                            <td>: <?= e($sesi['lokasi']) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
