<?php

class Playbasis_Gamify_Block_Adminhtml_Gamify_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gamify';
        $this->_controller = 'adminhtml_gamify';
        
        $this->_updateButton('save', 'label', Mage::helper('gamify')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('gamify')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('gamify_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'gamify_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'gamify_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('gamify_data') && Mage::registry('gamify_data')->getId() ) {
            return Mage::helper('gamify')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('gamify_data')->getTitle()));
        } else {
            return Mage::helper('gamify')->__('Add Item');
        }
    }
}