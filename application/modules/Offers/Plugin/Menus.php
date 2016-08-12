<?php

class Offers_Plugin_Menus
{

  public function onMenuInitialize_OffersMainManage()
  {

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_OffersUpcoming()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label' => 'OFFERS_upcoming_offers',
      'href' => $subject->getHref().'/content/offers/',
      'onClick' => 'Offers.list("upcoming"); return false;',
      'route' => 'offers_upcoming',
    );
  }

  public function onMenuInitialize_OffersPast()
  {
    return array(
      'label' => 'OFFERS_past_offers',
      'href' => 'javascript:void(0);',
      'onClick' => 'Offers.list("past");',
      'route' => 'offers_page',
    );
  }

  public function onMenuInitialize_OffersMine()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $isAllowedPost = $subject->authorization()->isAllowed($viewer, 'posting');

    if ($isAllowedPost){
      return array(
        'label' => 'OFFERS_manage_offers',
        'href' => 'javascript:void(0)',
        'onClick' => 'Offers.list("manage");',
        'route' => 'offers_page',
      );
    }

    return false;
  }

  public function onMenuInitialize_OffersCreate($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if ($subject->getOwner()->getIdentity() == $viewer_id) {
      return array(
        'label' => 'OFFERS_create_offer',
        'href' => 'javascript:void(0);',
        'onClick' => 'Offers.goForm();',
        'route' => 'offers_page'
      );
    }

    return false;
  }

  public function onMenuInitialize_OfferProfileEdit($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($viewer->getIdentity() <= 0 || !$subject) {
       return false;
    } else if (($viewer->getIdentity() == $subject->owner_id) || $viewer->isAdmin()) {
      return array(
        'label' => 'OFFERS_edit_offer',
        'icon' => 'application/modules/Offers/externals/images/edit.png',
        'route' => 'offers_specific',
        'params' => array(
          'action' => 'edit',
          'offer_id' => $subject->getIdentity()
        )
      );
    }
  }

  public function onMenuInitialize_OfferProfileDelete($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() <= 0 || !$subject) {
      return false;
    } else if (($viewer->getIdentity() == $subject->owner_id) || $viewer->isAdmin()) {
      return array(
        'label' => 'OFFERS_delete_offer',
        'icon' => 'application/modules/Offers/externals/images/delete.png',
        'class' => 'smoothbox',
        'route' => 'offers_specific',
        'params' => array(
          'action' => 'delete',
          'offer_id' => $subject->getIdentity()
        )
      );
    }
  }

  public function onMenuInitialize_OfferProfileShare($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$subject || $subject->getType() !== 'offer') {
      return false;
    }

    if ($viewer->getIdentity()) {
      return array(
        'label' => 'OFFERS_share_offer',
        'icon' => 'application/modules/Offers/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
          'module' => 'activity',
          'controller' => 'index',
          'action' => 'share',
          'type' => $subject->getType(),
          'id' => $subject->getIdentity(),
          'format' => 'smoothbox',
        ),
      );
    }
  }

  public function onMenuInitialize_OfferProfileFollow($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$subject || $subject->getType() !== 'offer') {
      return false;
    }
    if ($subject->getOfferType() !== 'reward' && $subject->getOfferType() !== 'store') {
      return false;
    }

    $followsTbl = Engine_Api::_()->getDbTable('follows', 'offers');

    $user = Engine_Api::_()->user()->getUser($viewer->getIdentity());
    $offer = $subject;
    $requires = $offer->getRequire();
    $require_complete = Engine_Api::_()->getDbTable('require', 'offers')->getCompleteRequireIds($user, $offer, $offer->page_id);
    $requireIsComplete = true;

    foreach ($requires as $item) {
      if (!in_array($item->getIdentity(), $require_complete)) {
        $requireIsComplete = false;
        break;
      }
    }
    if ($requireIsComplete) {
      return false;
    }

    $followStatus = $followsTbl->getFollowStatus($subject->offer_id, $viewer->getIdentity());

    $label = 'OFFERS_FOLLOW_Follow_Offer';
    if ($followStatus == 'active') {
      $label = 'OFFERS_FOLLOW_Followed';
    }
    elseif($followStatus == 'finished') {
      return false;
    }

    if ($viewer->getIdentity()) {
      return array(
        'label' => $label,
        'icon' => 'application/modules/Offers/externals/images/follow.png',
        'class' => 'smoothbox',
        'route' => 'offers_specific',
        'params' => array(
          'action' => 'follow',
          'offer_id' => $subject->getIdentity(),
          'follow_status' => $followStatus
        )
      );
    }
  }

  public function onMenuInitialize_OfferProfileFavorite($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$subject || $subject->getType() !== 'offer') {
      return false;
    }
    if (!$subject->page_id) {
      return false;
    }
    if ($subject->owner_id != $viewer->getIdentity()) {
      return false;
    }

    $currentTime = strtotime(Engine_Api::_()->offers()->getDatetime());
    $endTime = strtotime($subject->endtime);
    if ($subject->time_limit == 'limit' && $currentTime >= $endTime) {
      return false;
    }
    $favoriteStatus = Engine_Api::_()->getDbTable('offers', 'offers')->getOfferById($subject->offer_id)->favorite;

    if ($favoriteStatus) {
      $label = 'OFFERS_Make As Simple';
      $icon = 'application/modules/Offers/externals/images/non-favorite.png';
    }
    else {
      $label = 'OFFERS_Make As Favorite';
      $icon = 'application/modules/Offers/externals/images/favorite.png';
    }

    if ($viewer->getIdentity()) {
      return array(
        'label' => $label,
        'icon' => $icon,
        'class' => 'smoothbox',
        'route' => 'offers_specific',
        'params' => array(
          'action' => 'favorite',
          'offer_id' => $subject->getIdentity(),
          'format' => 'smoothbox',
        ),
      );
    }
  }

  public function onMenuInitialize_EditOffer()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label' => 'OFFERS_edit_offer',
      'route' => 'offers_specific',
      'icon' => 'application/modules/Offers/externals/images/edit.png',
      'params' => array(
        'action' => 'edit',
        'offer_id' => $subject->getIdentity()
      )
    );
  }

  public function onMenuInitialize_EditContacts()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label' => 'OFFERS_edit_contacts',
      'route' => 'offers_specific',
      'icon' => 'application/modules/Offers/externals/images/edit_contacts.png',
      'class' => 'edit_offer_menu',
      'params' => array(
        'action' => 'edit-contacts',
        'offer_id' => $subject->getIdentity()
      )
    );
  }

  public function onMenuInitialize_OfferManagePhotos()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label' => 'OFFERS_manage_photos',
      'route' => 'offers_specific',
      'icon' => 'application/modules/Offers/externals/images/photo_manage.png',
      'class' => 'buttonlink',
      'params' => array(
        'action' => 'manage-photos',
        'offer_id' => $subject->getIdentity()
      )
    );
  }

  public function onMenuInitialize_OfferAddPhotos()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label' => 'OFFERS_add_photos',
      'route' => 'offers_specific',
      'icon' => 'application/modules/Offers/externals/images/add.png',
      'params' => array(
        'action' => 'add-photos',
        'offer_id' => $subject->getIdentity()
      )
    );
  }

  public function onMenuInitialize_ViewOffer()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label' => 'OFFERS_view_offer',
      'route' => 'offers_specific',
      'icon' => 'application/modules/Offers/externals/images/view_offer.png',
      'params' => array(
        'action' => 'view',
        'offer_id' => $subject->getIdentity()
      )
    );
  }

  public function onMenuInitialize_OfferAdminMainCredits()
  {
    return (boolean)(Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('credit'));
  }
}