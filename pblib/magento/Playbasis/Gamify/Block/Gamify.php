<?php
class Playbasis_Gamify_Block_Gamify extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getGamify()     
     { 
        if (!$this->hasData('gamify')) {
            $this->setData('gamify', Mage::registry('gamify'));
        }
        return $this->getData('gamify');
        
    }
}