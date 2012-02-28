<?php
class Exceed_Orderexport_Model_Mysql4_Orderexport extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('orderexport/orderexport', 'orderexport_id');
    }   
}
?>