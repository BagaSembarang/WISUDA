<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-plus"></i> Tambah Periode Wisuda</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('admin/periode') ?>">Periode</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Form Tambah Periode</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= url('admin/createPeriode') ?>">
                                <div class="mb-3">
                                    <label for="nama_periode" class="form-label">Nama Periode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_periode" name="nama_periode" required placeholder="Contoh: Wisuda Periode III Tahun 2025">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="tahun" name="tahun" required min="2020" max="2099" value="<?= date('Y') ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="periode_ke" class="form-label">Periode Ke <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="periode_ke" name="periode_ke" required min="1" max="12" value="1">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Informasi:</strong> Tabel wisudawan akan dibuat otomatis dengan format: <code>{tahun}_{periode_ke}_t_wisudawan</code>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="<?= url('admin/periode') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
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
