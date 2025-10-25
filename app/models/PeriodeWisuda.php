<?php
/**
 * Periode Wisuda Model
 */

class PeriodeWisuda extends Model {
    protected $table = 'periode_wisuda';
    
    /**
     * Create new periode and generate table
     */
    public function createPeriode($data) {
        try {
            $this->beginTransaction();
            
            // Generate table prefix
            $tablePrefix = $data['tahun'] . '_' . $data['periode_ke'] . '_t_wisudawan';
            $data['table_prefix'] = $tablePrefix;
            
            // Insert periode
            $periodeId = $this->insert($data);
            
            if ($periodeId) {
                // Create dynamic table for wisudawan
                $this->createWisudawanTable($tablePrefix, $periodeId);
                $this->commit();
                return $periodeId;
            }
            
            $this->rollback();
            return false;
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Create wisudawan table dynamically
     */
    private function createWisudawanTable($tableName, $periodeId) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            periode_id INT NOT NULL,
            sesi_id INT NOT NULL,
            kode_unik VARCHAR(4) UNIQUE NOT NULL,
            nim VARCHAR(20) NOT NULL,
            nama_lengkap VARCHAR(100) NOT NULL,
            program_studi VARCHAR(100),
            fakultas VARCHAR(100),
            ipk DECIMAL(3,2),
            predikat VARCHAR(50),
            email VARCHAR(100),
            no_hp VARCHAR(20),
            ukuran_toga ENUM('S', 'M', 'L', 'XL', 'XXL'),
            nomor_kursi VARCHAR(20),
            extra_data TEXT,
            
            status_rsvp ENUM('pending', 'confirmed', 'declined') DEFAULT 'pending',
            rsvp_at TIMESTAMP NULL,
            
            presensi_toga_at TIMESTAMP NULL,
            presensi_toga_by INT,
            ttd_toga TEXT,
            keterangan_toga TEXT,
            
            presensi_gladi_at TIMESTAMP NULL,
            presensi_gladi_by INT,
            
            presensi_hadir_at TIMESTAMP NULL,
            presensi_hadir_by INT,
            ttd_hadir TEXT,
            keterangan_hadir TEXT,
            
            presensi_konsumsi_at TIMESTAMP NULL,
            presensi_konsumsi_by INT,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (periode_id) REFERENCES periode_wisuda(id) ON DELETE CASCADE,
            FOREIGN KEY (sesi_id) REFERENCES sesi_wisuda(id) ON DELETE CASCADE,
            INDEX idx_kode_unik (kode_unik),
            INDEX idx_nim (nim),
            INDEX idx_sesi (sesi_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        return $this->query($sql);
    }
    
    /**
     * Get all periode with sesi count
     */
    public function getAllWithStats() {
        return $this->queryAll("
            SELECT 
                p.*,
                COUNT(DISTINCT s.id) as jumlah_sesi,
                u.full_name as created_by_name
            FROM {$this->table} p
            LEFT JOIN sesi_wisuda s ON p.id = s.periode_id
            LEFT JOIN users u ON p.created_by = u.id
            GROUP BY p.id
            ORDER BY p.tahun DESC, p.periode_ke DESC
        ");
    }
    
    /**
     * Get active periode
     */
    public function getActive() {
        return $this->queryAll(
            "SELECT * FROM {$this->table} WHERE status = 'aktif' ORDER BY tahun DESC, periode_ke DESC"
        );
    }
    
    /**
     * Check if table prefix exists
     */
    public function tablePrefixExists($tablePrefix, $excludeId = null) {
        if ($excludeId) {
            $result = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE table_prefix = ? AND id != ?",
                [$tablePrefix, $excludeId]
            );
        } else {
            $result = $this->queryOne(
                "SELECT COUNT(*) as count FROM {$this->table} WHERE table_prefix = ?",
                [$tablePrefix]
            );
        }
        return $result['count'] > 0;
    }
    
    /**
     * Delete periode and its table
     */
    public function deletePeriode($id) {
        try {
            $periode = $this->find($id);
            if (!$periode) {
                return false;
            }
            
            $this->beginTransaction();
            
            // Drop wisudawan table
            $this->query("DROP TABLE IF EXISTS `{$periode['table_prefix']}`");
            
            // Delete periode
            $this->delete($id);
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
