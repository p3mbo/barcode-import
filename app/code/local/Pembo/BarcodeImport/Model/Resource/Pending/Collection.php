<?php
class Pembo_BarcodeImport_Model_Resource_Pending_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct() {
        $this->_init('barcode_import/pending');
    }
}

