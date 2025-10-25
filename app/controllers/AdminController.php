<?php
/**
 * Admin Controller
 */

class AdminController extends Controller {
    private $periodeModel;
    private $sesiModel;
    private $activityLog;
    
    public function __construct() {
        $this->requireRole('admin');
        $this->periodeModel = new PeriodeWisuda();
        $this->sesiModel = new SesiWisuda();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Dashboard
     */
    public function dashboard() {
        $data = [
            'title' => 'Dashboard Admin',
            'periodes' => $this->periodeModel->getAllWithStats(),
            'recent_logs' => $this->activityLog->getRecent(10)
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    /**
     * Manage Periode
     */
    public function periode() {
        $data = [
            'title' => 'Manajemen Periode Wisuda',
            'periodes' => $this->periodeModel->getAllWithStats()
        ];
        
        $this->view('admin/periode/index', $data);
    }
    
    /**
     * Create Periode
     */
    public function createPeriode() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreatePeriode();
        } else {
            $data = ['title' => 'Tambah Periode Wisuda'];
            $this->view('admin/periode/create', $data);
        }
    }
    
    /**
     * Handle create periode
     */
    private function handleCreatePeriode() {
        $namaPeriode = $this->post('nama_periode');
        $tahun = $this->post('tahun');
        $periodeKe = $this->post('periode_ke');
        $keterangan = $this->post('keterangan');
        
        // Validation
        if (empty($namaPeriode) || empty($tahun) || empty($periodeKe)) {
            setFlash('danger', 'Semua field wajib diisi');
            $this->redirect('admin/createPeriode');
        }
        
        $tablePrefix = $tahun . '_' . $periodeKe . '_t_wisudawan';
        
        if ($this->periodeModel->tablePrefixExists($tablePrefix)) {
            setFlash('danger', 'Periode dengan tahun dan periode ke ini sudah ada');
            $this->redirect('admin/createPeriode');
        }
        
        try {
            $periodeId = $this->periodeModel->createPeriode([
                'nama_periode' => $namaPeriode,
                'tahun' => $tahun,
                'periode_ke' => $periodeKe,
                'status' => 'draft',
                'keterangan' => $keterangan,
                'created_by' => $_SESSION['user_id']
            ]);
            
            if ($periodeId) {
                $this->activityLog->log(
                    $_SESSION['user_id'], 
                    'create_periode', 
                    "Membuat periode: {$namaPeriode}"
                );
                
                setFlash('success', 'Periode wisuda berhasil dibuat');
                $this->redirect('admin/periode');
            } else {
                setFlash('danger', 'Gagal membuat periode wisuda');
                $this->redirect('admin/createPeriode');
            }
        } catch (Exception $e) {
            setFlash('danger', 'Error: ' . $e->getMessage());
            $this->redirect('admin/createPeriode');
        }
    }
    
    /**
     * Edit Periode
     */
    public function editPeriode($id) {
        $periode = $this->periodeModel->find($id);
        
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditPeriode($id);
        } else {
            $data = [
                'title' => 'Edit Periode Wisuda',
                'periode' => $periode
            ];
            $this->view('admin/periode/edit', $data);
        }
    }
    
    /**
     * Handle edit periode
     */
    private function handleEditPeriode($id) {
        $namaPeriode = $this->post('nama_periode');
        $status = $this->post('status');
        $keterangan = $this->post('keterangan');
        
        if (empty($namaPeriode)) {
            setFlash('danger', 'Nama periode wajib diisi');
            $this->redirect('admin/editPeriode/' . $id);
        }
        
        $updated = $this->periodeModel->update($id, [
            'nama_periode' => $namaPeriode,
            'status' => $status,
            'keterangan' => $keterangan
        ]);
        
        if ($updated) {
            $this->activityLog->log(
                $_SESSION['user_id'], 
                'update_periode', 
                "Mengupdate periode ID: {$id}"
            );
            
            setFlash('success', 'Periode wisuda berhasil diupdate');
        } else {
            setFlash('danger', 'Gagal mengupdate periode wisuda');
        }
        
        $this->redirect('admin/periode');
    }
    
    /**
     * Delete Periode
     */
    public function deletePeriode($id) {
        try {
            $periode = $this->periodeModel->find($id);
            
            if (!$periode) {
                $this->json(['success' => false, 'message' => 'Periode tidak ditemukan'], 404);
            }
            
            if ($this->periodeModel->deletePeriode($id)) {
                $this->activityLog->log(
                    $_SESSION['user_id'], 
                    'delete_periode', 
                    "Menghapus periode: {$periode['nama_periode']}"
                );
                
                $this->json(['success' => true, 'message' => 'Periode berhasil dihapus']);
            } else {
                $this->json(['success' => false, 'message' => 'Gagal menghapus periode'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * View Periode Detail
     */
    public function viewPeriode($id) {
        $periode = $this->periodeModel->find($id);
        
        if (!$periode) {
            setFlash('danger', 'Periode tidak ditemukan');
            $this->redirect('admin/periode');
        }
        
        $sesiList = $this->sesiModel->getWithStats($id);
        
        $data = [
            'title' => 'Detail Periode - ' . $periode['nama_periode'],
            'periode' => $periode,
            'sesi_list' => $sesiList
        ];
        
        $this->view('admin/periode/view', $data);
    }
}
