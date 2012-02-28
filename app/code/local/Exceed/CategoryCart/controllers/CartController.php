<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Shopping cart controller
 */
include_once("Mage/Checkout/controllers/CartController.php");  
class Exceed_CategoryCart_CartController extends Mage_Checkout_CartController
{


    /*Modified to accepts quatities correctly */
    public function addproductsAction()
    {
        //ewrite("Enteres addproducts");
        $orderItemIds=array(); //comma seperated list of ids for filter
        $orderItems=array();   //id => qty
        
        foreach($this->getRequest()->getParams() as $key => $value)
        {
            if(preg_match('/^qty_/',$key ))
            {
                $productId = substr($key,4);
                if(is_numeric($productId))
                {
                    $orderItemIds[] = $productId;
                    $orderItems[$productId] = $value;
                }
            }
        }
         
//ewrite("Got through request with ".count($orderItems)." items in orderItems");
        if (is_array($orderItemIds)) {
//ewrite("Yep its an array");           
            /*$itemsCollection = Mage::getModel('sales/order_item')
            ->getCollection()
            ->addIdFilter($orderItemIds)
            ->load();*/
            /* @var $itemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection */
            $cart = $this->_getCart();
            foreach ($orderItems as $itemId => $qty) {
//ewrite("We have an items id of $itemId and a qty of $qty");                
                try {
                 
                    $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($itemId);
//ewrite("Product ID says: ".$product->getId());                
                    $cart->addProduct($product, $qty);
//ewrite("Got passed the addProdct");

                }
                catch (Mage_Core_Exception $e) {
//ewrite("Ooops on ".__LINE__." ".$e);                   
                    if ($this->_getSession()->getUseNotice(true)) {
                        $this->_getSession()->addNotice($e->getMessage());
                    } else {
                        $this->_getSession()->addError($e->getMessage());
                    }
                }
                catch (Exception $e) {
//ewrite("Ooops on ".__LINE__." ".$e);                   
                    $this->_getSession()->addException($e, $this->__('Can not add item to shopping cart'));
                    $this->_goBack();
                    return;
                }
            }
            $cart->save();
//ewrite("Cart Saved");
            $this->_getSession()->setCartWasUpdated(true);
        }
        // EXCEED DEBUG - Arshiya //
        $backUrl = $this->_getRefererUrl();
        $redirectURL = Mage::getSingleton('core/session')->getData('currentUrl');

        $message = $this->__('The selected item(s) have been added to your cart. <a href="'.Mage::getBaseUrl().'checkout/cart">Proceed to checkout</a> or continue shopping');

        if (!$this->_getSession()->getNoCartRedirect(true)) {
            $this->_getSession()->addSuccess($message);
            //ewrite("Success message added, now going back");
            // EXCEED DEBUG - Arshiya //
            $this->_redirectUrl($redirectURL);          
            //$this->_goBack();
            return;
        }
        else
        {
            //ewrite("Normal Go back");
            $this->_goBack();
            return; 
        }
        
        //ewrite("OOOOOPS should get here so far");
        return;
    } 

 
}
