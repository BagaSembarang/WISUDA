<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-qrcode"></i> Presensi Wisudawan</h2>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pilih Periode dan Jenis Presensi</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?= url('presensi/scan') ?>">
                                <div class="mb-3">
                                    <label for="periode_id" class="form-label">Periode Wisuda <span class="text-danger">*</span></label>
                                    <select class="form-select" id="periode_id" name="periode_id" required>
                                        <option value="">-- Pilih Periode --</option>
                                        <?php foreach ($periodes as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= e($p['nama_periode']) ?> (<?= e($p['tahun']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Jenis Presensi <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card border-primary h-100">
                                                <div class="card-body text-center">
                                                    <input type="radio" class="btn-check" name="type" id="type_toga" value="toga" required>
                                                    <label class="w-100" for="type_toga">
                                                        <i class="fas fa-user-graduate fa-3x text-primary mb-2"></i>
                                                        <h5>Pengambilan Toga</h5>
                                                        <p class="text-muted small">Presensi + TTD + Ukuran Toga</p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card border-info h-100">
                                                <div class="card-body text-center">
                                                    <input type="radio" class="btn-check" name="type" id="type_gladi" value="gladi">
                                                    <label class="w-100" for="type_gladi">
                                                        <i class="fas fa-clipboard-check fa-3x text-info mb-2"></i>
                                                        <h5>Gladi Bersih</h5>
                                                        <p class="text-muted small">Presensi kehadiran gladi</p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card border-success h-100">
                                                <div class="card-body text-center">
                                                    <input type="radio" class="btn-check" name="type" id="type_hadir" value="hadir">
                                                    <label class="w-100" for="type_hadir">
                                                        <i class="fas fa-certificate fa-3x text-success mb-2"></i>
                                                        <h5>Hari-H (Ijazah)</h5>
                                                        <p class="text-muted small">Presensi + TTD Ijazah</p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card border-warning h-100">
                                                <div class="card-body text-center">
                                                    <input type="radio" class="btn-check" name="type" id="type_konsumsi" value="konsumsi">
                                                    <label class="w-100" for="type_konsumsi">
                                                        <i class="fas fa-utensils fa-3x text-warning mb-2"></i>
                                                        <h5>Konsumsi</h5>
                                                        <p class="text-muted small">Presensi pengambilan konsumsi</p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-right"></i> Lanjutkan ke Scanner
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card.border-primary:has(input:checked),
.card.border-info:has(input:checked),
.card.border-success:has(input:checked),
.card.border-warning:has(input:checked) {
    background-color: rgba(0, 123, 255, 0.1);
    border-width: 2px !important;
}

.card label {
    cursor: pointer;
}
</style>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
