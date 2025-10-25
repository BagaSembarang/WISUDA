<?php
/**
 * User Model
 */

class User extends Model {
    protected $table = 'users';
    
    /**
     * Find user by username
     */
    public function findByUsername($username) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE username = ?",
            [$username]
        );
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Hash password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Get active users by role
     */
    public function getByRole($role) {
        return $this->queryAll(
            "SELECT * FROM {$this->table} WHERE role = ? AND is_active = 1 ORDER BY full_name",
            [$role]
        );
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin($userId) {
        return $this->query(
            "UPDATE {$this->table} SET updated_at = NOW() WHERE id = ?",
            [$userId]
        );
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        if ($excludeId) {
            $result = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ? AND id != ?",
                [$username, $excludeId]
            );
        } else {
            $result = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?",
                [$username]
            );
        }
        return $result['count'] > 0;
    }
}
