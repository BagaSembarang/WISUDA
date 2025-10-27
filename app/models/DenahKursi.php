<?php
/**
 * Denah Kursi Model
 */

class DenahKursi extends Model {
    protected $table = 'denah_kursi';
    
    /**
     * Get denah by sesi
     */
    public function getBySesi($sesiId) {
        return $this->queryAll(
            "SELECT * FROM {$this->table} WHERE sesi_id = ? ORDER BY baris, kolom",
            [$sesiId]
        );
    }
    
    /**
     * Generate denah kursi otomatis
     */
    public function generateDenah($sesiId, $jumlahBaris, $jumlahKolom, $zona = 'Utama') {
        $this->beginTransaction();
        
        try {
            // Delete existing denah
            $this->query("DELETE FROM {$this->table} WHERE sesi_id = ?", [$sesiId]);
            
            $counter = 1;
            for ($baris = 1; $baris <= $jumlahBaris; $baris++) {
                for ($kolom = 1; $kolom <= $jumlahKolom; $kolom++) {
                    $nomorKursi = str_pad($counter, 3, '0', STR_PAD_LEFT);
                    
                    $this->insert([
                        'sesi_id' => $sesiId,
                        'nomor_kursi' => $nomorKursi,
                        'baris' => chr(64 + $baris), // A, B, C, ...
                        'kolom' => $kolom,
                        'zona' => $zona,
                        'status' => 'tersedia'
                    ]);
                    
                    $counter++;
                }
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Update status kursi
     */
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Get available seats
     */
    public function getAvailableSeats($sesiId) {
        return $this->queryAll(
            "SELECT * FROM {$this->table} WHERE sesi_id = ? AND status = 'tersedia' ORDER BY nomor_kursi",
            [$sesiId]
        );
    }
    public function replaceForSesi($sesiId, $seats) {
        $this->beginTransaction();
        try {
            $this->query("DELETE FROM {$this->table} WHERE sesi_id = ?", [$sesiId]);
            foreach ($seats as $s) {
                $nomor = isset($s['nomor_kursi']) ? $s['nomor_kursi'] : (isset($s['nomor']) ? $s['nomor'] : '');
                $baris = isset($s['baris']) ? $s['baris'] : '';
                $kolom = isset($s['kolom']) ? (int)$s['kolom'] : 0;
                if ($nomor === '' || $baris === '' || $kolom <= 0) {
                    continue;
                }
                $this->insert([
                    'sesi_id' => $sesiId,
                    'nomor_kursi' => $nomor,
                    'baris' => $baris,
                    'kolom' => $kolom,
                    'zona' => isset($s['zona']) ? $s['zona'] : 'Utama',
                    'status' => 'tersedia'
                ]);
            }
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}

