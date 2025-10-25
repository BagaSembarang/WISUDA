<?php require_once APP_PATH . '/views/layouts/header.php'; ?>
<?php require_once APP_PATH . '/views/layouts/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0">
            <?php require_once APP_PATH . '/views/layouts/sidebar_admin.php'; ?>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2><i class="fas fa-qrcode"></i> Scan QR Code - <?= ucfirst($type) ?></h2>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Scanner QR Code</h5>
                        </div>
                        <div class="card-body">
                            <div id="reader" style="width: 100%;"></div>
                            <div class="mt-3">
                                <button id="startScan" class="btn btn-primary w-100">
                                    <i class="fas fa-camera"></i> Mulai Scan
                                </button>
                                <button id="stopScan" class="btn btn-danger w-100 d-none">
                                    <i class="fas fa-stop"></i> Stop Scan
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <label class="form-label">Atau Masukkan Kode Manual</label>
                                <div class="input-group">
                                    <input type="text" id="manualCode" class="form-control" placeholder="Masukkan 4 digit kode">
                                    <button class="btn btn-primary" onclick="processManualCode()">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Data Wisudawan</h5>
                        </div>
                        <div class="card-body">
                            <div id="wisudawanInfo" class="d-none">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">NIM</th>
                                        <td>: <span id="nim"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>: <span id="nama"></span></td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>: <span id="prodi"></span></td>
                                    </tr>
                                    <?php if ($type === 'toga'): ?>
                                    <tr>
                                        <th>Ukuran Toga</th>
                                        <td>: <span id="ukuran_toga" class="badge bg-info"></span></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Nomor Kursi</th>
                                        <td>: <span id="nomor_kursi" class="badge bg-primary"></span></td>
                                    </tr>
                                </table>
                                
                                <div id="alreadyPresent" class="alert alert-warning d-none">
                                    <i class="fas fa-exclamation-triangle"></i> <span id="alreadyPresentMsg"></span>
                                </div>
                                
                                <div id="presensiForm">
                                    <?php if ($type === 'toga' || $type === 'hadir'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Tanda Tangan <span class="text-danger">*</span></label>
                                        <canvas id="signaturePad" class="signature-pad" width="500" height="200"></canvas>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="clearSignature()">
                                                <i class="fas fa-eraser"></i> Reset TTD
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan (Opsional)</label>
                                        <input type="text" id="keterangan" class="form-control" placeholder="Masukkan keterangan jika ada">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <button type="button" class="btn btn-success w-100" onclick="submitPresensi()">
                                        <i class="fas fa-check"></i> Submit Presensi
                                    </button>
                                </div>
                            </div>
                            
                            <div id="noData" class="text-center text-muted">
                                <i class="fas fa-qrcode fa-3x mb-3"></i>
                                <p>Scan QR Code untuk menampilkan data wisudawan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let html5QrCode;
let signaturePad;
let currentWisudawanId = null;

// Initialize signature pad
<?php if ($type === 'toga' || $type === 'hadir'): ?>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signaturePad');
    signaturePad = new SignaturePad(canvas);
});

function clearSignature() {
    signaturePad.clear();
}
<?php endif; ?>

// Start QR Scanner
document.getElementById('startScan').addEventListener('click', function() {
    html5QrCode = new Html5Qrcode("reader");
    
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        onScanSuccess,
        onScanError
    ).then(() => {
        document.getElementById('startScan').classList.add('d-none');
        document.getElementById('stopScan').classList.remove('d-none');
    });
});

// Stop QR Scanner
document.getElementById('stopScan').addEventListener('click', function() {
    html5QrCode.stop().then(() => {
        document.getElementById('startScan').classList.remove('d-none');
        document.getElementById('stopScan').classList.add('d-none');
    });
});

function onScanSuccess(decodedText, decodedResult) {
    processQRCode(decodedText);
}

function onScanError(error) {
    // Ignore scan errors
}

function processManualCode() {
    const code = document.getElementById('manualCode').value;
    if (code.length === 4) {
        processQRCode(code);
    } else {
        Swal.fire('Error', 'Kode harus 4 digit', 'error');
    }
}

function processQRCode(kodeUnik) {
    fetch('<?= url('presensi/process') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            kode_unik: kodeUnik,
            periode_id: '<?= $periode['id'] ?>',
            type: '<?= $type ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentWisudawanId = data.data.id;
            
            // Show wisudawan info
            document.getElementById('nim').textContent = data.data.nim;
            document.getElementById('nama').textContent = data.data.nama_lengkap;
            document.getElementById('prodi').textContent = data.data.program_studi;
            
            <?php if ($type === 'toga'): ?>
            document.getElementById('ukuran_toga').textContent = data.data.ukuran_toga;
            <?php endif; ?>
            
            document.getElementById('nomor_kursi').textContent = data.data.nomor_kursi || '-';
            
            document.getElementById('noData').classList.add('d-none');
            document.getElementById('wisudawanInfo').classList.remove('d-none');
            
            if (data.already_present) {
                document.getElementById('alreadyPresentMsg').textContent = data.message;
                document.getElementById('alreadyPresent').classList.remove('d-none');
                document.getElementById('presensiForm').classList.add('d-none');
            } else {
                document.getElementById('alreadyPresent').classList.add('d-none');
                document.getElementById('presensiForm').classList.remove('d-none');
            }
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
    });
}

function submitPresensi() {
    <?php if ($type === 'toga' || $type === 'hadir'): ?>
    if (signaturePad.isEmpty()) {
        Swal.fire('Error', 'Tanda tangan harus diisi', 'error');
        return;
    }
    
    const ttdData = signaturePad.toDataURL();
    <?php else: ?>
    const ttdData = '';
    <?php endif; ?>
    
    const keterangan = document.getElementById('keterangan')?.value || '';
    
    fetch('<?= url('presensi/submit') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            wisudawan_id: currentWisudawanId,
            periode_id: '<?= $periode['id'] ?>',
            type: '<?= $type ?>',
            ttd: ttdData,
            keterangan: keterangan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Berhasil', data.message, 'success');
            
            // Reset form
            <?php if ($type === 'toga' || $type === 'hadir'): ?>
            signaturePad.clear();
            <?php endif; ?>
            
            if (document.getElementById('keterangan')) {
                document.getElementById('keterangan').value = '';
            }
            
            document.getElementById('wisudawanInfo').classList.add('d-none');
            document.getElementById('noData').classList.remove('d-none');
            document.getElementById('manualCode').value = '';
            currentWisudawanId = null;
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
    });
}
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
