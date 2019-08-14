<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Process
 *
 * @author kryuch
 */
class Creovector_Sberbank_ProcessingController extends Mage_Core_Controller_Front_Action {

    function indexAction() {
        $block = $this->getLayout()->createBlock('core/template')->setTemplate('crvsberbank/index.phtml');

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Iframe page which submits the payment data to Pelecard.
     */
    public function payAction() {
        $folder = Mage::getBaseDir("var") . DS . "bank";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        Mage::helper("crvsberbank")->log(print_r($this->getRequest()->getParams(), true), "From frontend");
        $receip = $this->getLayout()->createBlock('crvsberbank/sberbank');
        $pdf = $_SERVER["DOCUMENT_ROOT"] . "/var/bank/" . $receip->getOrderIncrementId() . ".pdf";
        Mage::helper("crvsberbank")->createPdf($receip->toHtml(), $pdf);
        $this->_redirect("checkout/onepage/success");
    }

    /**
     * Action to which the customer will be returned when the payment is made.
     */
    public function successAction() {

        Mage::helper("crvsberbank")->log(print_r($this->getRequest()->getParams(), true), "Success");
        try {

            Mage::helper("crvsberbank")->createInvoice();

            $this->_redirect('checkout/onepage/success');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return mixed
     */
    public function cancelAction($errorMsg = '') {
        $session = $this->_getCheckout();

        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {

                if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
                    $order->registerCancellation($errorMsg)->save();
                }
                $quote = Mage::getModel('sales/quote')
                        ->load($order->getQuoteId());
                if ($quote->getId()) {
                    $quote->setIsActive(1)
                            ->setReservedOrderId(NULL)
                            ->save();
                    $session->replaceQuote($quote);
                }
                $session->unsLastRealOrderId();
            }
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * Action to which the transaction details will be posted after the payment
     * process is complete.
     */
    public function errorAction() {
        Mage::helper("crvsberbank")->log(print_r($this->getRequest()->getParams(), true), "Error");
        $result = $this->getRequest()->getParams();

        $session = $this->_getCheckout();
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                //Cancel order
                if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
                    $order->registerCancellation($result['result'])->save();
                }
                $quote = Mage::getModel('sales/quote')
                        ->load($order->getQuoteId());
                //Return quote
                if ($quote->getId()) {
                    $quote->setIsActive(1)
                            ->setReservedOrderId(NULL)
                            ->save();
                    $session->replaceQuote($quote);
                }
                //Unset data
                $session->unsLastRealOrderId();
            }
        }
        Mage::getSingleton('core/session')->addError('ההזמנה נכשלה עם שגיאה מספר ' . substr($result['result'], 0, 3) . ' אנא פנה למוקד שרות הלקוחות במידה וההזמנה נכשלת שנית');

        $this->_redirect('checkout/cart');
    }

    protected function _redirect($path, $arguments = array()) {
        $block = $this->getLayout()->createBlock('core/template')->setTemplate('crvsberbank/redirect.phtml');
        Mage::getSingleton('core/session')->setRedirectUrl(Mage::getUrl($path, $arguments)); // In the Controller
        $this->getResponse()->setBody($block->toHtml());
    }

}
