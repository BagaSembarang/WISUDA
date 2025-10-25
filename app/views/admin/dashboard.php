<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h2>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Total Periode</h6>
                                    <h3 class="mb-0"><?= count($periodes) ?></h3>
                                </div>
                                <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Periode Aktif</h6>
                                    <h3 class="mb-0">
                                        <?= count(array_filter($periodes, fn($p) => $p['status'] === 'aktif')) ?>
                                    </h3>
                                </div>
                                <i class="fas fa-check-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Total Sesi</h6>
                                    <h3 class="mb-0">
                                        <?= array_sum(array_column($periodes, 'jumlah_sesi')) ?>
                                    </h3>
                                </div>
                                <i class="fas fa-list fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">Selamat Datang</h6>
                                    <h6 class="mb-0"><?= e($_SESSION['full_name']) ?></h6>
                                </div>
                                <i class="fas fa-user fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Periode List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Daftar Periode Wisuda</h5>
                    <a href="<?= url('admin/createPeriode') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Periode
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Periode</th>
                                    <th>Tahun</th>
                                    <th>Periode Ke</th>
                                    <th>Jumlah Sesi</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
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
                                    <td><?= e($periode['created_by_name'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= url('admin/viewPeriode/' . $periode['id']) ?>" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('admin/editPeriode/' . $periode['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Aktivitas Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_logs as $log): ?>
                                <tr>
                                    <td><?= formatDateTimeIndo($log['created_at']) ?></td>
                                    <td><?= e($log['full_name'] ?? 'System') ?></td>
                                    <td><span class="badge bg-info"><?= e($log['action']) ?></span></td>
                                    <td><?= e($log['description']) ?></td>
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
