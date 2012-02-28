<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Exceed_Orderexport_Helper_Data extends Mage_Core_Helper_Abstract {

    const LastOrderEntityId = 'Exceed/Orderexport/last_exported_order_entity_id';
    const LastOrderId = 'Exceed/Orderexport/last_exported_order_id';
    
    /**
     *Save save config value into core_configdata
     * 
     * @param string $configPath
     * @param string $configValue 
     */
    public function saveConfig($configPath,$configValue) {
        Mage::getConfig()->saveConfig($configPath, $configValue);
    }
    
    /**
     *get config value from core_configdata
     * 
     * @param string $configPath
     * @return string 
     */
    public function getConfig($configPath) {
        return Mage::getStoreConfig($configPath);
    }
}

?>
