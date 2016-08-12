<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminStatsController.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_AdminStatsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('updates_admin_main', array(), 'updates_admin_main_stats');


    //GLOBAL STATS
    $updatesTb = Engine_Api::_()->getDbtable('updates', 'updates');
    $linksTb = Engine_Api::_()->getDbtable('links', 'updates');
		$select = $updatesTb->select()
      ->setIntegrityCheck(false)
      ->from(array('u'=>$updatesTb->info('name')))
      ->joinLeft(array('l'=>$linksTb->info('name')), '`l`.id=u.update_id && l.type="updates"', array('SUM(l.referred_count) as referred'))
      ->group('u.update_id')
      ->order('creation_date DESC');

		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = $paginator->setCurrentPageNumber( $page );


    $rows = array();
    if( $paginator->getCurrentItemCount()>0)
    {
		 	$row_array = array();
		 	foreach( $paginator as $item )
		 	{
				$row_array[] = array($this->view->locale()->toDate($item["creation_date"], array('size'=>'long')), (int)$item['sent'], (int)$item['viewed'], (int)$item['referred']);
		 	}

		 	for ($i = count($row_array)-1; $i>=0; $i--)
		 	{
		 		$rows[] = $row_array[$i];
		 	}
    }

    if (count($rows) == 1)
    {
    	$rows[1] = $rows[0];
    }

		$this->view->rows = $rows;
    //END OF GLOBAL STATS

    
    //REFERRED STATS
   	$linksTb = Engine_Api::_()->getDbtable('links', 'updates');
 		$date = date('Y-m-d', strtotime('1/'.date('m/Y', time())));

 		$link_referreds['current'] = $linksTb->getReferredLinks($date)->toArray();
 		$link_referreds['all'] =  $linksTb->getReferredLinks()->toArray();
 		$this->view->link_referreds = $link_referreds;

		$total['current'] = $linksTb->getTotalReferreds($date)->referreds;
		$total['all'] = $linksTb->getTotalReferreds()->referreds;
		$this->view->total_referreds = $total;
    /**
     * @var $linksTb Updates_Model_DbTable_Links
     */

		$modules['current'] = $linksTb->getReferredModules($date)->toArray();
		$modules['all'] = $linksTb->getReferredModules()->toArray();

		foreach ($modules as $type=>$mods)
		{
			foreach ($mods as $key=>$mod)
			{
				$widget = Engine_Api::_()->getDbtable('widgets', 'updates')->getWidget(array('module'=>$mod['module']));
				$moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        $module = $moduleTb->getModule($widget->module);

        $module_referreds[$type][$key]['module_title'] = $module ? $module->title : '';
        $module_referreds[$type][$key]['module_referreds'] = $mod['referred_count'];
      }
    }

		$this->view->module_referreds = (isset($module_referreds))? $module_referreds:false;
    //END OF REFERRED STATS
  }

  public function campaignAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    	->getNavigation('updates_admin_main', array(), 'updates_admin_main_stats');
    
    //GLOBAL STATS
    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');
    $linksTb = Engine_Api::_()->getDbtable('links', 'updates');
		$select = $campaignTb->select()
      ->setIntegrityCheck(false)
      ->from(array('c'=>$campaignTb->info('name')))
      ->joinLeft(array('l'=>$linksTb->info('name')), 'l.id=c.campaign_id && l.type="campaign"', array('SUM(l.referred_count) as referred'))
      ->where('c.finished = ?', 1)
      ->group('c.campaign_id')
      ->order('creation_date DESC');


		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = $paginator->setCurrentPageNumber( $page );

    $rows = array();
    if( $paginator->getCurrentItemCount()>0)
    {
		 	$row_array = array();
		 	foreach( $paginator as $item )
		 	{
				$row_array[] = array($this->view->locale()->toDate($item["creation_date"], array('size'=>'long')), (int)$item['sent'], (int)$item['viewed'], (int)$item['referred']);
		 	}

		 	for ($i = count($row_array)-1; $i>=0; $i--)
		 	{
		 		$rows[] = $row_array[$i];
		 	}
    }

    if (count($rows) == 1)
    {
    	$rows[1] = $rows[0];
    }

		$this->view->rows = $rows;
    //END OF GLOBAL STATS

    //REFERRED STATS
   	$linksTb = Engine_Api::_()->getDbtable('links', 'updates');
 		$date = date('Y-m-d', strtotime('1/'.date('m/Y', time())));

 		$link_referreds['current'] = $linksTb->getReferredLinks($date, 'campaign')->toArray();
 		$link_referreds['all'] =  $linksTb->getReferredLinks(0, 'campaign')->toArray();
 		$this->view->link_referreds = $link_referreds;

		$total['current'] = $linksTb->getTotalReferreds($date, 'campaign')->referreds;
		$total['all'] = $linksTb->getTotalReferreds(0, 'campaign')->referreds;
		$this->view->total_referreds = $total;
    //END OF REFERRED STATS
  }
}