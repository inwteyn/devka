<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 17.10.13
 * Time: 8:53
 */

class SocialBoost_Api_Core extends Core_Api_Abstract
{
  protected $_moduleTbl;

  public function getPopupType()
  {
    if($this->isOfferAllowed())
      return 'offers';

    if($this->isCreditAllowed())
      return 'credit';

    return 'standard';
  }

  public function isOfferAllowed()
  {
    $modulesTbl = $this->getModuleTbl();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if( !$modulesTbl->isModuleEnabled('offers') ) {
      return false;
    }

    if( !$settings->getSetting('socialboost.admin.offers', 0) ) {
      return false;
    }

    if( !$settings->getSetting('socialboost.offer.id', 0) ) {
      return false;
    }

    if( !$this->checkReward() ) {
      return false;
    }

    $offer = Engine_Api::_()->getItem('offer', $settings->getSetting('socialboost.offer.id'), 0);
    if( !$offer->getIdentity() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if( $offer->isSubscribed($viewer) ) {
      return false;
    }

    return $offer;
  }

  public function isCreditAllowed()
  {
    $modulesTbl = $this->getModuleTbl();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if( !$modulesTbl->isModuleEnabled('credit') ) {
      return false;
    }

    if( !$settings->getSetting('socialboost.admin.credit', 0) ) {
      return false;
    }

    if( !$settings->getSetting('socialboost.credit.amount', 0) ) {
      return false;
    }


    if( !$this->checkReward() ) {
      return false;
    }

    return $settings->getSetting('socialboost.credit.amount', 0);
  }

  public function isSubscribeAllowed()
  {
    $modulesTbl = $this->getModuleTbl();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    if( !$modulesTbl->isModuleEnabled('updates') ) {
      return false;
    }

    if( !$settings->getSetting('socialboost.admin.newsletter', 0) ) {
      return false;
    }

    return true;
  }

  public function getModuleTbl()
  {
    if(!$this->_moduleTbl) {
      $this->_moduleTbl = Engine_Api::_()->getDbTable('modules', 'core');
    }

    return $this->_moduleTbl;
  }

  public function checkReward()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    /**
     * @var $dataTbl SocialBoost_Model_DbTable_Datas
     * @var $settings Core_Model_DbTable_Settings
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $params = array(
      'reward' => 1
    );

    $dataTbl = Engine_Api::_()->getDbTable('datas', 'socialBoost');
    if( $dataTbl->getUsersData($viewer, $params)->count() && $settings->getSetting('socialboost.admin.reward', 1)) {
      return false;
    }
    return true;

  }
}