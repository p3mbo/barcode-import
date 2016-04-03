<?php

class Pembo_BarcodeImport_Block_Adminhtml_BarcodeImport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('importtabs_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('barcode_import')->__('Data'));
    }
}
