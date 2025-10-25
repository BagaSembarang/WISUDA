<?php
/**
 * LO (Liaison Officer) Controller
 */

class LOController extends Controller {
    private $wisudawanModel;
    private $sesiModel;
    private $periodeModel;
    
    public function __construct() {
        $this->requireRole('lo');
        $this->wisudawanModel = new Wisudawan();
        $this->sesiModel = new SesiWisuda();
        $this->periodeModel = new PeriodeWisuda();
    }
    
    /**
     * Dashboard LO
     */
    public function dashboard() {
        $periodes = $this->periodeModel->getActive();
        
        $data = [
            'title' => 'Dashboard LO',
            'periodes' => $periodes
        ];
        
        $this->view('lo/dashboard', $data);
    }
    
    /**
     * View denah kehadiran
     */
    public function denah($sesiId) {
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('lo/dashboard');
        }
        
        $denah = $this->wisudawanModel->getDenahKehadiran($sesiId, $sesi['periode_id']);
        $stats = $this->wisudawanModel->getStatsBySesi($sesiId, $sesi['periode_id']);
        
        $data = [
            'title' => 'Denah Kehadiran - ' . $sesi['nama_sesi'],
            'sesi' => $sesi,
            'denah' => $denah,
            'stats' => $stats
        ];
        
        $this->view('lo/denah', $data);
    }
    
    /**
     * Scan undangan untuk cek lokasi kursi
     */
    public function scanUndangan() {
        $periodeId = $this->get('periode_id');
        
        if (!$periodeId) {
            setFlash('danger', 'Pilih periode terlebih dahulu');
            $this->redirect('lo/dashboard');
        }
        
        $periode = $this->periodeModel->find($periodeId);
        
        $data = [
            'title' => 'Scan Undangan',
            'periode' => $periode
        ];
        
        $this->view('lo/scan', $data);
    }
    
    /**
     * Process scan undangan
     */
    public function processScan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $kodeUnik = $this->post('kode_unik');
        $periodeId = $this->post('periode_id');
        
        if (empty($kodeUnik) || empty($periodeId)) {
            $this->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
        }
        
        $wisudawan = $this->wisudawanModel->findByKode($kodeUnik, $periodeId);
        
        if (!$wisudawan) {
            $this->json(['success' => false, 'message' => 'Kode tidak valid'], 404);
        }
        
        $sesi = $this->sesiModel->find($wisudawan['sesi_id']);
        
        $this->json([
            'success' => true,
            'data' => [
                'nim' => $wisudawan['nim'],
                'nama_lengkap' => $wisudawan['nama_lengkap'],
                'program_studi' => $wisudawan['program_studi'],
                'nomor_kursi' => $wisudawan['nomor_kursi'],
                'sesi' => $sesi['nama_sesi'],
                'lokasi' => $sesi['lokasi'],
                'status_rsvp' => $wisudawan['status_rsvp'],
                'status_kehadiran' => $wisudawan['presensi_hadir_at'] ? 'Sudah Hadir' : 'Belum Hadir'
            ]
        ]);
    }
}
