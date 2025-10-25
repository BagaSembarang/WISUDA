<?php
/**
 * Presensi Controller
 */

class PresensiController extends Controller {
    private $wisudawanModel;
    private $sesiModel;
    private $periodeModel;
    private $activityLog;
    
    public function __construct() {
        $this->requireLogin();
        $this->wisudawanModel = new Wisudawan();
        $this->sesiModel = new SesiWisuda();
        $this->periodeModel = new PeriodeWisuda();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Presensi page
     */
    public function index() {
        $periodes = $this->periodeModel->getActive();
        
        $data = [
            'title' => 'Presensi Wisudawan',
            'periodes' => $periodes
        ];
        
        $this->view('presensi/index', $data);
    }
    
    /**
     * Scan QR Code
     */
    public function scan($type = 'toga') {
        $periodeId = $this->get('periode_id');
        
        if (!$periodeId) {
            setFlash('danger', 'Pilih periode terlebih dahulu');
            $this->redirect('presensi/index');
        }
        
        $periode = $this->periodeModel->find($periodeId);
        
        $data = [
            'title' => 'Scan QR Code - ' . ucfirst($type),
            'type' => $type,
            'periode' => $periode
        ];
        
        $this->view('presensi/scan', $data);
    }
    
    /**
     * Process QR Code
     */
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $kodeUnik = $this->post('kode_unik');
        $periodeId = $this->post('periode_id');
        $type = $this->post('type');
        
        if (empty($kodeUnik) || empty($periodeId) || empty($type)) {
            $this->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
        }
        
        $wisudawan = $this->wisudawanModel->findByKode($kodeUnik, $periodeId);
        
        if (!$wisudawan) {
            $this->json(['success' => false, 'message' => 'Kode tidak valid'], 404);
        }
        
        // Check if already presensi
        $alreadyPresent = false;
        $message = '';
        
        switch ($type) {
            case 'toga':
                if ($wisudawan['presensi_toga_at']) {
                    $alreadyPresent = true;
                    $message = 'Wisudawan sudah melakukan presensi pengambilan toga';
                }
                break;
            case 'gladi':
                if ($wisudawan['presensi_gladi_at']) {
                    $alreadyPresent = true;
                    $message = 'Wisudawan sudah melakukan presensi gladi bersih';
                }
                break;
            case 'hadir':
                if ($wisudawan['presensi_hadir_at']) {
                    $alreadyPresent = true;
                    $message = 'Wisudawan sudah melakukan presensi hari-H';
                }
                break;
            case 'konsumsi':
                if ($wisudawan['presensi_konsumsi_at']) {
                    $alreadyPresent = true;
                    $message = 'Wisudawan sudah melakukan presensi pengambilan konsumsi';
                }
                break;
        }
        
        $this->json([
            'success' => true,
            'already_present' => $alreadyPresent,
            'message' => $message,
            'data' => [
                'id' => $wisudawan['id'],
                'nim' => $wisudawan['nim'],
                'nama_lengkap' => $wisudawan['nama_lengkap'],
                'program_studi' => $wisudawan['program_studi'],
                'ukuran_toga' => $wisudawan['ukuran_toga'],
                'nomor_kursi' => $wisudawan['nomor_kursi']
            ]
        ]);
    }
    
    /**
     * Submit presensi
     */
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $wisudawanId = $this->post('wisudawan_id');
        $periodeId = $this->post('periode_id');
        $type = $this->post('type');
        $ttd = $this->post('ttd'); // Base64 signature
        $keterangan = $this->post('keterangan', '');
        
        $this->wisudawanModel->getTableByPeriode($periodeId);
        
        try {
            $result = false;
            
            switch ($type) {
                case 'toga':
                    $result = $this->wisudawanModel->updatePresensiToga(
                        $wisudawanId, 
                        $_SESSION['user_id'], 
                        $ttd, 
                        $keterangan
                    );
                    break;
                    
                case 'gladi':
                    $result = $this->wisudawanModel->updatePresensiGladi(
                        $wisudawanId, 
                        $_SESSION['user_id']
                    );
                    break;
                    
                case 'hadir':
                    $result = $this->wisudawanModel->updatePresensiHadir(
                        $wisudawanId, 
                        $_SESSION['user_id'], 
                        $ttd, 
                        $keterangan
                    );
                    break;
                    
                case 'konsumsi':
                    $result = $this->wisudawanModel->updatePresensiKonsumsi(
                        $wisudawanId, 
                        $_SESSION['user_id']
                    );
                    break;
            }
            
            if ($result) {
                $this->activityLog->log(
                    $_SESSION['user_id'], 
                    'presensi_' . $type, 
                    "Presensi {$type} untuk wisudawan ID: {$wisudawanId}"
                );
                
                $this->json(['success' => true, 'message' => 'Presensi berhasil disimpan']);
            } else {
                $this->json(['success' => false, 'message' => 'Gagal menyimpan presensi'], 500);
            }
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
