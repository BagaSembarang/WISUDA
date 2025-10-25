<?php
/**
 * Setting Kupon Model
 */

class SettingKupon extends Model {
    protected $table = 'setting_kupon';
    
    /**
     * Get by periode
     */
    public function getByPeriode($periodeId) {
        return $this->queryOne(
            "SELECT * FROM {$this->table} WHERE periode_id = ?",
            [$periodeId]
        );
    }
    
    /**
     * Save or update setting
     */
    public function saveSetting($periodeId, $data) {
        $existing = $this->getByPeriode($periodeId);
        
        $data['periode_id'] = $periodeId;
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}
