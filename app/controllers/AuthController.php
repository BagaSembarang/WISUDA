<?php
/**
 * Authentication Controller
 */

class AuthController extends Controller {
    private $userModel;
    private $activityLog;
    
    public function __construct() {
        $this->userModel = new User();
        $this->activityLog = new ActivityLog();
    }
    
    /**
     * Login page
     */
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->view('auth/login');
        }
    }
    
    /**
     * Handle login
     */
    private function handleLogin() {
        $username = $this->post('username');
        $password = $this->post('password');
        
        if (empty($username) || empty($password)) {
            setFlash('danger', 'Username dan password harus diisi');
            $this->redirect('auth/login');
        }
        
        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            setFlash('danger', 'Username atau password salah');
            $this->redirect('auth/login');
        }
        
        if (!$user['is_active']) {
            setFlash('danger', 'Akun Anda tidak aktif');
            $this->redirect('auth/login');
        }
        
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            setFlash('danger', 'Username atau password salah');
            $this->redirect('auth/login');
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in_at'] = time();
        
        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        // Log activity
        $this->activityLog->log($user['id'], 'login', 'User logged in');
        
        setFlash('success', 'Selamat datang, ' . $user['full_name']);
        
        // Redirect to dashboard
        header('Location: ' . BASE_URL . 'dashboard.php');
        exit;
    }
    
    /**
     * Logout
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->activityLog->log($_SESSION['user_id'], 'logout', 'User logged out');
        }
        
        session_destroy();
        setFlash('success', 'Anda telah keluar');
        $this->redirect('auth/login');
    }
}
