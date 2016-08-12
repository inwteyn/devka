<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 06.09.12 17:48 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Widget_OfferDetailsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject('offer')) {
      return $this->setNoRender();
    }

    /**
     * @var $offer Offers_Model_Offer
     * @var $modules Hecore_Model_DbTable_Modules
     */
    $modules = Engine_Api::_()->getDbTable('modules', 'hecore');
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->offer = $offer = Engine_Api::_()->core()->getSubject();

    $this->view->canAccept = Engine_Api::_()->getDbTable('permissions', 'authorization')->isAllowed('offer', $viewer, 'accept');

    $this->view->offerPhotos = $offer->getCollectiblesPaginator();
    $this->view->isSubscribed = $offer->isSubscribed($viewer);
    $page = null;

    if ($modules->isModuleEnabled('page')) {
      $page = Engine_Api::_()->getItem('page', $offer->page_id);
      $this->view->page = $page;
    }

    $this->view->products = $products = $offer->getProductsStore($offer->getIdentity());
    $requireIsComplete = true;

    if ($offer->getOfferType() == 'reward' || $offer->getOfferType() == 'store') {
      $this->view->requires = $requires = $offer->getRequire();
      $this->view->require_complete = array();
      if ($viewer->getIdentity()) {
        $this->view->require_complete = $require_complete = Engine_Api::_()->getDbTable('require', 'offers')->getCompleteRequireIds($viewer, $offer, $offer->page_id);
        foreach ($requires as $item) {
          if (!in_array($item->getIdentity(), $require_complete)) {
            $requireIsComplete = false;
            break;
          }
        }
      }
    }

    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $this->view->time_left = Engine_Api::_()->offers()->availableOffer($offer, true);
    $this->view->requireIsComplete = $requireIsComplete;
    $this->view->checkTimeLeft = $offer->checkTime($offer->endtime);
    $this->view->checkTimeRedeem = $offer->checkTime($offer->redeem_endtime);
  }
}