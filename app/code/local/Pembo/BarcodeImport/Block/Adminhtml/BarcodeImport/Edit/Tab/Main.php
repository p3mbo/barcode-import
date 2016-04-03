<?php

class Pembo_BarcodeImport_Block_Adminhtml_BarcodeImport_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('barcodeimport_upload_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>Mage::helper('barcode_import')->__('General'), 'class'=>'fieldset-wide')
        );



		$fieldset->addField('csvfile', 'file', array(
			'label'     => Mage::helper('barcode_import')->__('Select a CSV File'),
			'required'  => false,
			'name'      => 'csvfile',
		));



        //$form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    public function getTabLabel()
    {
        return Mage::helper('barcode_import')->__('Upload File');
    }


    public function getTabTitle()
    {
        return Mage::helper('barcode_import')->__('Upload File');
    }


    public function canShowTab()
    {
        return true;
    }


    public function isHidden()
    {
        return false;
    }


    protected function _isAllowedAction($action)
    {
        return true;
    }
}
