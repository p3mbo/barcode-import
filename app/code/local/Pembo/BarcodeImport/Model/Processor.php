<?php
class Pembo_BarcodeImport_Model_Processor extends Mage_Core_Model_Abstract
{
    const BARCODE_FIELD = 'mpn';

    public function execute() {
        $collection = $this->_getCollection();

        /** @var Pembo_BarcodeImport_Model_Pending $pending */
        foreach($collection as $pending) {
            echo $pending->getSku();

            try {

                $product = $pending->getProduct();

                $product->setData(self::BARCODE_FIELD, $pending->getBarcode());
                $product->getResource()->saveAttribute($product, self::BARCODE_FIELD);

                echo ' .' . "\n";

                $pending->delete();
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
    }


    private function _getCollection() {
        return Mage::getModel('barcode_import/pending')->getCollection();
    }

}

