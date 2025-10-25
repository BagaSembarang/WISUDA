<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-0">Edit Wisudawan</h2>
                    <div class="text-muted"><?= e($wisudawan['nama_lengkap']) ?></div>
                </div>
                <a href="<?= url('wisudawan/detail/' . $wisudawan['id'] . '/' . $periode_id) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Perbaikan Data</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= url('wisudawan/edit/' . $wisudawan['id'] . '/' . $periode_id) ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="nim">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim" value="<?= e($wisudawan['nim']) ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= e($wisudawan['nama_lengkap']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="program_studi">Program Studi</label>
                                <input type="text" class="form-control" id="program_studi" name="program_studi" value="<?= e($wisudawan['program_studi']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="fakultas">Fakultas</label>
                                <input type="text" class="form-control" id="fakultas" name="fakultas" value="<?= e($wisudawan['fakultas']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="ipk">IPK</label>
                                <input type="text" class="form-control" id="ipk" name="ipk" value="<?= e($wisudawan['ipk']) ?>" placeholder="3.50">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="predikat">Predikat</label>
                                <input type="text" class="form-control" id="predikat" name="predikat" value="<?= e($wisudawan['predikat']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= e($wisudawan['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="no_hp">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= e($wisudawan['no_hp']) ?>" placeholder="0812xxxxxxx">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="ukuran_toga">Ukuran Toga</label>
                                <select class="form-select" id="ukuran_toga" name="ukuran_toga">
                                    <?php $sizes = ['S','M','L','XL','XXL']; foreach ($sizes as $s): ?>
                                        <option value="<?= $s ?>" <?= ($wisudawan['ukuran_toga'] === $s ? 'selected' : '') ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="nomor_kursi">Nomor Kursi</label>
                                <input type="text" class="form-control" id="nomor_kursi" name="nomor_kursi" value="<?= e($wisudawan['nomor_kursi']) ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="<?= url('wisudawan/detail/' . $wisudawan['id'] . '/' . $periode_id) ?>" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
