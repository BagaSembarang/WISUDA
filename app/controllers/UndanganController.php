<?php
/**
 * Undangan Controller
 */

class UndanganController extends Controller {
    private $wisudawanModel;
    private $sesiModel;
    private $periodeModel;
    private $informasiModel;
    
    public function __construct() {
        $this->wisudawanModel = new Wisudawan();
        $this->sesiModel = new SesiWisuda();
        $this->periodeModel = new PeriodeWisuda();
        $this->informasiModel = new InformasiUndangan();
    }
    
    /**
     * Show undangan by kode unik
     */
    public function show($kodeUnik) {
        if (empty($kodeUnik)) {
            $this->view('undangan/not_found');
            return;
        }
        
        // Find wisudawan across all active periodes
        $periodes = $this->periodeModel->getActive();
        $wisudawan = null;
        $periode = null;
        
        foreach ($periodes as $p) {
            $w = $this->wisudawanModel->findByKode($kodeUnik, $p['id']);
            if ($w) {
                $wisudawan = $w;
                $periode = $p;
                break;
            }
        }
        
        if (!$wisudawan) {
            $this->view('undangan/not_found');
            return;
        }
        
        $sesi = $this->sesiModel->find($wisudawan['sesi_id']);
        $informasiList = $this->informasiModel->getByPeriode($periode['id']);
        
        $data = [
            'title' => 'Undangan Wisuda',
            'wisudawan' => $wisudawan,
            'sesi' => $sesi,
            'periode' => $periode,
            'informasi_list' => $informasiList
        ];
        
        $this->view('undangan/view', $data);
    }
    
    /**
     * RSVP Confirmation
     */
    public function rsvp() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        $wisudawanId = $this->post('wisudawan_id');
        $periodeId = $this->post('periode_id');
        $status = $this->post('status'); // confirmed or declined
        
        if (!in_array($status, ['confirmed', 'declined'])) {
            $this->json(['success' => false, 'message' => 'Status tidak valid'], 400);
        }
        
        $this->wisudawanModel->getTableByPeriode($periodeId);
        
        try {
            $result = $this->wisudawanModel->updateRSVP($wisudawanId, $status);
            
            if ($result) {
                $message = $status === 'confirmed' 
                    ? 'Terima kasih atas konfirmasi kehadiran Anda' 
                    : 'Terima kasih atas konfirmasi Anda';
                    
                $this->json(['success' => true, 'message' => $message]);
            } else {
                $this->json(['success' => false, 'message' => 'Gagal menyimpan konfirmasi'], 500);
            }
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Generate QR Code for wisudawan
     */
    public function qrcode($kodeUnik) {
        
        $qrCode = new \Endroid\QrCode\QrCode($kodeUnik);
        $qrCode->setSize(QR_SIZE);
        $qrCode->setMargin(QR_MARGIN);
        
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        
        header('Content-Type: ' . $result->getMimeType());
        echo $result->getString();
        exit;
    }
}
