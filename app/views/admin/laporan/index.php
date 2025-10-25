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
                                <a id="btn_view" href="#" class="btn btn-primary disabled">
                                    <i class="fas fa-eye"></i> Lihat Laporan
                                </a>
                                <a id="btn_export" href="#" class="btn btn-success disabled">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                                <a id="btn_kupon" href="#" class="btn btn-warning disabled">
                                    <i class="fas fa-ticket-alt"></i> Print Kupon
                                </a>
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
const btnView = document.getElementById('btn_view');
const btnExport = document.getElementById('btn_export');
const btnKupon = document.getElementById('btn_kupon');

periodeSelect.addEventListener('change', async () => {
    const periodeId = periodeSelect.value;
    sesiSelect.innerHTML = '<option value="">-- Pilih Sesi --</option>';
    sesiSelect.disabled = true;
    btnView.classList.add('disabled');
    btnExport.classList.add('disabled');
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
        btnView.classList.add('disabled');
        btnExport.classList.add('disabled');
        btnKupon.classList.add('disabled');
        btnView.href = '#';
        btnExport.href = '#';
        btnKupon.href = '#';
        return;
    }
    btnView.classList.remove('disabled');
    btnExport.classList.remove('disabled');
    btnKupon.classList.remove('disabled');

    btnView.href = '<?= url('laporan/presensi/') ?>' + sesiId;
    btnExport.href = '<?= url('laporan/exportPDF/') ?>' + sesiId + '/presensi';
    btnKupon.href = '<?= url('laporan/printKupon/') ?>' + sesiId;
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
