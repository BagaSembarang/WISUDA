<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-eye"></i> Detail Periode Wisuda</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('admin/periode') ?>">Periode</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            
            <!-- Periode Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Periode</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Nama Periode</th>
                                    <td>: <?= e($periode['nama_periode']) ?></td>
                                </tr>
                                <tr>
                                    <th>Tahun</th>
                                    <td>: <?= e($periode['tahun']) ?></td>
                                </tr>
                                <tr>
                                    <th>Periode Ke</th>
                                    <td>: <?= e($periode['periode_ke']) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>: 
                                        <?php if ($periode['status'] === 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($periode['status'] === 'selesai'): ?>
                                            <span class="badge bg-secondary">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Table Prefix</th>
                                    <td>: <code><?= e($periode['table_prefix']) ?></code></td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>: <?= e($periode['keterangan'] ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>: <?= formatDateTimeIndo($periode['created_at']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sesi List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Sesi Wisuda</h5>
                    <div class="d-flex gap-2">
                        <a href="<?= url('sesi/manageDenah/' . $periode['id']) ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-chair"></i> Kelola Denah
                        </a>
                        <a href="<?= url('sesi/create/' . $periode['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Sesi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($sesi_list)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada sesi wisuda. Silakan tambah sesi terlebih dahulu.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Sesi</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Total Wisudawan</th>
                                        <th>RSVP Confirmed</th>
                                        <th>Hadir</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($sesi_list as $sesi): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= e($sesi['nama_sesi']) ?></td>
                                        <td><?= formatDateIndo($sesi['tanggal']) ?></td>
                                        <td><?= date('H:i', strtotime($sesi['waktu_mulai'])) ?> - <?= date('H:i', strtotime($sesi['waktu_selesai'])) ?></td>
                                        <td><?= e($sesi['lokasi']) ?></td>
                                        <td><?= $sesi['total_wisudawan'] ?? 0 ?></td>
                                        <td><?= $sesi['confirmed_count'] ?? 0 ?></td>
                                        <td><?= $sesi['hadir_count'] ?? 0 ?></td>
                                        <td>
                                            <a href="<?= url('wisudawan/index/' . $sesi['id']) ?>" class="btn btn-sm btn-primary" title="Data Wisudawan">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <a href="<?= url('wisudawan/upload/' . $sesi['id']) ?>" class="btn btn-sm btn-success" title="Upload Excel">
                                                <i class="fas fa-upload"></i>
                                            </a>
                                            <a href="<?= url('sesi/edit/' . $sesi['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= url('lo/denah/' . $sesi['id']) ?>" class="btn btn-sm btn-info" title="Denah">
                                                <i class="fas fa-map"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
