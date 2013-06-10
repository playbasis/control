<?php

class Playbasis_Gamify_Model_Mysql4_Gamify_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gamify/gamify');
    }
}