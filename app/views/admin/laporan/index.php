<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-file-alt"></i> Laporan</h2>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pilih Periode & Sesi</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Periode Wisuda</label>
                            <select id="periode_id" class="form-select">
                                <option value="">-- Pilih Periode --</option>
                                <?php foreach ($periodes as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['nama_periode']) ?> (<?= e($p['tahun']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sesi</label>
                            <select id="sesi_id" class="form-select" disabled>
                                <option value="">-- Pilih Sesi --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid gap-2 d-md-flex">
                                <a id="btn_view_presensi" href="#" class="btn btn-primary disabled">
                                    <i class="fas fa-clipboard-list"></i> Lihat Presensi
                                </a>
                                <a id="btn_view_toga" href="#" class="btn btn-secondary disabled">
                                    <i class="fas fa-tshirt"></i> Lihat Toga
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="d-grid gap-2 d-md-flex">
                                <a id="btn_export_presensi" href="#" class="btn btn-success disabled">
                                    <i class="fas fa-file-pdf"></i> Export PDF Presensi
                                </a>
                                <a id="btn_export_toga" href="#" class="btn btn-success disabled">
                                    <i class="fas fa-file-pdf"></i> Export PDF Toga
                                </a>
                                <a id="btn_kupon" href="#" class="btn btn-warning disabled">
                                    <i class="fas fa-ticket-alt"></i> Print Kupon
                                </a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            
            <div id="info" class="alert alert-info">
                Pilih periode terlebih dahulu untuk memuat daftar sesi.
            </div>
        </div>
    </div>
</div>

<script>
const periodeSelect = document.getElementById('periode_id');
const sesiSelect = document.getElementById('sesi_id');
const btnViewPresensi = document.getElementById('btn_view_presensi');
const btnViewToga = document.getElementById('btn_view_toga');
const btnExportPresensi = document.getElementById('btn_export_presensi');
const btnExportToga = document.getElementById('btn_export_toga');
const btnKupon = document.getElementById('btn_kupon');

periodeSelect.addEventListener('change', async () => {
    const periodeId = periodeSelect.value;
    sesiSelect.innerHTML = '<option value="">-- Pilih Sesi --</option>';
    sesiSelect.disabled = true;
    btnViewPresensi.classList.add('disabled');
    btnViewToga.classList.add('disabled');
    btnExportPresensi.classList.add('disabled');
    btnExportToga.classList.add('disabled');
    btnKupon.classList.add('disabled');

    if (!periodeId) return;

    try {
        const res = await fetch('<?= url('laporan/sesiByPeriode/') ?>' + periodeId);
        const json = await res.json();
        if (json.success) {
            json.data.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = `${s.nama_sesi} - ${s.tanggal} (Total: ${s.total_wisudawan || 0}, Hadir: ${s.hadir_count || 0})`;
                sesiSelect.appendChild(opt);
            });
            sesiSelect.disabled = false;
            document.getElementById('info').classList.add('d-none');
        } else {
            Swal.fire('Error', json.message || 'Gagal memuat sesi', 'error');
        }
    } catch (e) {
        Swal.fire('Error', e.message, 'error');
    }
});

sesiSelect.addEventListener('change', () => {
    const sesiId = sesiSelect.value;
    if (!sesiId) {
        btnViewPresensi.classList.add('disabled');
        btnViewToga.classList.add('disabled');
        btnExportPresensi.classList.add('disabled');
        btnExportToga.classList.add('disabled');
        btnKupon.classList.add('disabled');
        btnViewPresensi.href = '#';
        btnViewToga.href = '#';
        btnExportPresensi.href = '#';
        btnExportToga.href = '#';
        btnKupon.href = '#';
        return;
    }
    btnViewPresensi.classList.remove('disabled');
    btnViewToga.classList.remove('disabled');
    btnExportPresensi.classList.remove('disabled');
    btnExportToga.classList.remove('disabled');
    btnKupon.classList.remove('disabled');

    btnViewPresensi.href = '<?= url('laporan/presensi/') ?>' + sesiId;
    btnViewToga.href = '<?= url('laporan/toga/') ?>' + sesiId;
    btnExportPresensi.href = '<?= url('laporan/exportPDF/') ?>' + sesiId + '/presensi';
    btnExportToga.href = '<?= url('laporan/exportPDF/') ?>' + sesiId + '/toga';
    btnKupon.href = '<?= url('laporan/printKupon/') ?>' + sesiId;
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
