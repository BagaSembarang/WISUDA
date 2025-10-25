<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-alt"></i> Manajemen Periode Wisuda</h2>
                <a href="<?= url('admin/createPeriode') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Periode
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Periode</th>
                                    <th>Tahun</th>
                                    <th>Periode Ke</th>
                                    <th>Table Prefix</th>
                                    <th>Jumlah Sesi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($periodes as $periode): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= e($periode['nama_periode']) ?></td>
                                    <td><?= e($periode['tahun']) ?></td>
                                    <td><?= e($periode['periode_ke']) ?></td>
                                    <td><code><?= e($periode['table_prefix']) ?></code></td>
                                    <td><?= e($periode['jumlah_sesi']) ?></td>
                                    <td>
                                        <?php if ($periode['status'] === 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($periode['status'] === 'selesai'): ?>
                                            <span class="badge bg-secondary">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('admin/viewPeriode/' . $periode['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('admin/editPeriode/' . $periode['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete('<?= url('admin/deletePeriode/' . $periode['id']) ?>')" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
