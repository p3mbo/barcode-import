<?php
class Pembo_BarcodeImport_Model_Cron extends Mage_Core_Model_Abstract {

    public function execute() {
        return Mage::getModel('barcode_import/processor')->execute();
    }

}

