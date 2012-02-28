<?php
class Magentotutorial_Weblog_Model_Mysql4_Blogpost extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('weblog/blogpost', 'blogpost_id');
    }   
}
?>