<?php
/**
 * Sesi Wisuda Model
 */

class SesiWisuda extends Model {
    protected $table = 'sesi_wisuda';
    
    /**
     * Get sesi by periode
     */
    public function getByPeriode($periodeId) {
        return $this->queryAll(
            "SELECT * FROM {$this->table} WHERE periode_id = ? ORDER BY tanggal, waktu_mulai",
            [$periodeId]
        );
    }
    
    /**
     * Get sesi with periode info
     */
    public function getWithPeriode($id) {
        return $this->queryOne("
            SELECT 
                s.*,
                p.nama_periode,
                p.tahun,
                p.table_prefix
            FROM {$this->table} s
            JOIN periode_wisuda p ON s.periode_id = p.id
            WHERE s.id = ?
        ", [$id]);
    }
    
    /**
     * Get sesi with stats
     */
    public function getWithStats($periodeId) {
        $periode = $this->queryOne("SELECT table_prefix FROM periode_wisuda WHERE id = ?", [$periodeId]);
        
        if (!$periode) {
            return [];
        }
        
        $tableName = $periode['table_prefix'];
        
        return $this->queryAll("
            SELECT 
                s.*,
                COUNT(w.id) as total_wisudawan,
                SUM(CASE WHEN w.status_rsvp = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count,
                SUM(CASE WHEN w.presensi_hadir_at IS NOT NULL THEN 1 ELSE 0 END) as hadir_count
            FROM {$this->table} s
            LEFT JOIN `{$tableName}` w ON s.id = w.sesi_id
            WHERE s.periode_id = ?
            GROUP BY s.id
            ORDER BY s.tanggal, s.waktu_mulai
        ", [$periodeId]);
    }
    
    /**
     * Check if sesi has wisudawan
     */
    public function hasWisudawan($sesiId) {
        $sesi = $this->getWithPeriode($sesiId);
        if (!$sesi) {
            return false;
        }
        
        $tableName = $sesi['table_prefix'];
        $result = $this->queryOne(
            "SELECT COUNT(*) as count FROM `{$tableName}` WHERE sesi_id = ?",
            [$sesiId]
        );
        
        return $result['count'] > 0;
    }

    /**
     * Ensure lokasi_iframe column exists
     */
    public function ensureIframeColumn() {
        try {
            $col = $this->queryOne("SHOW COLUMNS FROM `{$this->table}` LIKE 'lokasi_iframe'");
            if (!$col) {
                $this->query("ALTER TABLE `{$this->table}` ADD COLUMN lokasi_iframe TEXT NULL AFTER lokasi");
            }
        } catch (Exception $e) {
        }
    }
}
