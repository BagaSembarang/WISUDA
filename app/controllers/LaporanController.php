<?php
/**
 * Laporan Controller
 */

class LaporanController extends Controller {
    private $wisudawanModel;
    private $sesiModel;
    private $periodeModel;
    
    public function __construct() {
        $this->requireRole('admin');
        $this->wisudawanModel = new Wisudawan();
        $this->sesiModel = new SesiWisuda();
        $this->periodeModel = new PeriodeWisuda();
    }
    
    /**
     * Laporan index
     */
    public function index() {
        $periodes = $this->periodeModel->all('tahun DESC, periode_ke DESC');
        
        $data = [
            'title' => 'Laporan',
            'periodes' => $periodes
        ];
        
        $this->view('admin/laporan/index', $data);
    }
    
    public function sesiByPeriode($periodeId) {
        try {
            $sessions = $this->sesiModel->getWithStats($periodeId);
            $this->json(['success' => true, 'data' => $sessions]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Laporan presensi
     */
    public function presensi($sesiId) {
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('laporan/index');
        }
        
        $wisudawanList = $this->wisudawanModel->getBySesi($sesiId, $sesi['periode_id']);
        $stats = $this->wisudawanModel->getStatsBySesi($sesiId, $sesi['periode_id']);
        
        $data = [
            'title' => 'Laporan Presensi - ' . $sesi['nama_sesi'],
            'sesi' => $sesi,
            'wisudawan_list' => $wisudawanList,
            'stats' => $stats
        ];
        
        $this->view('admin/laporan/presensi', $data);
    }
    
    public function toga($sesiId) {
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('laporan/index');
        }
        
        $wisudawanList = $this->wisudawanModel->getBySesi($sesiId, $sesi['periode_id']);
        $stats = $this->wisudawanModel->getStatsBySesi($sesiId, $sesi['periode_id']);
        
        $data = [
            'title' => 'Laporan Pengambilan Toga - ' . $sesi['nama_sesi'],
            'sesi' => $sesi,
            'wisudawan_list' => $wisudawanList,
            'stats' => $stats
        ];
        
        $this->view('admin/laporan/toga', $data);
    }
    
    /**
     * Export to PDF
     */
    public function exportPDF($sesiId, $type = 'presensi') {
        require_once BASE_PATH . '/vendor/setasign/fpdf/fpdf.php';
        
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        if (!$sesi) {
            die('Sesi tidak ditemukan');
        }
        $wisudawanList = $this->wisudawanModel->getBySesi($sesiId, $sesi['periode_id']);
        
        // Group by fakultas
        $groups = [];
        foreach ($wisudawanList as $w) {
            $fak = trim((string)($w['fakultas'] ?? ''));
            if ($fak === '') { $fak = 'Lainnya'; }
            if (!isset($groups[$fak])) { $groups[$fak] = []; }
            $groups[$fak][] = $w;
        }
        ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);
        
        $pdf = new FPDF('L', 'mm', 'A4');
        
        foreach ($groups as $fakultas => $items) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'LAPORAN ' . strtoupper($type), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, $sesi['nama_periode'] . ' - ' . $sesi['nama_sesi'], 0, 1, 'C');
            $pdf->Ln(2);
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 7, 'Fakultas: ' . $fakultas, 0, 1, 'L');
            $pdf->Ln(2);
            
            // Table header
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 7, 'No', 1, 0, 'C');
            $pdf->Cell(30, 7, 'NIM', 1, 0, 'C');
            $pdf->Cell(70, 7, 'Nama', 1, 0, 'C');
            $pdf->Cell(60, 7, 'Program Studi', 1, 0, 'C');
            
            if ($type === 'presensi') {
                $pdf->Cell(25, 7, 'Toga', 1, 0, 'C');
                $pdf->Cell(25, 7, 'Gladi', 1, 0, 'C');
                $pdf->Cell(25, 7, 'Hari-H', 1, 0, 'C');
                $pdf->Cell(25, 7, 'Konsumsi', 1, 1, 'C');
            } elseif ($type === 'toga') {
                $pdf->Cell(25, 7, 'Ukuran', 1, 0, 'C');
                $pdf->Cell(30, 7, 'Ambil Toga', 1, 0, 'C');
                $pdf->Cell(40, 7, 'Keterangan', 1, 1, 'C');
            } else {
                $pdf->Cell(25, 7, 'Ukuran', 1, 0, 'C');
                $pdf->Cell(30, 7, 'TTD Toga', 1, 0, 'C');
                $pdf->Cell(40, 7, 'TTD Ijazah', 1, 1, 'C');
            }
            
            // Table rows
            $pdf->SetFont('Arial', '', 9);
            $no = 1;
            foreach ($items as $w) {
                $pdf->Cell(10, 6, $no++, 1, 0, 'C');
                $pdf->Cell(30, 6, $w['nim'], 1, 0);
                $pdf->Cell(70, 6, substr($w['nama_lengkap'], 0, 42), 1, 0);
                $pdf->Cell(60, 6, substr($w['program_studi'], 0, 34), 1, 0);
                if ($type === 'presensi') {
                    $pdf->Cell(25, 6, $w['presensi_toga_at'] ? 'Ya' : '-', 1, 0, 'C');
                    $pdf->Cell(25, 6, $w['presensi_gladi_at'] ? 'Ya' : '-', 1, 0, 'C');
                    $pdf->Cell(25, 6, $w['presensi_hadir_at'] ? 'Ya' : '-', 1, 0, 'C');
                    $pdf->Cell(25, 6, $w['presensi_konsumsi_at'] ? 'Ya' : '-', 1, 1, 'C');
                } elseif ($type === 'toga') {
                    $pdf->Cell(25, 6, $w['ukuran_toga'], 1, 0, 'C');
                    $pdf->Cell(30, 6, $w['presensi_toga_at'] ? 'Sudah' : 'Belum', 1, 0, 'C');
                    $ket = $w['keterangan_toga'] ? substr($w['keterangan_toga'], 0, 25) : '-';
                    $pdf->Cell(40, 6, $ket, 1, 1);
                } else {
                    $pdf->Cell(25, 6, $w['ukuran_toga'], 1, 0, 'C');
                    $pdf->Cell(30, 6, $w['ttd_toga'] ? 'Ada' : '-', 1, 0, 'C');
                    $pdf->Cell(40, 6, $w['ttd_hadir'] ? 'Ada' : '-', 1, 1, 'C');
                }
            }
        }
        
        $filename = 'Laporan_' . $type . '_' . date('YmdHis') . '.pdf';
        $pdf->Output('D', $filename);
    }
    
    /**
     * Print kupon
     */
    public function printKupon($sesiId) {
        // Use FPDF from composer package setasign/fpdf
        require_once BASE_PATH . '/vendor/setasign/fpdf/fpdf.php';
        
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        
        if (!$sesi) {
            die('Sesi tidak ditemukan');
        }
        
        $wisudawanList = $this->wisudawanModel->getBySesi($sesiId, $sesi['periode_id']);
        $settingKupon = (new SettingKupon())->getByPeriode($sesi['periode_id']);
        
        $pdf = new FPDF('P', 'mm', 'A4');
        
        foreach ($wisudawanList as $w) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 14);
            
            // Header
            if ($settingKupon && $settingKupon['template_header']) {
                $pdf->MultiCell(0, 7, $settingKupon['template_header'], 0, 'C');
            } else {
                $pdf->Cell(0, 10, 'KUPON WISUDA', 0, 1, 'C');
            }
            
            $pdf->Ln(5);
            
            // Body
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(50, 7, 'Nama', 0, 0);
            $pdf->Cell(5, 7, ':', 0, 0);
            $pdf->Cell(0, 7, $w['nama_lengkap'], 0, 1);
            
            $pdf->Cell(50, 7, 'NIM', 0, 0);
            $pdf->Cell(5, 7, ':', 0, 0);
            $pdf->Cell(0, 7, $w['nim'], 0, 1);
            
            $pdf->Cell(50, 7, 'Program Studi', 0, 0);
            $pdf->Cell(5, 7, ':', 0, 0);
            $pdf->Cell(0, 7, $w['program_studi'], 0, 1);
            
            $pdf->Cell(50, 7, 'Nomor Kursi', 0, 0);
            $pdf->Cell(5, 7, ':', 0, 0);
            $pdf->Cell(0, 7, $w['nomor_kursi'], 0, 1);
            
            $pdf->Cell(50, 7, 'Kode Unik', 0, 0);
            $pdf->Cell(5, 7, ':', 0, 0);
            $pdf->Cell(0, 7, $w['kode_unik'], 0, 1);
            
            $pdf->Ln(10);
            
            // QR Code placeholder (you need to generate actual QR code)
            $pdf->Cell(0, 7, '[QR CODE: ' . $w['kode_unik'] . ']', 0, 1, 'C');
            
            $pdf->Ln(10);
            
            // Footer
            if ($settingKupon && $settingKupon['template_footer']) {
                $pdf->MultiCell(0, 7, $settingKupon['template_footer'], 0, 'C');
            }
        }
        
        $filename = 'Kupon_' . $sesi['nama_sesi'] . '_' . date('YmdHis') . '.pdf';
        $pdf->Output('D', $filename);
    }
}
