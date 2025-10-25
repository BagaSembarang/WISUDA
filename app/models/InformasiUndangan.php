<?php
/**
 * Informasi Undangan Model
 */

class InformasiUndangan extends Model {
    protected $table = 'informasi_undangan';
    
    /**
     * Get by periode
     */
    public function getByPeriode($periodeId) {
        return $this->queryAll(
            "SELECT * FROM {$this->table} WHERE periode_id = ? AND is_active = 1 ORDER BY urutan",
            [$periodeId]
        );
    }
    
    /**
     * Reorder informasi
     */
    public function reorder($periodeId, $orders) {
        $this->beginTransaction();
        
        try {
            foreach ($orders as $id => $urutan) {
                $this->query(
                    "UPDATE {$this->table} SET urutan = ? WHERE id = ? AND periode_id = ?",
                    [$urutan, $id, $periodeId]
                );
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
}
