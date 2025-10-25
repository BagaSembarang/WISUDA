<?php
/**
 * Base Controller Class
 * Parent class untuk semua controller
 */

class Controller {
    
    /**
     * Load view
     */
    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: {$view}");
        }
    }
    
    /**
     * Load model
     */
    protected function model($model) {
        $modelFile = APP_PATH . '/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model not found: {$model}");
        }
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        if (empty($url)) {
            header('Location: ' . BASE_URL . 'index.php');
        } else {
            header('Location: ' . BASE_URL . 'index.php?url=' . ltrim($url, '/'));
        }
        exit;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Require login
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Check user role
     */
    protected function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Require specific role
     */
    protected function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            $this->redirect('error/forbidden');
        }
    }
    
    /**
     * Get POST data
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Get GET data
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed');
        }
    }
}
