<?php

class Creovector_Sberbank_IndexController extends Mage_Core_Controller_Front_Action {

    function indexAction() {
        $filename = $this->getRequest()->getParam("folder")."/".$this->getRequest()->getParam("file");
        $file = $_SERVER["DOCUMENT_ROOT"]."/var/".$filename;
        if (file_exists($file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            readfile($file);
            exit;
        }
    }

}
