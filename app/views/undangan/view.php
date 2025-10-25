<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<style>
/* UNIKA Theme: Dominant Purple with Yellow Accents */
:root {
    --unika-purple: #6A1B9A;
    --unika-purple-dark: #4A148C;
    --unika-yellow: #FBC02D;
    --bg-soft: #faf7ff;
}

.undangan-container {
    background: linear-gradient(135deg, var(--unika-purple) 0%, var(--unika-purple-dark) 100%);
    min-height: 100vh;
    padding: 24px;
}

.undangan-card {
    max-width: 920px;
    margin: 0 auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.25);
    overflow: hidden;
}

.undangan-header {
    background: linear-gradient(135deg, var(--unika-purple), var(--unika-purple-dark));
    color: white;
    padding: 48px 24px;
    text-align: center;
    position: relative;
}

.undangan-header h2 {
    letter-spacing: 1px;
}

.undangan-header h4 {
    color: var(--unika-yellow);
    font-weight: 700;
}

.undangan-body {
    padding: 32px;
    background: var(--bg-soft);
}

.info-box {
    background: #fff;
    border-left: 6px solid var(--unika-purple);
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 8px;
}

.badge-unika {
    background-color: var(--unika-yellow);
    color: #4a4a4a;
}

.qr-code-container {
    text-align: center;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #eee;
}

/* Map iframe responsive */
.map-embed {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 */
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #eee;
}
.map-embed iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100% !important;
    height: 100% !important;
    border: 0 !important;
}

/* Buttons */
.btn-unika {
    background-color: var(--unika-purple);
    border-color: var(--unika-purple);
}
.btn-unika:hover {
    background-color: var(--unika-purple-dark);
    border-color: var(--unika-purple-dark);
}
.btn-outline-unika {
    color: var(--unika-purple);
    border-color: var(--unika-purple);
}
.btn-outline-unika:hover {
    color: #fff;
    background-color: var(--unika-purple);
    border-color: var(--unika-purple);
}
</style>

<div class="undangan-container">
    <div class="undangan-card">
        <div class="undangan-header">
            <i class="fas fa-graduation-cap fa-4x mb-3"></i>
            <h2>UNDANGAN WISUDA</h2>
            <h4><?= e($periode['nama_periode']) ?></h4>
        </div>
        
        <div class="undangan-body">
            <!-- Wisudawan Info -->
            <div class="text-center mb-4">
                <h3 style="color: var(--unika-purple); font-weight:700;"><?= e($wisudawan['nama_lengkap']) ?></h3>
                <p class="text-muted mb-0">NIM: <?= e($wisudawan['nim']) ?></p>
                <p class="text-muted"><?= e($wisudawan['program_studi']) ?></p>
            </div>
            
            <!-- Sesi Info -->
            <div class="info-box">
                <h5 style="color: var(--unika-purple)"><i class="fas fa-calendar-alt"></i> Informasi Acara</h5>
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="150">Sesi</th>
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
                    <tr>
                        <th>Nomor Kursi</th>
                        <td>: <span class="badge badge-unika fs-5"><?= e($wisudawan['nomor_kursi'] ?: 'Belum ditentukan') ?></span></td>
                    </tr>
                </table>
            </div>
            
            <!-- Map -->
            <?php if (!empty($sesi['lokasi_iframe'])): ?>
            <div class="mb-4">
                <h5 style="color: var(--unika-purple)"><i class="fas fa-map-marker-alt"></i> Lokasi</h5>
                <div class="map-embed">
                    <?= $sesi['lokasi_iframe'] ?>
                </div>
            </div>
            <?php elseif ($sesi['latitude'] && $sesi['longitude']): ?>
            <div class="mb-4">
                <h5 style="color: var(--unika-purple)"><i class="fas fa-map-marker-alt"></i> Lokasi</h5>
                <div class="map-embed">
                    <iframe src="https://maps.google.com/maps?q=<?= $sesi['latitude'] ?>,<?= $sesi['longitude'] ?>&output=embed"></iframe>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Additional Info -->
            <?php if (!empty($informasi_list)): ?>
            <div class="mb-4">
                <h5><i class="fas fa-info-circle"></i> Informasi Tambahan</h5>
                <?php foreach ($informasi_list as $info): ?>
                <div class="alert alert-info">
                    <strong><?= e($info['judul']) ?></strong>
                    <p class="mb-0"><?= nl2br(e($info['konten'])) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- QR Code -->
            <div class="qr-code-container mb-4">
                <h5>QR Code Undangan</h5>
                <img src="<?= url('undangan/qrcode/' . $wisudawan['kode_unik']) ?>" alt="QR Code" style="max-width: 200px;">
                <p class="mt-2">Kode Unik: <strong class="fs-4"><?= e($wisudawan['kode_unik']) ?></strong></p>
                <small class="text-muted">Tunjukkan QR Code ini saat presensi</small>
            </div>
            
            <!-- RSVP -->
            <div class="text-center" id="rsvp">
                <h5 class="mb-3" style="color: var(--unika-purple)">Konfirmasi Kehadiran</h5>
                
                <?php if ($wisudawan['status_rsvp'] === 'pending'): ?>
                    <p class="text-muted">Mohon konfirmasi kehadiran Anda</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-unika btn-lg text-white" onclick="confirmRSVP('confirmed')">
                            <i class="fas fa-check"></i> Saya Hadir
                        </button>
                        <button class="btn btn-outline-unika btn-lg" onclick="confirmRSVP('declined')">
                            <i class="fas fa-times"></i> Tidak Hadir
                        </button>
                    </div>
                <?php elseif ($wisudawan['status_rsvp'] === 'confirmed'): ?>
                    <div class="alert" style="background: #e8ddff; color: #2d0d52; border-left: 6px solid var(--unika-purple);">
                        <i class="fas fa-check-circle"></i> Anda telah mengkonfirmasi kehadiran
                        <br><small>Dikonfirmasi pada: <?= formatDateTimeIndo($wisudawan['rsvp_at']) ?></small>
                    </div>
                <?php else: ?>
                    <div class="alert" style="background: #fff9e3; color: #5c4b00; border-left: 6px solid var(--unika-yellow);">
                        <i class="fas fa-times-circle"></i> Anda telah mengkonfirmasi tidak hadir
                        <br><small>Dikonfirmasi pada: <?= formatDateTimeIndo($wisudawan['rsvp_at']) ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRSVP(status) {
    Swal.fire({
        title: 'Konfirmasi',
        text: status === 'confirmed' ? 'Anda akan mengkonfirmasi kehadiran?' : 'Anda yakin tidak dapat hadir?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= url('undangan/rsvp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    wisudawan_id: '<?= $wisudawan['id'] ?>',
                    periode_id: '<?= $periode['id'] ?>',
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
