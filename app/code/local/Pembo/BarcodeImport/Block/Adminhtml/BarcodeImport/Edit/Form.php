<?php
class Pembo_BarcodeImport_Block_Adminhtml_BarcodeImport_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getActionUrl(),
            'method' => 'post',
            'enctype'	=> 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getActionUrl()
    {
        return $this->getUrl('adminhtml/barcodeImport_index/save');
    }

}
