<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-2 p-0">
      <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
    </div>
    <div class="col-md-10 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-users"></i> Manajemen Pengguna</h2>
        <a href="<?= url('user/create') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah User</a>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped datatable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Aktif</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['full_name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge <?= $u['role']==='admin'?'bg-primary':'bg-success' ?>"><?= e($u['role']) ?></span></td>
                    <td><?= $u['is_active'] ? 'Ya' : 'Tidak' ?></td>
                    <td>
                      <a href="<?= url('user/edit/' . $u['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                      <button class="btn btn-sm btn-danger" onclick="delUser(<?= $u['id'] ?>)"><i class="fas fa-trash"></i></button>
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
<script>
function delUser(id){
  if(!confirm('Hapus user ini?')) return;
  fetch('<?= url('user/delete/') ?>'+id, {method:'POST'}).then(r=>r.json()).then(j=>{
    alert(j.message||'OK'); location.reload();
  }).catch(()=>alert('Gagal'))
}
</script>
<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
