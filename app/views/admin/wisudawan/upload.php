<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-upload"></i> Upload Data Wisudawan</h2>
                <h5 class="text-muted"><?= e($sesi['nama_sesi']) ?></h5>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Upload File Excel</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Format Excel:</h6>
                                <p class="mb-0">Kolom yang diperlukan (urutan):</p>
                                <ol class="mb-0">
                                    <li>NIM</li>
                                    <li>Nama Lengkap</li>
                                    <li>Program Studi</li>
                                    <li>Fakultas</li>
                                    <li>IPK</li>
                                    <li>Predikat</li>
                                    <li>Email</li>
                                    <li>No HP</li>
                                    <li>Ukuran Toga (S/M/L/XL/XXL)</li>
                                    <li>Nomor Kursi</li>
                                </ol>
                                <p class="mt-2 mb-0"><strong>Catatan:</strong> Baris pertama adalah header, akan di-skip</p>
                            </div>
                            
                            <form method="POST" action="<?= url('wisudawan/upload/' . $sesi['id']) ?>" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">File Excel <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                                    <small class="text-muted">Format: .xls atau .xlsx, Max: 5MB</small>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="<?= url('wisudawan/index/' . $sesi['id']) ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Download Template</h5>
                        </div>
                        <div class="card-body">
                            <p>Untuk memudahkan, Anda dapat download template Excel berikut:</p>
                            <a href="<?= asset('templates/template_wisudawan.xlsx') ?>" class="btn btn-success">
                                <i class="fas fa-download"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
