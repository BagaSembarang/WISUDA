<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_lo.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard LO</h2>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pilih Periode untuk Melihat Denah</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($periodes as $periode): ?>
                        <a href="<?= url('lo/overview/' . $periode['id']) ?>" class="btn btn-primary mb-2">
                            <?= e($periode['nama_periode']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
