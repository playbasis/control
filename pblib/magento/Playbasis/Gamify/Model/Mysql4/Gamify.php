<?php

class Playbasis_Gamify_Model_Mysql4_Gamify extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the gamify_id refers to the key field in your database table.
        $this->_init('gamify/gamify', 'gamify_id');
    }
}