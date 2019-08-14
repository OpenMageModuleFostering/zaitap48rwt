<?php

class Creovector_Sberbank_Model_Sberbank extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'crvsberbank_sberbank';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_defaultLocale = 'ru';
    protected $_supportedLocales = array('en');
    protected $_hidelogin = '1';
    protected $_order;
    protected $_paymentMethod = 'SBERBANK';

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('crvsberbank/processing/pay');
    }

    public function isInitializeNeeded() {
        return true;
    }

    public function initialize($paymentAction, $stateObject) {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }

    public function getConfigPaymentAction() {
        $paymentAction = $this->getConfigData('payment_action');
        return empty($paymentAction) ? true : $paymentAction;
    }

    public function getOrder() {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }
        return $this->_order;
    }

}
