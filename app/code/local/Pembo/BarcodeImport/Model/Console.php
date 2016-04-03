<?php
class Pembo_BarcodeImport_Model_Console extends Mage_Core_Model_Abstract {

    public function importPendingAction() {
        return Mage::getModel('barcode_import/processor')->execute();
    }

    public function importPendingHelp() {
        return 'Imports pending barcodes into magento';
    }

}

