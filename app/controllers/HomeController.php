<?php
/**
 * Home Controller
 */

class HomeController extends Controller {
    
    public function index() {
        // Redirect based on role
        if ($this->isLoggedIn()) {
            $role = $_SESSION['role'] ?? '';
            
            if ($role === 'admin') {
                $this->redirect('admin/dashboard');
            } elseif ($role === 'lo') {
                $this->redirect('lo/dashboard');
            } else {
                $this->redirect('auth/logout');
            }
        } else {
            $this->redirect('auth/login');
        }
    }
}
