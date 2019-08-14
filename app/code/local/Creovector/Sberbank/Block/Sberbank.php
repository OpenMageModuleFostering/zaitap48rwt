<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Webxmore
 * @package     Webxmore_Pelecard
 * @copyright   Copyright (c) 2011 Webxmore 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Pelecard notification processor model
 */
class Creovector_Sberbank_Block_Sberbank extends Mage_Core_Block_Template {

    /**
     * Constructor. Set template.
     */
    var $order;

    protected function _construct() {
        parent::_construct();
        $this->order = Mage::getModel("sales/order")->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        $this->setTemplate('crvsberbank/sberbank.phtml');
    }

    public function getAddress() {
        $address = $this->order->getShippingAddress()->getData();
        return $address["postcode"] . ", " . $address["city"] . ", " . $address["street"];
    }

    public function getCustomerName() {
        $customer = ($this->order->getCustomerId() > 0) ? Mage::getModel('customer/customer')->load($this->order->getCustomerId()) : $this->order->getBillingAddress();
        return $customer->getName();
    }

    public function getOrderIncrementId() {
        return $this->order->getIncrementId();
    }

    public function getOrderTotal() {
        return Mage::helper('core')->currency($this->order->getGrandTotal());
    }

    public function getStoreName() {
        return Mage::getStoreConfig("general/store_information/name", Mage::app()->getStore());
    }

    public function getStorePhone() {
        return Mage::getStoreConfig("general/store_information/phone", Mage::app()->getStore());
    }

    public function getStoreEmail() {
        return Mage::getStoreConfig("trans_email/ident_general/email", Mage::app()->getStore());
    }

    public function getBankAccount($param) {
        return Mage::getStoreConfig("general/bankaccount/$param", Mage::app()->getStore());
    }

    public function getForm() {
        $order = $this->getOrderIncrementId();
        $block = $this->getLayout()->createBlock('core/template');
        $block->setData("content", $this->toHtml());
        $block->setData("order", $order);
        return $block->setTemplate('crvsberbank/receip.phtml')->toHtml();
    }

}
