<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 08.12.10
 * Time: 10:30
 * To change this template use File | Settings | File Templates.
 */
 
class Updates_Model_Template extends Core_Model_Item_Abstract
{
  protected $_type = 'updates_template';
  
  public function hasCampaign($template_id = null)
  {
    $template_id = ($template_id == null)? $this->template_id : $template_id;
    
    $campaignTb = Engine_Api::_()->getDbtable('campaigns', 'updates');
    $select = $campaignTb->select()
      ->where('finished = ?', 0)
      ->where('template_id = ?', $template_id);

    if (null !== ($campaign = $campaignTb->fetchRow($select)))
    {
      return $campaign;
    }

    return false;
  }
}