<?php
/**
 * Wisudawan Controller
 */

class WisudawanController extends Controller {
    private $wisudawanModel;
    private $sesiModel;
    private $periodeModel;
    private $activityLog;
    
    public function __construct() {
        $this->requireRole('admin');
        $this->wisudawanModel = new Wisudawan();
        $this->sesiModel = new SesiWisuda();
        $this->periodeModel = new PeriodeWisuda();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * List wisudawan by sesi
     */
    public function index($sesiId) {
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        $wisudawanList = $this->wisudawanModel->getBySesi($sesiId, $sesi['periode_id']);
        $stats = $this->wisudawanModel->getStatsBySesi($sesiId, $sesi['periode_id']);
        
        $data = [
            'title' => 'Data Wisudawan - ' . $sesi['nama_sesi'],
            'sesi' => $sesi,
            'wisudawan_list' => $wisudawanList,
            'stats' => $stats
        ];
        
        $this->view('admin/wisudawan/index', $data);
    }
    
    /**
     * Export Excel (HTML table) with requested columns and order
     * Columns: No Hp, Nama Mahasiswa, Nomor Kursi, Kode Unik, Sesi, Tanggal dan Jam TM, link zoom,
     *          Tanggal dan Jam Gladi, Tanggal dan Jam Wisuda, Jam Hadir Wisuda, Periode, Tahun
     */
    public function export($sesiId) {
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('admin/periode');
        }
        $wisudawanList = $this->wisudawanModel->getBySesi($sesiId, $sesi['periode_id']);

        // Prepare spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Wisudawan');

        // Header row
        $headers = [
            'No Hp', 'Nama Mahasiswa', 'Nomor Kursi', 'Kode Unik', 'Sesi',
            'Tanggal dan Jam TM', 'link zoom', 'Tanggal dan Jam Gladi',
            'Tanggal dan Jam Wisuda', 'Jam Hadir Wisuda', 'Periode', 'Tahun'
        ];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }
        // Bold headers
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        // Helpers
        $fmtDateTime = function($date, $time) {
            if (empty($date) || empty($time)) return '';
            return date('Y-m-d', strtotime($date)) . ' ' . substr($time, 0, 5);
        };
        $fmtTime = function($dt) {
            if (empty($dt)) return '';
            $ts = strtotime($dt);
            return $ts ? date('H:i', $ts) : '';
        };

        // Data rows
        $row = 2;
        foreach ($wisudawanList as $w) {
            $nohp = (string)($w['no_hp'] ?? '');
            $nama = $w['nama_lengkap'] ?? '';
            $kursi = $w['nomor_kursi'] ?? '';
            $kode = $w['kode_unik'] ?? '';
            $sesiNama = $sesi['nama_sesi'] ?? '';
            $tm = '';
            $zoom = '';
            $gladi = '';
            $wisuda = $fmtDateTime($sesi['tanggal'] ?? '', $sesi['waktu_mulai'] ?? '');
            $jamHadir = $fmtTime($w['presensi_hadir_at'] ?? '');
            $periode = $sesi['nama_periode'] ?? '';
            $tahun = $sesi['tahun'] ?? '';

            // Set values; phone as text
            $sheet->setCellValueExplicit('A' . $row, $nohp, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $nama);
            $sheet->setCellValue('C' . $row, $kursi);
            $sheet->setCellValue('D' . $row, $kode);
            $sheet->setCellValue('E' . $row, $sesiNama);
            $sheet->setCellValue('F' . $row, $tm);
            $sheet->setCellValue('G' . $row, $zoom);
            $sheet->setCellValue('H' . $row, $gladi);
            $sheet->setCellValue('I' . $row, $wisuda);
            $sheet->setCellValue('J' . $row, $jamHadir);
            $sheet->setCellValue('K' . $row, $periode);
            $sheet->setCellValue('L' . $row, $tahun);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output as XLSX
        $filename = 'wisudawan_export_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $sesi['nama_sesi']) . '_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Upload Excel
     */
    public function upload($sesiId) {
        $sesi = $this->sesiModel->getWithPeriode($sesiId);
        
        if (!$sesi) {
            setFlash('danger', 'Sesi tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpload($sesiId, $sesi['periode_id']);
        } else {
            $data = [
                'title' => 'Upload Data Wisudawan',
                'sesi' => $sesi
            ];
            $this->view('admin/wisudawan/upload', $data);
        }
    }
    
    /**
     * Handle upload Excel
     */
    private function handleUpload($sesiId, $periodeId) {
        if (isset($_POST['sesi_id'])) {
            $sesiId = $_POST['sesi_id'];
        }
        if (isset($_POST['periode_id'])) {
            $periodeId = $_POST['periode_id'];
        }
        $file = isset($_FILES['excel_file']) ? $_FILES['excel_file'] : (isset($_FILES['file_excel']) ? $_FILES['file_excel'] : null);
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            setFlash('danger', 'File tidak valid');
            $this->redirect('wisudawan/upload/' . $sesiId);
        }
        
        // Validate file type
        if (!isValidExcel($file)) {
            setFlash('danger', 'File harus berformat Excel (.xls atau .xlsx)');
            $this->redirect('wisudawan/upload/' . $sesiId);
        }
        
        try {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $rows = false;

            $phpssError = '';

            if ($rows === false && $ext === 'xlsx') {
                if (!class_exists('ZipArchive')) {
                    setFlash('danger', 'Dukungan .xlsx membutuhkan ekstensi PHP zip (ZipArchive). Aktifkan extension=zip di php.ini lalu restart server.');
                    $this->redirect('wisudawan/upload/' . $sesiId);
                }
                if (file_exists(BASE_PATH . '/vendor/SimpleXLSX.php')) {
                    require_once BASE_PATH . '/vendor/SimpleXLSX.php';
                } else {
                    require_once BASE_PATH . '/vendor/shuchkin/simplexlsx/src/SimpleXLSX.php';
                }
                $doc = false;
                $nsXlsx = 'Shuchkin\\SimpleXLSX';
                if (class_exists('SimpleXLSX')) {
                    $doc = call_user_func(['SimpleXLSX', 'parse'], $file['tmp_name']);
                } elseif (class_exists($nsXlsx)) {
                    $doc = call_user_func([$nsXlsx, 'parse'], $file['tmp_name']);
                }
                if (!$doc) {
                    $err = '';
                    if (class_exists($nsXlsx) && method_exists($nsXlsx, 'parseError')) {
                        $err = call_user_func([$nsXlsx, 'parseError']);
                    } elseif (class_exists('SimpleXLSX') && method_exists('SimpleXLSX', 'parseError')) {
                        $err = call_user_func(['SimpleXLSX', 'parseError']);
                    }
                    setFlash('danger', 'Gagal membaca file .xlsx' . ($err !== '' ? (': ' . $err) : ''));
                    $this->redirect('wisudawan/upload/' . $sesiId);
                }
                $rows = $doc->rows();
            } elseif ($rows === false && $ext === 'xls') {
                $path1 = BASE_PATH . '/vendor/SimpleXLS.php';
                $path2 = BASE_PATH . '/vendor/shuchkin/simplexls/src/SimpleXLS.php';
                if (file_exists($path1) || file_exists($path2)) {
                    if (file_exists($path1)) {
                        require_once $path1;
                    } else {
                        require_once $path2;
                    }
                    $doc = null;
                    $nsXls = 'Shuchkin\\SimpleXLS';
                    if (class_exists($nsXls)) {
                        $doc = call_user_func([$nsXls, 'parse'], $file['tmp_name']);
                    } elseif (class_exists('SimpleXLS')) {
                        $doc = call_user_func(['SimpleXLS', 'parse'], $file['tmp_name']);
                    }
                    if (!$doc) {
                        $err = '';
                        if (class_exists($nsXls) && method_exists($nsXls, 'parseError')) {
                            $err = call_user_func([$nsXls, 'parseError']);
                        } elseif (class_exists('SimpleXLS') && method_exists('SimpleXLS', 'parseError')) {
                            $err = call_user_func(['SimpleXLS', 'parseError']);
                        }
                        setFlash('danger', 'Gagal membaca file .xls' . ($err !== '' ? (': ' . $err) : ''));
                        $this->redirect('wisudawan/upload/' . $sesiId);
                    }
                    $rows = $doc->rows();
                } else {
                    setFlash('danger', 'Dukungan file .xls belum terpasang. Silakan unggah file .xlsx.');
                    $this->redirect('wisudawan/upload/' . $sesiId);
                }
            } else {
                setFlash('danger', 'Ekstensi file tidak didukung');
                $this->redirect('wisudawan/upload/' . $sesiId);
            }
            
            if (!$rows || count($rows) === 0) {
                setFlash('danger', 'File Excel kosong');
                $this->redirect('wisudawan/upload/' . $sesiId);
            }

            $headerRow = $rows[0] ?? [];
            $headerIndex = [];
            foreach ($headerRow as $i => $h) {
                $key = $this->normalizeHeader((string)$h);
                if ($key !== '') {
                    $headerIndex[$key] = $i;
                }
            }
            array_shift($rows);

            $uniqueHeaders = [];
            $headerCounts = [];
            foreach ($headerRow as $i => $rawH) {
                $base = trim((string)$rawH);
                if ($base === '') {
                    $base = 'COL_' . $i;
                }
                if (!isset($headerCounts[$base])) {
                    $headerCounts[$base] = 1;
                    $name = $base;
                } else {
                    $headerCounts[$base]++;
                    $name = $base . '_' . $headerCounts[$base];
                }
                $uniqueHeaders[$i] = $name;
            }

            $data = [];
            if ($this->shouldUseIndexMapping($headerIndex)) {
                foreach ($rows as $row) {
                    $nim = isset($row[7]) ? trim((string)$row[7]) : '';
                    $nama = isset($row[8]) ? trim((string)$row[8]) : '';
                    $prodi = isset($row[5]) ? trim((string)$row[5]) : '';
                    $kursi = isset($row[2]) ? trim((string)$row[2]) : '';
                    $email = isset($row[32]) ? trim((string)$row[32]) : '';
                    $nohpRaw = isset($row[17]) ? $row[17] : (isset($row[12]) ? $row[12] : '');
                    $nohp = $this->preservePhone($nohpRaw);
                    $toga = isset($row[35]) ? trim((string)$row[35]) : 'M';
                    if ($nim === '' && $nama === '') {
                        continue;
                    }
                    $extras = [];
                    foreach ($uniqueHeaders as $idx => $colName) {
                        $extras[$colName] = isset($row[$idx]) ? $row[$idx] : '';
                    }
                    $data[] = [
                        'nim' => $nim,
                        'nama_lengkap' => $nama,
                        'program_studi' => $prodi,
                        'fakultas' => '',
                        'ipk' => null,
                        'predikat' => '',
                        'email' => $email,
                        'no_hp' => $nohp,
                        'ukuran_toga' => ($toga !== '' ? $toga : 'M'),
                        'nomor_kursi' => $kursi,
                        'extra_data' => json_encode($extras, JSON_UNESCAPED_UNICODE)
                    ];
                }
            } else {
                foreach ($rows as $row) {
                    $nim = $this->getHeaderValue($row, $headerIndex, ['nim']);
                    $nama = $this->getHeaderValue($row, $headerIndex, ['namamhs', 'nama', 'namalengkap', 'nama_lengkap']);
                    $prodi = $this->getHeaderValue($row, $headerIndex, ['progdi', 'prodi', 'programstudi', 'program_studi']);
                    $fak = $this->getHeaderValue($row, $headerIndex, ['nm_fak', 'fakultas', 'nmfak']);
                    $ipkVal = $this->getHeaderValue($row, $headerIndex, ['ipk']);
                    $ipk = ($ipkVal === '' ? null : $ipkVal);
                    $predikat = $this->getHeaderValue($row, $headerIndex, ['predikat', 'pretdi']);
                    $email = $this->getHeaderValue($row, $headerIndex, ['email']);
                    $nohp = $this->preservePhone($this->getHeaderValue($row, $headerIndex, ['telepon', 'telp', 'nohp', 'no_hp', 'hp']));
                    $toga = $this->getHeaderValue($row, $headerIndex, ['toga', 'ukuran_toga', 'ukurantoga'], 'M');
                    $kursi = $this->getHeaderValue($row, $headerIndex, ['no_kursi', 'nokursi', 'nomor_kursi', 'kursi']);
                    if ($nim === '' && $nama === '') {
                        continue;
                    }
                    $extras = [];
                    foreach ($uniqueHeaders as $idx => $colName) {
                        $extras[$colName] = isset($row[$idx]) ? $row[$idx] : '';
                    }
                    $data[] = [
                        'nim' => $nim,
                        'nama_lengkap' => $nama,
                        'program_studi' => $prodi,
                        'fakultas' => $fak,
                        'ipk' => $ipk,
                        'predikat' => $predikat,
                        'email' => $email,
                        'no_hp' => $nohp,
                        'ukuran_toga' => ($toga !== '' ? $toga : 'M'),
                        'nomor_kursi' => $kursi,
                        'extra_data' => json_encode($extras, JSON_UNESCAPED_UNICODE)
                    ];
                }
            }
            
            $result = $this->wisudawanModel->importBatch($periodeId, $sesiId, $data);
            
            $this->activityLog->log(
                $_SESSION['user_id'], 
                'import_wisudawan', 
                "Import {$result['success']} wisudawan untuk sesi ID: {$sesiId}"
            );
            
            if ($result['success'] > 0) {
                $message = "Berhasil import {$result['success']} data wisudawan";
                if (count($result['errors']) > 0) {
                    $message .= ". " . count($result['errors']) . " data gagal diimport";
                }
                setFlash('success', $message);
            } else {
                setFlash('danger', 'Gagal import data wisudawan');
            }
            
            $this->redirect('wisudawan/index/' . $sesiId);
            
        } catch (Exception $e) {
            setFlash('danger', 'Error: ' . $e->getMessage());
            $this->redirect('wisudawan/upload/' . $sesiId);
        }
    }
    
    /**
     * Wisudawan detail page
     */
    public function detail($id, $periodeId) {
        $this->wisudawanModel->getTableByPeriode($periodeId);
        $wisudawan = $this->wisudawanModel->find($id);
        
        if (!$wisudawan) {
            setFlash('danger', 'Data wisudawan tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        $sesi = $this->sesiModel->find($wisudawan['sesi_id']);
        
        $data = [
            'title' => 'Detail Wisudawan - ' . $wisudawan['nama_lengkap'],
            'wisudawan' => $wisudawan,
            'sesi' => $sesi
        ];
        
        $this->view('admin/wisudawan/view', $data);
    }
    
    public function edit($id, $periodeId) {
        $this->wisudawanModel->getTableByPeriode($periodeId);
        $wisudawan = $this->wisudawanModel->find($id);
        if (!$wisudawan) {
            setFlash('danger', 'Data wisudawan tidak ditemukan');
            $this->redirect('admin/periode');
        }
        $sesi = $this->sesiModel->find($wisudawan['sesi_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nim = trim($this->post('nim'));
            $nama = trim($this->post('nama_lengkap'));
            $prodi = trim($this->post('program_studi'));
            $fak = trim($this->post('fakultas'));
            $ipk = trim($this->post('ipk'));
            $predikat = trim($this->post('predikat'));
            $email = trim($this->post('email'));
            $nohp = $this->preservePhone($this->post('no_hp'));
            $toga = strtoupper(trim($this->post('ukuran_toga')));
            $kursi = trim($this->post('nomor_kursi'));
            
            $allowedToga = ['S','M','L','XL','XXL'];
            if (!in_array($toga, $allowedToga, true)) { $toga = 'M'; }
            $ipkVal = ($ipk === '' ? null : $ipk);
            
            $ok = $this->wisudawanModel->update($id, [
                'nim' => $nim,
                'nama_lengkap' => $nama,
                'program_studi' => $prodi,
                'fakultas' => $fak,
                'ipk' => $ipkVal,
                'predikat' => $predikat,
                'email' => $email,
                'no_hp' => $nohp,
                'ukuran_toga' => $toga,
                'nomor_kursi' => $kursi,
            ]);
            
            if ($ok) {
                setFlash('success', 'Data berhasil diperbarui');
            } else {
                setFlash('danger', 'Gagal menyimpan perubahan');
            }
            $this->redirect('wisudawan/detail/' . $id . '/' . $periodeId);
        } else {
            $data = [
                'title' => 'Edit Wisudawan - ' . $wisudawan['nama_lengkap'],
                'wisudawan' => $wisudawan,
                'sesi' => $sesi,
                'periode_id' => $periodeId
            ];
            $this->view('admin/wisudawan/edit', $data);
        }
    }
    
    /**
     * Send WhatsApp message
     */
    public function sendWhatsApp($id, $periodeId) {
        $this->json(['success' => false, 'message' => 'Fitur WhatsApp telah dinonaktifkan'], 404);
    }

    private function preservePhone($val) {
        $s = is_null($val) ? '' : trim((string)$val);
        if ($s === '') return '';
        $digits = preg_replace('/[^0-9]/', '', $s);
        if ($digits === '') return '';
        if (strpos($digits, '0') === 0 || strpos($digits, '62') === 0) {
            return $digits;
        }
        return '0' . $digits;
    }

    private function normalizeHeader($str) {
        $s = strtolower((string)$str);
        $s = preg_replace('/[^a-z0-9]/', '', $s);
        return $s;
    }

    private function getHeaderValue($row, $headerIndex, $keys, $default = '') {
        foreach ($keys as $k) {
            $nk = $this->normalizeHeader($k);
            if (isset($headerIndex[$nk])) {
                $idx = $headerIndex[$nk];
                return isset($row[$idx]) ? trim((string)$row[$idx]) : $default;
            }
        }
        return $default;
    }

    private function shouldUseIndexMapping($headerIndex) {
        $needed = ['nim', 'namamhs', 'nama', 'namalengkap', 'nama_lengkap', 'progdi', 'prodi', 'programstudi', 'program_studi'];
        foreach ($needed as $k) {
            if (isset($headerIndex[$this->normalizeHeader($k)])) {
                return false;
            }
        }
        return true;
    }
}
