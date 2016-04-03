<?php

class Pembo_BarcodeImport_Model_Pending extends Mage_Core_Model_Abstract
{

    public function _construct() {
        parent::_construct();
        $this->_init('barcode_import/pending');
    }

    public function getProduct() {
        $sku = $this->getSku();

        $product = Mage::getModel('catalog/product');
        $product->load($product->getIdBySku($sku));

        return $product;
    }


}