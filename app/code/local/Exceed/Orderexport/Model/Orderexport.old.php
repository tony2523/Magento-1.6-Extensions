<?php

class Exceed_Orderexport_Model_Orderexport extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('orderexport/orderexport');
    }

    public function retrieveOrders($startEndId = null, $startEndDate=null, $orderStatusCollection = null,$orderIdCollection =NULL) {
        Mage::log("retrieve order start!!!!!");
        $orders = Mage::getModel('sales/order')->getCollection();
        
        //add start/end order id to filter
        if($startEndId){
            $idStartEndCondition =$this->getStartEndCondition($startEndId);
            if($idStartEndCondition){
                $orders->addFieldToFilter('increment_id',$idStartEndCondition);
            }
        }
        
            if($startEndDate){
            $dateStartEndCondition =$this->getStartEndCondition($startEndDate);
            if($dateStartEndCondition){
                $orders->addFieldToFilter('created_at',$dateStartEndCondition);
            }
        }
            

        //add order status to filter
        if ($orderStatusCollection) {
            $orders->addFieldToFilter('order_status', array('in' => $orderStatusCollection));
        }
        
        if($orderIdCollection){
            $orders->addFieldToFilter('increment_id',  array('in'=>$orderIdCollection));
        }

        //add id
        $orderIdCondition = '';


        if ($startOrderId && $endOrderId) {
            $orderIdCondition = array('from' => $startOrderId, 'to' => $endOrderId);
        } elseif ($startOrderId) {
            $orderIdCondition = array('from' => $startOrderId);
        } elseif ($endOrderId) {
            $orderIdCondition = array('to' => $endOrderId);
        }

        if ($orderIdCondition) {
            $orders->addFieldToFilter('increment_id', $orderIdCondition);
        }

        return $orders;
    }
    
    protected function getStartEndCondition($startEndArray){
        $start = $startEndArray['Start'];
        $end = $startEndArray['End'];
         $startEndCondition = '';

        if ($start && $endOrderId) {
            $startEndCondition = array('from' => $start, 'to' => $end);
        } elseif ($startOrderId) {
            $startEndCondition = array('from' => $start);
        } elseif ($endOrderId) {
            $startEndCondition = array('to' => $end);
        }
        
        return $startEndCondition;
        
    }




    /* @TODO: refactor this crappy code!!!! */
    public function export($orders) {
        $count = 0;
        $fields = array();
        foreach ($orders as $order) {
            if (!$count) {
                $fields[] = $this->getHeaders($order, true, true, true, true, true);

                $items = $order->getAllItems();
                foreach ($items as $item) {
                    
                    $itemValues = array(
                        $item->getName()
                    );
                    $otherValues = $this->getValues($order, true, true, TRUE, true, true);
                    $otherValues = array_merge($otherValues, $itemValues);
                    $fields[] = $otherValues;
                }
                $count++;
            } else {
                $items = $order->getAllItems();
                foreach ($items as $item) {
                    $itemValues = array(
                        $item->getName()
                    );
                    $otherValues = $this->getValues($order, true, true, TRUE, true, true);
                    $otherValues = array_merge($otherValues, $itemValues);
                    $fields[] = $otherValues;
                }
            }
        }
        return $fields;
    }

    //export csv
    //export xml
    //get csv headers
    protected function getHeaders($order, $getBasic = true, $getBilling = false, $getShipping = false, $getItem = false, $getGiftMessage = false) {
        $headers = array();
        if ($getBasic) {
            $basicHeaders = array(
                'Order Id',
                'Order Date',
                'Order Status',
                'Store Id',
                'Website Name',
                'Store Name',
                'Store View Name',
                'Store Currency Code',
                'Shipping Method',
                'Payment Method',
                'Order Currency Code',
                'Subtotal',
                'Tax Amount',
                'Shipping Amount',
                'Discount Amount',
                'Grand Total',
                'Total Paid',
                'Total Refunded',
                'Base Currency Code',
                'Base Subtotal',
                'Base Tax Amount',
                'Base Shipping Amount',
                'Base Discount Amount',
                'Base Grand Total',
                'Base Total Paid',
                'Base Total Refunded',
                'Customer Id',
                'Customer Name',
                'Customer Email'
            );
            $headers = array_merge($headers, $basicHeaders);
        }
        //billing header
        if ($getBilling) {
            $billingHeaders = array(
                'Billing Name',
                'Billing Company',
                'Billing Street1',
                'Billing Street2',
                'Billing City',
                'Billing Region',
                'Billing Country Code',
                'Billing Country',
                'Billing Postcode',
                'Billing Telephone'
            );
            $headers = array_merge($headers, $billingHeaders);
        }
        //shipping header
        if ($getShipping) {
            $shippingHeaders = array(
                'Shipping Name',
                'Shipping Company',
                'Shipping Street1',
                'Shipping Street2',
                'Shipping City',
                'Shipping Region',
                'Shipping Country Code',
                'Shipping Country',
                'Shipping Postcode',
                'Shipping Telephone'
            );
            $headers = array_merge($headers, $shippingHeaders);
        }
        if ($getGiftMessage) {
            array_push($headers, 'Gift Message');
        }
        if ($getItem) {
            array_push($headers, 'Item Name');
        }

        return $headers;
    }

    //get values
    public function getValues($order, $getBasic = true, $getBilling = false, $getShipping = false, $getItem = false, $getGiftMessage = false) {
        $values = array();

        //get basic values
        if ($getBasic) {
            $basicInfo = array(
                $order->getRealOrderId(),
                $order->getCreatedAtFormated('short'),
                $order->getStatus(),
                $order->getStoreId(),
                $order->getStoreName(0),
                $order->getStoreName(1),
                $order->getStoreName(2),
                $order->getStoreCurrencyCode(),
                $order->getShippingMethod(),
                $order->getPayment()->getMethod(),
                $order->getOrderCurrencyCode(),
                $order->getSubtotal(),
                $order->getTaxAmount(),
                $order->getShippingAmount(),
                $order->getDiscountAmount(),
                $order->getGrandTotal(),
                $order->getTotalPaid(),
                $order->getTotalRefunded(),
                //Base values
                $order->getBaseCurrencyCode(),
                $order->getBaseSubtotal(),
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount(),
                $order->getBaseDiscountAmount(),
                $order->getBaseGrandTotal(),
                $order->getBaseTotalPaid(),
                $order->getBaseTotalRefunded(),
                //Customer
                $order->getCustomerId(),
                $order->getCustomerName(),
                $order->getCustomerEmail()
            );
            $values = array_merge($values, $basicInfo);
        }

        //get billing info
        if ($getBilling) {
            $billingAddress = $order->getBillingAddress();

            $billingInfo = array(
                $billingAddress ? $billingAddress->getName() : '',
                $billingAddress ? $billingAddress->getCompany() : '',
                $billingAddress ? $billingAddress->getStreet1() : '',
                $billingAddress ? $billingAddress->getStreet2() : '',
                $billingAddress ? $billingAddress->getCity() : '',
                $billingAddress ? $billingAddress->getRegion() : '',
                $billingAddress ? $billingAddress->getCountry() : '',
                $billingAddress ? $billingAddress->getCountryModel()->getName() : '',
                $billingAddress ? $billingAddress->getPostcode() : '',
                $billingAddress ? $billingAddress->getTelephone() : ''
            );
            $values = array_merge($values, $billingInfo);
        }

        //get shipping info
        if ($getShipping) {
            $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
            $shippingInfo = array(
                $shippingAddress ? $shippingAddress->getName() : '',
                $shippingAddress ? $shippingAddress->getCompany() : '',
                $shippingAddress ? $shippingAddress->getStreet1() : '',
                $shippingAddress ? $shippingAddress->getStreet2() : '',
                $shippingAddress ? $shippingAddress->getCity() : '',
                $shippingAddress ? $shippingAddress->getRegion() : '',
                $shippingAddress ? $shippingAddress->getCountry() : '',
                $shippingAddress ? $shippingAddress->getCountryModel()->getName() : '',
                $shippingAddress ? $shippingAddress->getPostcode() : '',
                $shippingAddress ? $shippingAddress->getTelephone() : ''
            );
            $values = array_merge($values, $shippingInfo);
        }

        if ($getGiftMessage) {
            $giftMessageId = $order->getGiftMessageId();
          //  if ($giftMessageId) {
                $giftMessage = Mage::getModel('giftmessage/message')->load($order->getGiftMessageId());
                $giftMessageInfo=array(
                    $giftMessage ? $giftMessage->getMessage() :'' 
                );
     $values = array_merge($values, $giftMessageInfo);
          //  }
        }

        return $values;
    }

    public function createCSV($fields) {
        $fileName = 'export.csv';
        /** var/export * */
        $fp = fopen(Mage::getBaseDir('export') . '/' . $fileName, 'w');

        foreach ($fields as $field) {
            fputcsv($fp, $field, ',', '"');
        }
        fclose($fp);
        return Mage::getBaseDir('export');
    }

}

?>