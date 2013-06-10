<?php

class Playbasis_Gamify_Model_Gamify extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gamify/gamify');
    }
}