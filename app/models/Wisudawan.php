<?php
/**
 * Wisudawan Model - Dynamic Table Handler
 */

class Wisudawan extends Model {
    private $tableName;
    private $periodeId;
    
    /**
     * Set table name dynamically based on periode
     */
    public function setTable($tableName) {
        $this->tableName = $tableName;
        $this->table = $tableName;
    }
    
    /**
     * Get table name from periode
     */
    public function getTableByPeriode($periodeId) {
        $result = $this->queryOne(
            "SELECT table_prefix FROM periode_wisuda WHERE id = ?",
            [$periodeId]
        );
        
        if ($result) {
            $this->setTable($result['table_prefix']);
            $this->periodeId = $periodeId;
            return $result['table_prefix'];
        }
        
        return null;
    }
    
    /**
     * Import wisudawan from array (Excel data)
     */
    public function importBatch($periodeId, $sesiId, $data) {
        $this->getTableByPeriode($periodeId);
        
        if (!$this->tableName) {
            throw new Exception("Tabel wisudawan tidak ditemukan untuk periode ini");
        }
        
        $this->ensureExtraDataColumn();

        $imported = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                // Generate unique code
                $kodeUnik = $this->generateUniqueCode();
                
                $wisudawanData = [
                    'periode_id' => $periodeId,
                    'sesi_id' => $sesiId,
                    'kode_unik' => $kodeUnik,
                    'nim' => $row['nim'] ?? '',
                    'nama_lengkap' => $row['nama_lengkap'] ?? '',
                    'program_studi' => $row['program_studi'] ?? '',
                    'fakultas' => $row['fakultas'] ?? '',
                    'ipk' => $row['ipk'] ?? null,
                    'predikat' => $row['predikat'] ?? '',
                    'email' => $row['email'] ?? '',
                    'no_hp' => $row['no_hp'] ?? '',
                    'ukuran_toga' => $row['ukuran_toga'] ?? 'M',
                    'nomor_kursi' => $row['nomor_kursi'] ?? '',
                    'extra_data' => $row['extra_data'] ?? null
                ];
                
                if ($this->insert($wisudawanData)) {
                    $imported++;
                } else {
                    $errors[] = "Baris " . ($index + 1) . ": Gagal menyimpan data";
                }
                
            } catch (Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        return [
            'success' => $imported,
            'errors' => $errors
        ];
    }
    private function ensureExtraDataColumn() {
        try {
            $col = $this->queryOne("SHOW COLUMNS FROM `{$this->tableName}` LIKE 'extra_data'");
            if (!$col) {
                $this->query("ALTER TABLE `{$this->tableName}` ADD COLUMN extra_data TEXT NULL AFTER nomor_kursi");
            }
        } catch (Exception $e) {
        }
    }
    
    /**
     * Generate unique 4-digit code
     */
    private function generateUniqueCode() {
        $maxAttempts = 100;
        $attempt = 0;
        
        do {
            $code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $exists = $this->queryOne(
                "SELECT COUNT(*) as count FROM `{$this->tableName}` WHERE kode_unik = ?",
                [$code]
            );
            $attempt++;
        } while ($exists['count'] > 0 && $attempt < $maxAttempts);
        
        if ($attempt >= $maxAttempts) {
            throw new Exception("Gagal generate kode unik setelah {$maxAttempts} percobaan");
        }
        
        return $code;
    }
    
    /**
     * Find by kode unik
     */
    public function findByKode($kodeUnik, $periodeId = null) {
        if ($periodeId) {
            $this->getTableByPeriode($periodeId);
        }
        
        if (!$this->tableName) {
            return null;
        }
        
        return $this->queryOne(
            "SELECT * FROM `{$this->tableName}` WHERE kode_unik = ?",
            [$kodeUnik]
        );
    }
    
    /**
     * Get wisudawan by sesi with details
     */
    public function getBySesi($sesiId, $periodeId) {
        $this->getTableByPeriode($periodeId);
        
        return $this->queryAll("
            SELECT 
                w.*,
                s.nama_sesi,
                s.tanggal,
                s.waktu_mulai,
                s.lokasi
            FROM `{$this->tableName}` w
            JOIN sesi_wisuda s ON w.sesi_id = s.id
            WHERE w.sesi_id = ?
            ORDER BY w.id ASC
        ", [$sesiId]);
    }
    
    /**
     * Update RSVP status
     */
    public function updateRSVP($id, $status) {
        return $this->query(
            "UPDATE `{$this->tableName}` SET status_rsvp = ?, rsvp_at = NOW() WHERE id = ?",
            [$status, $id]
        );
    }
    
    /**
     * Update presensi toga
     */
    public function updatePresensiToga($id, $userId, $ttd, $keterangan = '') {
        return $this->query(
            "UPDATE `{$this->tableName}` 
            SET presensi_toga_at = NOW(), 
                presensi_toga_by = ?,
                ttd_toga = ?,
                keterangan_toga = ?
            WHERE id = ?",
            [$userId, $ttd, $keterangan, $id]
        );
    }
    
    /**
     * Update presensi gladi
     */
    public function updatePresensiGladi($id, $userId) {
        return $this->query(
            "UPDATE `{$this->tableName}` 
            SET presensi_gladi_at = NOW(), 
                presensi_gladi_by = ?
            WHERE id = ?",
            [$userId, $id]
        );
    }
    
    /**
     * Update presensi hadir (hari-H)
     */
    public function updatePresensiHadir($id, $userId, $ttd, $keterangan = '') {
        return $this->query(
            "UPDATE `{$this->tableName}` 
            SET presensi_hadir_at = NOW(), 
                presensi_hadir_by = ?,
                ttd_hadir = ?,
                keterangan_hadir = ?
            WHERE id = ?",
            [$userId, $ttd, $keterangan, $id]
        );
    }
    
    /**
     * Update presensi konsumsi
     */
    public function updatePresensiKonsumsi($id, $userId) {
        return $this->query(
            "UPDATE `{$this->tableName}` 
            SET presensi_konsumsi_at = NOW(), 
                presensi_konsumsi_by = ?
            WHERE id = ?",
            [$userId, $id]
        );
    }
    
    /**
     * Get statistics by sesi
     */
    public function getStatsBySesi($sesiId, $periodeId) {
        $this->getTableByPeriode($periodeId);
        
        return $this->queryOne("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status_rsvp = 'confirmed' THEN 1 ELSE 0 END) as rsvp_confirmed,
                SUM(CASE WHEN status_rsvp = 'declined' THEN 1 ELSE 0 END) as rsvp_declined,
                SUM(CASE WHEN presensi_toga_at IS NOT NULL THEN 1 ELSE 0 END) as presensi_toga,
                SUM(CASE WHEN presensi_gladi_at IS NOT NULL THEN 1 ELSE 0 END) as presensi_gladi,
                SUM(CASE WHEN presensi_hadir_at IS NOT NULL THEN 1 ELSE 0 END) as presensi_hadir,
                SUM(CASE WHEN presensi_konsumsi_at IS NOT NULL THEN 1 ELSE 0 END) as presensi_konsumsi
            FROM `{$this->tableName}`
            WHERE sesi_id = ?
        ", [$sesiId]);
    }
    
    /**
     * Get denah kehadiran (yang sudah presensi hari-H)
     */
    public function getDenahKehadiran($sesiId, $periodeId) {
        $this->getTableByPeriode($periodeId);
        
        return $this->queryAll("
            SELECT 
                nomor_kursi,
                nama_lengkap,
                nim,
                program_studi,
                presensi_hadir_at,
                CASE WHEN presensi_hadir_at IS NOT NULL THEN 'hadir' ELSE 'belum' END as status_kehadiran
            FROM `{$this->tableName}`
            WHERE sesi_id = ?
            ORDER BY nomor_kursi
        ", [$sesiId]);
    }
}
