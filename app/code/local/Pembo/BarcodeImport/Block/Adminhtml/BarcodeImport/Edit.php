<?php

class Pembo_BarcodeImport_Block_Adminhtml_BarcodeImport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'barcode_import';
        $this->_controller = 'adminhtml_barcodeImport';
    }


    protected function _preparelayout()
    {
        return parent::_prepareLayout();
    }


    public function getHeaderText()
    {
        return Mage::helper('barcode_import')->__('Upload File');
    }
}
