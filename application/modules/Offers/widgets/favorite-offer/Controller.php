<?php
/**featur
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-09-25 12:05 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Widget_FavoriteOfferController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() != 'page') {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $offer = Engine_Api::_()->offers()->getSpecialOffer('favorite', $subject->getIdentity());

    if (!$offer) {
      return $this->setNoRender();
    }

    $this->view->offer = $offer;

  }
}