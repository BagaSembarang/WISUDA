<?php
/**
 * Activity Log Model
 */

class ActivityLog extends Model {
    protected $table = 'activity_logs';
    
    /**
     * Log activity
     */
    public function log($userId, $action, $description = '') {
        return $this->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    /**
     * Get recent logs
     */
    public function getRecent($limit = 50) {
        return $this->queryAll("
            SELECT 
                l.*,
                u.full_name,
                u.username
            FROM {$this->table} l
            LEFT JOIN users u ON l.user_id = u.id
            ORDER BY l.created_at DESC
            LIMIT ?
        ", [$limit]);
    }
    
    /**
     * Get logs by user
     */
    public function getByUser($userId, $limit = 50) {
        return $this->queryAll("
            SELECT * FROM {$this->table}
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ", [$userId, $limit]);
    }
    
    /**
     * Get logs by date range
     */
    public function getByDateRange($startDate, $endDate) {
        return $this->queryAll("
            SELECT 
                l.*,
                u.full_name,
                u.username
            FROM {$this->table} l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE DATE(l.created_at) BETWEEN ? AND ?
            ORDER BY l.created_at DESC
        ", [$startDate, $endDate]);
    }
}
