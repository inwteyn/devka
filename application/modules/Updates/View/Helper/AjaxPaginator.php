<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Message.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_View_Helper_AjaxPaginator extends Zend_View_Helper_Abstract
{
  public function ajaxPaginator($items, $type)
  {
    $soonCampaigns = array();
    if ($type == 'schedule_paginator')
    {
      $timezone = Engine_Api::_()->getApi('settings', 'core')->__get('core.locale.timezone');
      $tmp_timezone = date_default_timezone_get();
      date_default_timezone_set($timezone);
      $from_date = date('Y-m-d H:i:s');
      $to_date = date('Y-m-d H:i:s', time()+2*24*60*60);
      date_default_timezone_set($tmp_timezone);

      $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');
      $select= $campaignTb->select()
        ->setIntegrityCheck(false)
        ->from($campaignTb->info('name'), array('campaign_id'))
        ->where('type=?', 'schedule')
        ->where('finished=?', 0)
        ->where('planned_date >?', $from_date)
        ->where('planned_date <?', $to_date)
        ->order('planned_date ASC');
      
      $soonCampaigns_array = $campaignTb->fetchAll($select)->toArray();
      foreach ($soonCampaigns_array as $campaign)
      {
        $soonCampaigns[]  = $campaign['campaign_id'];
      }
    }
    $data = array(
      'items'=>$items,
      'soonCampaigns' => $soonCampaigns,
      'type'=>$type,
      'timezone'=>Engine_Api::_()->getApi('settings', 'core')->__get('core.locale.timezone')
    );

	  return $this->view->partial('_ajax_paginator.tpl', 'updates', $data);
  }
}
