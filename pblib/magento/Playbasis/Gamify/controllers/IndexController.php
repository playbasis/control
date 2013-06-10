<?php
class Playbasis_Gamify_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/gamify?id=15 
    	 *  or
    	 * http://site.com/gamify/id/15 	
    	 */
    	/* 
		$gamify_id = $this->getRequest()->getParam('id');

  		if($gamify_id != null && $gamify_id != '')	{
			$gamify = Mage::getModel('gamify/gamify')->load($gamify_id)->getData();
		} else {
			$gamify = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($gamify == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$gamifyTable = $resource->getTableName('gamify');
			
			$select = $read->select()
			   ->from($gamifyTable,array('gamify_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$gamify = $read->fetchRow($select);
		}
		Mage::register('gamify', $gamify);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}