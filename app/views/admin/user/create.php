<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-2 p-0">
      <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-10 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-user-plus"></i> Tambah Pengguna</h2>
        <a href="<?= url('user/index') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
      </div>
      <div class="card">
        <div class="card-body">
          <form method="POST" action="<?= url('user/create') ?>">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                  <option value="lo">LO</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="activeCheck">
                  <label class="form-check-label" for="activeCheck">Aktif</label>
                </div>
              </div>
            </div>
            <div class="mt-3">
              <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
