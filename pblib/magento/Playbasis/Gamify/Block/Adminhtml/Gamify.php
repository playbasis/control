<?php
class Playbasis_Gamify_Block_Adminhtml_Gamify extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_gamify';
    $this->_blockGroup = 'gamify';
    $this->_headerText = Mage::helper('gamify')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('gamify')->__('Add Item');
    parent::__construct();
  }
}