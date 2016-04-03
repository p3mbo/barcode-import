<?php
class Pembo_BarcodeImport_Model_Resource_Pending extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct() {
        $this->_init('barcode_import/pending', 'entity_id');
    }
}

