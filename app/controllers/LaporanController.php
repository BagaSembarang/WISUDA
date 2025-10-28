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
        
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        
        $ticket_width = 95;
        $ticket_height = 50;
        $margin_x = 10;
        $margin_y = 10;
        $gutter = 5;
        $tickets_per_row = 2;
        $tickets_per_page = 10;
        
        $ticket_count = 0;
        
        foreach ($wisudawanList as $w) {
            if ($ticket_count > 0 && $ticket_count % $tickets_per_page == 0) {
                $pdf->AddPage();
            }
            
            $row_index = floor(($ticket_count % $tickets_per_page) / $tickets_per_row);
            $col_index = ($ticket_count % $tickets_per_page) % $tickets_per_row;
            
            $x = $margin_x + ($col_index * ($ticket_width + $gutter));
            $y = $margin_y + ($row_index * ($ticket_height + $gutter));
            
            $nama_array = preg_split('/\s+/', trim((string)$w['nama_lengkap']));
            $nama_display = implode(' ', array_slice($nama_array, 0, 3));
            $nama_display = ucwords(strtolower($nama_display));
            
            if ((int)$sesiId === 1) {
                $header_color = [80, 20, 92];
            } else if ((int)$sesiId === 2) {
                $header_color = [255, 237, 0];
            } else if ((int)$sesiId === 3) {
                $header_color = [100, 149, 237];
            } else {
                $header_color = [169, 169, 169];
            }
            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->SetLineWidth(0.3);
            $pdf->Rect($x, $y, $ticket_width, $ticket_height, 'DF');
            
            $pdf->SetFillColor($header_color[0], $header_color[1], $header_color[2]);
            $pdf->Rect($x, $y, $ticket_width, 15, 'F');
            
            $logoPath = BASE_PATH . '/public/img/LogoSCU.png';
            if (!file_exists($logoPath)) {
                $logoPath = BASE_PATH . '/vendor/setasign/fpdf/tutorial/logo.png';
            }
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, $x + 3, $y + 1, 13, 0, 'PNG');
            }
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY($x + 20, $y + 2);
            $pdf->Cell($ticket_width - 25, 6, 'KUPON KONSUMSI', 0, 1, 'C');
            
            $pdf->SetFont('Helvetica', '', 7);
            $pdf->SetXY($x + 20, $y + 7);
            $displayText = trim(($sesi['nama_periode'] ?? '') . ' - ' . ($sesi['nama_sesi'] ?? ''));
            $pdf->Cell($ticket_width - 25, 5, $displayText, 0, 1, 'C');
            
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY($x + 3, $y + 17);
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->Cell(62, 5, $nama_display, 0, 1, 'L');
            
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetXY($x + 3, $pdf->GetY());
            $pdf->Cell(62, 4, 'NIM: ' . ($w['nim'] ?? ''), 0, 1, 'L');
            
            $pdf->SetXY($x + 3, $pdf->GetY());
            $pdf->Cell(62, 4, 'Sesi: ' . ($sesi['nama_sesi'] ?? '') . ' / Kursi: ' . ($w['nomor_kursi'] ?? ''), 0, 1, 'L');
            
            if (!empty($w['kode_unik'])) {
                $qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($w['kode_unik']) . '&ecc=M';
                $pdf->Image($qr_code_url, $x + 69, $y + 18, 24, 0, 'PNG');
            }
            
            $pdf->SetXY($x + 3, $y + 35);
            $pdf->SetFont('Helvetica', 'I', 6);
            $pdf->MultiCell($ticket_width - 6, 3, "- Pengambilan konsumsi oleh 1 perwakilan (1 paket isi 3 box).\n- Panduan lengkap wisuda: " . BASE_URL . "?i=" . ($w['kode_unik'] ?? ''), 0, 'L');
            
            $ticket_count++;
        }
        
        $filename = 'Kupon_Konsumsi_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', ($sesi['nama_sesi'] ?? 'Sesi')) . '.pdf';
        $pdf->Output('I', $filename);
    }
}
