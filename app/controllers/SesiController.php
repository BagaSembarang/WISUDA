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

    public function manageDenah($periodeId) {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('admin/periode');
        }
        $sesiList = $this->sesiModel->getByPeriode($periodeId);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = isset($_FILES['denah_excel']) ? $_FILES['denah_excel'] : null;
            if (!$file || $file['error'] !== UPLOAD_ERR_OK || !isValidExcel($file)) {
                setFlash('danger', 'File tidak valid');
                $this->redirect('sesi/manageDenah/' . $periodeId);
            }
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
                $seats = [];
                $used = [];
                for ($r = 1; $r <= $highestRow; $r++) {
                    $baris = $this->numToLetters($r);
                    for ($c = 1; $c <= $highestColumn; $c++) {
                        $val = trim((string)$sheet->getCellByColumnAndRow($c, $r)->getCalculatedValue());
                        if ($val === '') { continue; }
                        if (isset($used[$val])) { continue; }
                        $used[$val] = true;
                        $seats[] = [
                            'nomor_kursi' => $val,
                            'baris' => $baris,
                            'kolom' => $c,
                            'zona' => 'Utama'
                        ];
                    }
                }
                foreach ($sesiList as $s) {
                    $this->denahModel->replaceForSesi($s['id'], $seats);
                }
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    'import_denah_periode',
                    'Import denah untuk periode ID: ' . $periodeId
                );
                setFlash('success', 'Denah berhasil diimpor dan diterapkan ke semua sesi pada periode ini');
                $this->redirect('sesi/manageDenah/' . $periodeId);
            } catch (Exception $e) {
                setFlash('danger', 'Error: ' . $e->getMessage());
                $this->redirect('sesi/manageDenah/' . $periodeId);
            }
        } else {
            $data = [
                'title' => 'Kelola Denah Periode',
                'periode' => $periode,
                'sesi_list' => $sesiList
            ];
            $this->view('admin/denah/index', $data);
        }
    }

    public function previewDenah($periodeId, $sesiId) {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('admin/periode');
        }
        $sesi = $this->sesiModel->find($sesiId);
        if (!$sesi || (int)$sesi['periode_id'] !== (int)$periodeId) {
            setFlash('danger', 'Sesi tidak valid');
            $this->redirect('sesi/manageDenah/' . $periodeId);
        }
        $sesiList = $this->sesiModel->getByPeriode($periodeId);
        $denah = $this->denahModel->getBySesi($sesiId);
        $rows = [];
        $maxCol = 0;
        $grid = [];
        foreach ($denah as $d) {
            $rows[$d['baris']] = true;
            $col = (int)$d['kolom'];
            if ($col > $maxCol) { $maxCol = $col; }
            $grid[$d['baris'] . '-' . $col] = $d['nomor_kursi'];
        }
        $rows = array_keys($rows);
        sort($rows);
        $occupied = [];
        $occupied_norm = [];
        $w = new Wisudawan();
        $wis = $w->getBySesi($sesiId, $periodeId);
        foreach ($wis as $item) {
            if (!empty($item['nomor_kursi']) && !empty($item['presensi_hadir_at'])) {
                $seat = trim((string)$item['nomor_kursi']);
                $occupied[$seat] = true;
                $norm = strtoupper(preg_replace('/[^A-Z0-9]/','', $seat));
                $norm = preg_replace_callback('/\\d+/', function($m){ $v = ltrim($m[0], '0'); return $v === '' ? '0' : $v; }, $norm);
                $occupied_norm[$norm] = true;
            }
        }
        $data = [
            'title' => 'Preview Denah',
            'periode' => $periode,
            'sesi' => $sesi,
            'sesi_list' => $sesiList,
            'rows' => $rows,
            'max_col' => $maxCol,
            'grid' => $grid,
            'occupied' => $occupied,
            'occupied_norm' => $occupied_norm
        ];
        $this->view('admin/denah/preview', $data);
    }

    private function numToLetters($n) {
        $r = '';
        while ($n > 0) {
            $n--;
            $r = chr(65 + ($n % 26)) . $r;
            $n = intdiv($n, 26);
        }
        return $r;
    }

    public function downloadDenahTemplate($periodeId) {
        $rows = (int)$this->get('rows', 20);
        $cols = (int)$this->get('cols', 12);
        $type = strtolower((string)$this->get('type', 'blank'));
        $start = (int)$this->get('start', 1);
        $pad = (int)$this->get('pad', 3);
        if ($rows < 1) { $rows = 1; }
        if ($cols < 1) { $cols = 1; }
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Denah');
        $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cols);
        $range = 'A1:' . $endCol . $rows;
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        for ($c = 1; $c <= $cols; $c++) { $sheet->getColumnDimensionByColumn($c)->setWidth(6.5); }
        for ($r = 1; $r <= $rows; $r++) { $sheet->getRowDimension($r)->setRowHeight(20); }
        if ($type === 'numeric') {
            $n = $start;
            for ($r = 1; $r <= $rows; $r++) {
                for ($c = 1; $c <= $cols; $c++) {
                    $sheet->setCellValueByColumnAndRow($c, $r, str_pad($n, $pad, '0', STR_PAD_LEFT));
                    $n++;
                }
            }
        } elseif ($type === 'vip') {
            for ($c = 1; $c <= $cols; $c++) {
                $sheet->setCellValueByColumnAndRow($c, 1, 'VIP ' . str_pad($c, 2, '0', STR_PAD_LEFT));
            }
            $n = $start;
            for ($r = 2; $r <= $rows; $r++) {
                for ($c = 1; $c <= $cols; $c++) {
                    $sheet->setCellValueByColumnAndRow($c, $r, str_pad($n, $pad, '0', STR_PAD_LEFT));
                    $n++;
                }
            }
        }
        $filename = 'template_denah_' . $type . '_' . $rows . 'x' . $cols . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
