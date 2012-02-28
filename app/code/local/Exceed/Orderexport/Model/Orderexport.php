<?php

class Exceed_Orderexport_Model_Orderexport extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('orderexport/orderexport');
    }

    public function retrieveOrders($startEndId = null, $startEndDate = null, $orderStatusCollection = null, $orderIdCollection = NULL) {
        Mage::log("retrieve order start!!!!!");
        $orders = Mage::getModel('sales/order')->getCollection();

        //add start/end order id to filter
        if ($startEndId) {
            $idStartEndCondition = $this->getStartEndCondition($startEndId);
            if ($idStartEndCondition) {
                $orders->addFieldToFilter('entity_id', $idStartEndCondition);
            }
        }

        //add start/end order date to filter
        if ($startEndDate) {
            $dateStartEndCondition = $this->getStartEndCondition($startEndDate);
            if ($dateStartEndCondition) {
                $orders->addFieldToFilter('created_at', $dateStartEndCondition);
            }
        }

        //add order status to filter
        if ($orderStatusCollection) {
            $orders->addFieldToFilter('status', array('in' => $orderStatusCollection));
        }

        //add order id collection to filter->to export specific orders
        if ($orderIdCollection) {
            $orders->addFieldToFilter('increment_id', array('in' => $orderIdCollection));
        }

        //  $orders->addAttributeToSelect('increment_id');

        return $orders;
    }

    protected function getStartEndCondition($startEndArray) {
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

    public function getLastOrderEntityId($orders) {
        $lastOrderId = 0;
        foreach ($orders as $order) {
            if ($order->getEntityId() > $lastOrderId) {
                $lastOrderId = $order->getEntityId();
            } 
        }
     
        return $lastOrderId;
    }

    public function prepareRowsForExport($orders, $getBasic, $getBilling, $getShipping, $getGift, $getItems) {
        $header = $this->getHeader($getBasic, $getBilling, $getShipping, $getGift, $getItems);
        $rows = $this->getRows($orders, $getBasic, $getBilling, $getShipping, $getGift, $getItems);
        array_unshift($rows, $header);
        return $rows;
    }

    public function createCSV($rows) {
        $fileName = Mage::getBaseDir('export') . '/' . 'export.csv';
        /** var/export * */
        $fp = fopen($fileName, 'w');

        foreach ($rows as $field) {
            fputcsv($fp, $field, ',', '"');
        }
        fclose($fp);
        return $fileName;
    }

    //<editor-fold defaultstate="collapsed" desc="Get Headers">
    //return headers
    protected function getHeader($getBasic, $getBilling, $getShipping, $getGift, $getItems) {
        $header = array();
        if ($getBasic) {
            $header = array_merge($header, $this->getBasicHeader());
        }
        if ($getBilling) {
            $header = array_merge($header, $this->getBillingHeader());
        }
        if ($getShipping) {
            $header = array_merge($header, $this->getShippingHeader());
        }
        if ($getGift) {
            $header = array_merge($header, $this->getGiftHeader());
        }
        if ($getItems) {
            $header = array_merge($header, $this->getItemsHeader());
        }
        return $header;
    }

    protected function getBasicHeader() {
        return array(
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
    }

    protected function getBillingHeader() {
        return array(
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
    }

    protected function getShippingHeader() {
        return array(
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
    }

    protected function getGiftHeader() {
        return array(
            'Order Gift Message'
        );
    }

    protected function getItemsHeader() {
        return array(
            'Item Name',
            'Item Status',
            'Item SKU',
            'Item Original Price',
            'Item Price',
            'Item Qty Ordered',
            'Item Qty Invoiced',
            'Item Qty Shipped',
            'Item Qty Canceled',
            'Item Qty Refunded',
        );
    }

// </editor-fold>
    //<editor-fold defaultstate="collapsed" desc="Get Row">
    //get all the rows for the csv file
    public function getRows($orders, $getBasic, $getBilling, $getShipping, $getGift, $getItems) {
        if ($getItems) {
            $rows = $this->getRowsWithItems($orders, $getBasic, $getBilling, $getShipping, $getGift);
        } else {
            $rows = $this->getRowsNoItems($orders, $getBasic, $getBilling, $getShipping, $getGift);
        }
        return $rows;
    }

    protected function getRowsNoItems($orders, $getBasic, $getBilling, $getShipping, $getGift) {
        $rows = array();
        foreach ($orders as $order) {
            $rows[] = $this->getRowNoItems($order, $getBasic, $getBilling, $getShipping, $getGift);
        }
        return $rows;
    }

    protected function getRowsWithItems($orders, $getBasic, $getBilling, $getShipping, $getGift) {
        $rows = array();
        foreach ($orders as $order) {
            $itemRows = $this->getRowWithItems($order, $getBasic, $getBilling, $getShipping, $getGift);
            foreach ($itemRows as $itemRow) {
                $rows[] = $itemRow;
            }
        }
        return $rows;
    }

    protected function getRowNoItems($order, $getBasic, $getBilling, $getShipping, $getGift) {

        //represents a row in the csv file
        $row = array();

        if ($getBasic) {
            $row = array_merge($row, $this->getBasicInfo($order));
        }

        if ($getBilling) {
            $row = array_merge($row, $this->getBillingInfo($order));
        }

        if ($getShipping) {
            $row = array_merge($row, $this->getShippingInfo($order));
        }

        if ($getGift) {
            $row = array_merge($row, $this->getGiftMessageInfo($order));
        }
        return $row;
    }

    protected function getRowWithItems($order, $getBasic, $getBilling, $getShipping, $getGift) {
        $itemRows = array();
        $row = $this->getRowNoItems($order, $getBasic, $getBilling, $getShipping, $getGift);
        $itemsInfo = $this->getItemsInfo($order);
        echo "count:" . count($itemsInfo);
        foreach ($itemsInfo as $itemInfo) {
            //assign the merged row to new virable to avoid having multiple items in one row( ie appending new item to existing row)
            $rowItem = array_merge($row, $itemInfo);
            $itemRows[] = $rowItem;
        }
        return $itemRows;
    }

    protected function getBasicInfo($order) {
        return array(
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
    }

    protected function getBillingInfo($order) {
        //get billingAddress Object
        $billingAddress = $order->getBillingAddress();
        return array(
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
    }

    protected function getShippingInfo($order) {
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        return array(
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
    }

    protected function getGiftMessageInfo($order) {
        $giftMessageId = $order->getGiftMessageId();
        $giftMessage = Mage::getModel('giftmessage/message')->load($giftMessageId);
        return array(
            $giftMessage ? $giftMessage->getMessage() : ''
        );
    }

    protected function getItemsInfo($order) {
//TODO: take care of configurable,bundle, and grouped products.
//TODO: get more fields if possible
        $itemsInfo = array();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            $itemInfo = array(
                $item->getName(),
                $item->getStatus(),
                $item->getSku(),
                $item->getOriginalPrice(),
                $item->getPrice(),
                $item->getQtyOrdered(),
                $item->getQtyInvoiced(),
                $item->getQtyShipped(),
                $item->getQtyCanceled(),
                $item->getQtyRefunded()
            );
            $itemsInfo[] = $itemInfo;
        }
        return $itemsInfo;
    }

// </editor-fold>
}

?>
