<?php
/**
 * Sesi Wisuda Controller
 */

class SesiController extends Controller {
    private $sesiModel;
    private $periodeModel;
    private $denahModel;
    private $activityLog;
    
    public function __construct() {
        $this->requireRole('admin');
        $this->sesiModel = new SesiWisuda();
        $this->periodeModel = new PeriodeWisuda();
        $this->denahModel = new DenahKursi();
        $this->activityLog = new ActivityLog();
        $this->sesiModel->ensureIframeColumn();
    }
    
    /**
     * Create Sesi
     */
    public function create($periodeId) {
        $periode = $this->periodeModel->find($periodeId);
        
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($periodeId);
        } else {
            $data = [
                'title' => 'Tambah Sesi Wisuda',
                'periode' => $periode
            ];
            $this->view('admin/sesi/create', $data);
        }
    }
    
    /**
     * Handle create sesi
     */
    private function handleCreate($periodeId) {
        $namaSesi = $this->post('nama_sesi');
        $tanggal = $this->post('tanggal');
        $waktuMulai = $this->post('waktu_mulai');
        $waktuSelesai = $this->post('waktu_selesai');
        $lokasi = $this->post('lokasi');
        $lokasiIframe = $this->post('lokasi_iframe');
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');
        $kapasitas = $this->post('kapasitas', 0);
        $informasiTambahan = $this->post('informasi_tambahan');
        
        if (empty($namaSesi) || empty($tanggal) || empty($waktuMulai) || empty($waktuSelesai) || (empty($lokasi) && empty($lokasiIframe))) {
            setFlash('danger', 'Semua field wajib diisi');
            $this->redirect('sesi/create/' . $periodeId);
        }
        if (empty($lokasi) && !empty($lokasiIframe)) {
            $lokasi = 'Lokasi pada peta';
        }
        
        $sesiId = $this->sesiModel->insert([
            'periode_id' => $periodeId,
            'nama_sesi' => $namaSesi,
            'tanggal' => $tanggal,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'lokasi' => $lokasi,
            'lokasi_iframe' => $lokasiIframe,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'kapasitas' => $kapasitas,
            'informasi_tambahan' => $informasiTambahan
        ]);
        
        if ($sesiId) {
            $this->activityLog->log(
                $_SESSION['user_id'], 
                'create_sesi', 
                "Membuat sesi: {$namaSesi}"
            );
            
            setFlash('success', 'Sesi wisuda berhasil dibuat');
            $this->redirect('admin/viewPeriode/' . $periodeId);
        } else {
            setFlash('danger', 'Gagal membuat sesi wisuda');
            $this->redirect('sesi/create/' . $periodeId);
        }
    }
    
    /**
     * Edit Sesi
     */
    public function edit($id) {
        $sesi = $this->sesiModel->getWithPeriode($id);
        
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id, $sesi['periode_id']);
        } else {
            $data = [
                'title' => 'Edit Sesi Wisuda',
                'sesi' => $sesi
            ];
            $this->view('admin/sesi/edit', $data);
        }
    }
    
    /**
     * Handle edit sesi
     */
    private function handleEdit($id, $periodeId) {
        $namaSesi = $this->post('nama_sesi');
        $tanggal = $this->post('tanggal');
        $waktuMulai = $this->post('waktu_mulai');
        $waktuSelesai = $this->post('waktu_selesai');
        $lokasi = $this->post('lokasi');
        $lokasiIframe = $this->post('lokasi_iframe');
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');
        $kapasitas = $this->post('kapasitas', 0);
        $informasiTambahan = $this->post('informasi_tambahan');
        
        if (empty($namaSesi) || empty($tanggal) || empty($waktuMulai) || empty($waktuSelesai) || (empty($lokasi) && empty($lokasiIframe))) {
            setFlash('danger', 'Semua field wajib diisi');
            $this->redirect('sesi/edit/' . $id);
        }
        if (empty($lokasi) && !empty($lokasiIframe)) {
            $lokasi = 'Lokasi pada peta';
        }
        
        $updated = $this->sesiModel->update($id, [
            'nama_sesi' => $namaSesi,
            'tanggal' => $tanggal,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'lokasi' => $lokasi,
            'lokasi_iframe' => $lokasiIframe,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'kapasitas' => $kapasitas,
            'informasi_tambahan' => $informasiTambahan
        ]);
        
        if ($updated) {
            $this->activityLog->log(
                $_SESSION['user_id'], 
                'update_sesi', 
                "Mengupdate sesi ID: {$id}"
            );
            
            setFlash('success', 'Sesi wisuda berhasil diupdate');
        } else {
            setFlash('danger', 'Gagal mengupdate sesi wisuda');
        }
        
        $this->redirect('admin/viewPeriode/' . $periodeId);
    }
    
    /**
     * Delete Sesi
     */
    public function delete($id) {
        try {
            $sesi = $this->sesiModel->find($id);
            
            if (!$sesi) {
                $this->json(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
            }
            
            // Check if has wisudawan
            if ($this->sesiModel->hasWisudawan($id)) {
                $this->json(['success' => false, 'message' => 'Tidak dapat menghapus sesi yang sudah memiliki wisudawan'], 400);
            }
            
            if ($this->sesiModel->delete($id)) {
                $this->activityLog->log(
                    $_SESSION['user_id'], 
                    'delete_sesi', 
                    "Menghapus sesi ID: {$id}"
                );
                
                $this->json(['success' => true, 'message' => 'Sesi berhasil dihapus']);
            } else {
                $this->json(['success' => false, 'message' => 'Gagal menghapus sesi'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Generate Denah Kursi
     */
    public function generateDenah($sesiId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jumlahBaris = $this->post('jumlah_baris');
            $jumlahKolom = $this->post('jumlah_kolom');
            $zona = $this->post('zona', 'Utama');
            
            try {
                $this->denahModel->generateDenah($sesiId, $jumlahBaris, $jumlahKolom, $zona);
                
                $this->activityLog->log(
                    $_SESSION['user_id'], 
                    'generate_denah', 
                    "Generate denah untuk sesi ID: {$sesiId}"
                );
                
                $this->json(['success' => true, 'message' => 'Denah kursi berhasil digenerate']);
            } catch (Exception $e) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }
    }
}
