<?php

class Playbasis_Gamify_Block_Adminhtml_Gamify_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('gamify_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('gamify')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('gamify')->__('Item Information'),
          'title'     => Mage::helper('gamify')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('gamify/adminhtml_gamify_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}