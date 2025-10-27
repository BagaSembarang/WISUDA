<?php
class UserController extends Controller {
    private $userModel;
    private $activityLog;

    public function __construct() {
        $this->requireRole('admin');
        $this->userModel = new User();
        $this->activityLog = new ActivityLog();
    }

    public function index() {
        $users = $this->userModel->all('id DESC');
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $users
        ];
        $this->view('admin/user/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            $data = [
                'title' => 'Tambah Pengguna'
            ];
            $this->view('admin/user/create', $data);
        }
    }

    private function handleCreate() {
        $username = trim($this->post('username'));
        $password = trim($this->post('password'));
        $fullName = trim($this->post('full_name'));
        $email = trim($this->post('email'));
        $role = $this->post('role');
        $isActive = $this->post('is_active') ? 1 : 0;

        if ($username === '' || $password === '' || $fullName === '' || ($role !== 'admin' && $role !== 'lo')) {
            setFlash('danger', 'Data tidak lengkap');
            $this->redirect('user/create');
        }
        if ($this->userModel->usernameExists($username)) {
            setFlash('danger', 'Username sudah dipakai');
            $this->redirect('user/create');
        }
        $hash = $this->userModel->hashPassword($password);
        $id = $this->userModel->insert([
            'username' => $username,
            'password' => $hash,
            'full_name' => $fullName,
            'email' => $email,
            'role' => $role,
            'is_active' => $isActive
        ]);
        if ($id) {
            $this->activityLog->log($_SESSION['user_id'], 'create_user', 'Tambah user: ' . $username);
            setFlash('success', 'Pengguna berhasil ditambahkan');
            $this->redirect('user/index');
        } else {
            setFlash('danger', 'Gagal menambah pengguna');
            $this->redirect('user/create');
        }
    }

    public function edit($id) {
        $user = $this->userModel->find($id);
        if (!$user) {
            setFlash('danger', 'User tidak ditemukan');
            $this->redirect('user/index');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id, $user);
        } else {
            $data = [
                'title' => 'Edit Pengguna',
                'user' => $user
            ];
            $this->view('admin/user/edit', $data);
        }
    }

    private function handleEdit($id, $user) {
        $username = trim($this->post('username'));
        $password = trim($this->post('password'));
        $fullName = trim($this->post('full_name'));
        $email = trim($this->post('email'));
        $role = $this->post('role');
        $isActive = $this->post('is_active') ? 1 : 0;

        if ($username === '' || $fullName === '' || ($role !== 'admin' && $role !== 'lo')) {
            setFlash('danger', 'Data tidak lengkap');
            $this->redirect('user/edit/' . $id);
        }
        if ($username !== $user['username'] && $this->userModel->usernameExists($username)) {
            setFlash('danger', 'Username sudah dipakai');
            $this->redirect('user/edit/' . $id);
        }
        $data = [
            'username' => $username,
            'full_name' => $fullName,
            'email' => $email,
            'role' => $role,
            'is_active' => $isActive
        ];
        if ($password !== '') {
            $data['password'] = $this->userModel->hashPassword($password);
        }
        $ok = $this->userModel->update($id, $data);
        if ($ok) {
            $this->activityLog->log($_SESSION['user_id'], 'update_user', 'Edit user ID: ' . $id);
            setFlash('success', 'Pengguna berhasil diperbarui');
        } else {
            setFlash('danger', 'Gagal memperbarui pengguna');
        }
        $this->redirect('user/index');
    }

    public function delete($id) {
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }
        if ((int)$user['id'] === (int)($_SESSION['user_id'] ?? 0)) {
            $this->json(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri'], 400);
        }
        if ($this->userModel->delete($id)) {
            $this->activityLog->log($_SESSION['user_id'], 'delete_user', 'Hapus user ID: ' . $id);
            $this->json(['success' => true, 'message' => 'User dihapus']);
        } else {
            $this->json(['success' => false, 'message' => 'Gagal menghapus user'], 500);
        }
    }
}
