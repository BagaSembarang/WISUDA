<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-edit"></i> Edit Periode Wisuda</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('admin/periode') ?>">Periode</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Form Edit Periode</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= url('admin/editPeriode/' . $periode['id']) ?>">
                                <div class="mb-3">
                                    <label for="nama_periode" class="form-label">Nama Periode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_periode" name="nama_periode" required value="<?= e($periode['nama_periode']) ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tahun</label>
                                            <input type="text" class="form-control" value="<?= e($periode['tahun']) ?>" disabled>
                                            <small class="text-muted">Tahun tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Periode Ke</label>
                                            <input type="text" class="form-control" value="<?= e($periode['periode_ke']) ?>" disabled>
                                            <small class="text-muted">Periode ke tidak dapat diubah</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="draft" <?= $periode['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="aktif" <?= $periode['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="selesai" <?= $periode['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= e($periode['keterangan']) ?></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="<?= url('admin/periode') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update
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
