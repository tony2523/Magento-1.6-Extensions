<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Exceed_Orderexport_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        /* $orders = Mage::getModel('orderexport/orderexport')->getCollection();
          foreach($orders as $order){
          var_dump($order);
          } */
        $status = array('complete', 'pending');
        $orderExport = Mage::getModel('orderexport/orderexport');
        $orders = $orderExport->retrieveOrders(null, null, $status);
        $id = $orderExport->getLastOrderEntityId($orders);
        echo $id ."\n";
        foreach ($orders as $order){
            var_dump($order);
        }
        $types = Mage::helper('orderexport/ftp')->connect();
            
        /*
        $rows = Mage::getModel('orderexport/orderexport')->prepareRowsForExport($orders, true, true, true, true, true);
        $file = Mage::getModel('orderexport/orderexport')->createCSV($rows);

        var_dump($rows);
        var_dump($file);
*/
        //$fields = Mage::getModel('orderexport/orderexport')->export($orders);
        //$file = Mage::getModel('orderexport/orderexport')->createCSV($fields);
        //   var_dump($file);

        /*
          foreach (Mage::getConfig()->getNode('global/sales/order/statuses')->children() as $status) {
          var_dump($status);
          } */
    }

}

?>
