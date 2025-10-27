<?php
/**
 * LO (Liaison Officer) Controller
 */

class LOController extends Controller {
    private $wisudawanModel;
    private $sesiModel;
    private $periodeModel;
    
    public function __construct() {
        $this->wisudawanModel = $this->model('Wisudawan');
        $this->sesiModel = $this->model('SesiWisuda');
        $this->periodeModel = $this->model('PeriodeWisuda');
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
    
    public function overview($periodeId, $sesiId = null) {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('lo/dashboard');
        }
        $sesiList = $this->sesiModel->getByPeriode($periodeId);
        if (empty($sesiList)) {
            $data = [
                'title' => 'Ringkasan Sesi',
                'periode' => $periode,
                'sesi' => null,
                'sesi_list' => [],
                'stats' => ['total'=>0,'presensi_hadir'=>0],
            ];
            $this->view('lo/overview', $data);
            return;
        }
        $selectedId = $sesiId ? (int)$sesiId : (int)$sesiList[0]['id'];
        $sesi = $this->sesiModel->find($selectedId);
        $stats = $this->wisudawanModel->getStatsBySesi($selectedId, $periodeId);
        $tidakHadir = max(0, (int)($stats['total'] ?? 0) - (int)($stats['presensi_hadir'] ?? 0));
        $denahModel = $this->model('DenahKursi');
        $denah = $denahModel->getBySesi($selectedId);
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
        $wis = $this->wisudawanModel->getBySesi($selectedId, $periodeId);
        foreach ($wis as $item) {
            if (!empty($item['nomor_kursi']) && !empty($item['presensi_hadir_at'])) {
                $occupied[$item['nomor_kursi']] = true;
            }
        }
        $data = [
            'title' => 'Ringkasan Sesi',
            'periode' => $periode,
            'sesi' => $sesi,
            'sesi_list' => $sesiList,
            'stats' => $stats,
            'tidak_hadir' => $tidakHadir,
            'rows' => $rows,
            'max_col' => $maxCol,
            'grid' => $grid,
            'occupied' => $occupied
        ];
        $this->view('lo/overview', $data);
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

    public function denahPeriode($periodeId, $sesiId = null) {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('lo/dashboard');
        }
        $sesiList = $this->sesiModel->getByPeriode($periodeId);
        if (empty($sesiList)) {
            $data = [
                'title' => 'Denah Periode',
                'periode' => $periode,
                'sesi_list' => [],
                'rows' => [],
                'max_col' => 0,
                'grid' => [],
                'occupied' => [],
                'sesi' => ['id' => null, 'nama_sesi' => '-']
            ];
            $this->view('lo/denah_grid', $data);
            return;
        }
        $selectedId = $sesiId ? (int)$sesiId : (int)$sesiList[0]['id'];
        $sesi = $this->sesiModel->find($selectedId);
        $denahModel = new DenahKursi();
        $denah = $denahModel->getBySesi($selectedId);
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
        $wis = $this->wisudawanModel->getBySesi($selectedId, $periodeId);
        foreach ($wis as $item) {
            if (!empty($item['nomor_kursi']) && !empty($item['presensi_hadir_at'])) {
                $occupied[$item['nomor_kursi']] = true;
            }
        }
        $data = [
            'title' => 'Denah Periode',
            'periode' => $periode,
            'sesi' => $sesi,
            'sesi_list' => $sesiList,
            'rows' => $rows,
            'max_col' => $maxCol,
            'grid' => $grid,
            'occupied' => $occupied
        ];
        $this->view('lo/denah_grid', $data);
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
