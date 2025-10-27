<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-2 p-0">
      <?php require_once APP_PATH . '/views/layouts/sidebar_lo.php'; ?>
    </div>
    <div class="col-md-10 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h2><i class="fas fa-chart-bar"></i> Ringkasan Sesi</h2>
          <h5 class="text-muted"><?= e($periode['nama_periode']) ?></h5>
        </div>
        <a href="<?= url('lo/dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Pilih Periode</a>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <?php if (empty($sesi_list)): ?>
            <div class="alert alert-warning">Belum ada sesi pada periode ini.</div>
          <?php else: ?>
            <div class="d-flex flex-wrap gap-2">
              <?php foreach ($sesi_list as $s): ?>
                <a href="<?= url('lo/overview/' . $periode['id'] . '/' . $s['id']) ?>" class="btn <?= ($sesi && $sesi['id']==$s['id']) ? 'btn-primary' : 'btn-outline-primary' ?>">
                  <i class="fas fa-door-open"></i> <?= e($s['nama_sesi']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($sesi): ?>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card bg-primary text-white"><div class="card-body"><h6>Total Wisudawan</h6><h3><?= (int)($stats['total'] ?? 0) ?></h3></div></div>
        </div>
        <div class="col-md-4">
          <div class="card bg-success text-white"><div class="card-body"><h6>Sudah Hadir</h6><h3><?= (int)($stats['presensi_hadir'] ?? 0) ?></h3></div></div>
        </div>
        <div class="col-md-4">
          <div class="card bg-danger text-white"><div class="card-body"><h6>Tidak Hadir</h6><h3><?= (int)($tidak_hadir ?? 0) ?></h3></div></div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header text-center">
          <h5 class="mb-0">DENAH TEMPAT DUDUK – <?= e($sesi['nama_sesi']) ?></h5>
        </div>
        <div class="card-body">
          <?php if (empty($rows)): ?>
            <div class="alert alert-warning">Belum ada denah untuk sesi ini. Silakan impor denah pada admin.</div>
          <?php else: ?>
            <div class="bg-success text-white text-center fw-semibold rounded mb-3">SEKTOR</div>
            <div class="table-responsive">
              <table class="table table-bordered text-center align-middle" style="min-width: 700px;">
                <thead>
                  <tr>
                    <th style="width:60px">Baris</th>
                    <?php for ($c=1; $c <= $max_col; $c++): ?>
                      <th style="width:60px"><?= $c ?></th>
                    <?php endfor; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($rows as $r): ?>
                    <tr>
                      <th><?= $r ?></th>
                      <?php for ($c=1; $c <= $max_col; $c++): ?>
                        <?php $key = $r . '-' . $c; $label = $grid[$key] ?? ''; $isOcc = ($label !== '' && isset($occupied[$label])); ?>
                        <td class="<?= $label!=='' ? ($isOcc ? 'table-danger' : 'table-success') : '' ?>" style="height:38px;">
                          <?= $label !== '' ? e($label) : '&nbsp;' ?>
                        </td>
                      <?php endfor; ?>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="mt-3 d-flex gap-3 justify-content-center">
              <span class="badge bg-success">Kursi Tersedia</span>
              <span class="badge bg-danger">Kursi Terisi</span>
              <span class="badge bg-secondary">Kosong</span>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
