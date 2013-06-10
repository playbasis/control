<?php

class Playbasis_Gamify_Block_Adminhtml_Gamify_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('gamify_form', array('legend'=>Mage::helper('gamify')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('gamify')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('gamify')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('gamify')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('gamify')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('gamify')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('gamify')->__('Content'),
          'title'     => Mage::helper('gamify')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getGamifyData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGamifyData());
          Mage::getSingleton('adminhtml/session')->setGamifyData(null);
      } elseif ( Mage::registry('gamify_data') ) {
          $form->setValues(Mage::registry('gamify_data')->getData());
      }
      return parent::_prepareForm();
  }
}