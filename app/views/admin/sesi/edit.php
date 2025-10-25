<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-edit"></i> Edit Sesi Wisuda</h2>
                <h5 class="text-muted"><?= e($sesi['nama_periode']) ?></h5>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Form Edit Sesi</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= url('sesi/edit/' . $sesi['id']) ?>">
                                <div class="mb-3">
                                    <label for="nama_sesi" class="form-label">Nama Sesi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_sesi" name="nama_sesi" required value="<?= e($sesi['nama_sesi']) ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= e($sesi['tanggal']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="waktu_mulai" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" required value="<?= e(substr($sesi['waktu_mulai'],0,5)) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="waktu_selesai" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" required value="<?= e(substr($sesi['waktu_selesai'],0,5)) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">Lokasi</label>
                                    <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?= e($sesi['lokasi']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="lokasi_iframe" class="form-label">Embed Map (Iframe Google Maps)</label>
                                    <textarea class="form-control" id="lokasi_iframe" name="lokasi_iframe" rows="4" placeholder="Paste kode iframe Google Maps di sini"><?= e($sesi['lokasi_iframe'] ?? '') ?></textarea>
                                    <div class="form-text">Contoh: <code>&lt;iframe ...&gt;&lt;/iframe&gt;</code></div>
                                </div>
                                
                                
                                
                                <div class="mb-3">
                                    <label for="kapasitas" class="form-label">Kapasitas</label>
                                    <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="0" value="<?= e((int)$sesi['kapasitas']) ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="informasi_tambahan" class="form-label">Informasi Tambahan</label>
                                    <textarea class="form-control" id="informasi_tambahan" name="informasi_tambahan" rows="3"><?= e($sesi['informasi_tambahan']) ?></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="<?= url('admin/viewPeriode/' . $sesi['periode_id']) ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
