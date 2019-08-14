<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Data
 *
 * @author kryuch
 */
class Creovector_Sberbank_Helper_Data extends Mage_Payment_Helper_Data {

    function getParam($key) {
        $store = Mage::app()->getStore();
        $value = Mage::getStoreConfig("payment/crvsberbank_crvsberban/$key", $store);
        return ($value == "") ? Mage::getStoreConfig("payment/crvsberbank/$key", $store) : $value;
    }

    public function log($text, $title) {
        if ($this->getParam("debug")) {
            Mage::log($title, null, "crvsberbank");
            Mage::log("***************************", null, "crvsberbank");
            Mage::log($text, null, "crvsberbank");
            Mage::log("", null, "crvsberbank");
        }
    }

    public function createPdf($source, $filename) {
        require_once(dirname(__FILE__) . '/html2pdf.class.php');

        $pdf = new HTML2PDF('p', 'A4', 'en', true, 'UTF-8', 0);
        $pdf->addFont('Times', '', 'times.php');
        $pdf->WriteHTML($source);
        $pdf->Output($filename, "F");
    }

}
