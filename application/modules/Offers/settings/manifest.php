<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 2012-10-05 17:07:11 taalay $
 * @author     TJ
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  'package' => array(
    'type' => 'module',
    'name' => 'offers',
    'version' => '4.2.5p2',
    'path' => 'application/modules/Offers',
    'title' => 'Offers',
    'description' => 'Offers Plugin from Hire-Express LLC',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array (
      'title' => 'Offers',
      'description' => 'Offers Plugin from Hire-Express LLC',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      ),
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.2.0p4',
      ),
    ),
    'callback' => array(
      'path' => 'application/modules/Offers/settings/install.php',
      'class' => 'Offers_Installer',
    ),
    'actions' =>
    array(
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array(
      0 => 'application/modules/Offers',
    ),
    'files' =>
    array(
      0 => 'application/languages/en/offers.csv',
    ),
  ),
  // Items -----------------------------------------------------
  'items' => array(
    'offer',
    'offersphoto',
    'offers_order',
    'offers_transaction',
    'offers_subscription',
  ),

  // Routes ----------------------------------------------------
  'routes' => array(
    'offers_general' => array(
      'route' => 'offers/:action/:num_page/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'index',
        'action' => 'browse',
        'num_page' => 1
      ),
      'regs' => array(
        'controller' => '(index)',
        'action' => '(browse|mark-as-used|favorite|upload-photo|browse-subscribers)'
      )
    ),

    'offers_subscription' => array(
      'route' => 'offers-subscription/:action/:offer_id/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'subscription',
        'action' => 'choose',
        'offer_id' => 0
      ),
    ),

    'offers_specific' => array(
      'route' => 'offer/:action/:offer_id/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'offer',
        'action' => 'index',
      ),
      'regs' => array(
        'action' => '(view|delete|edit|manage-photos|add-photos|follow|edit-contacts|print|email|show-all-participants|upload-photo)',
        'offer_id' => '\d+'
      ),
    ),

    'offers_page' => array(
      'route' => 'page-offers/:action/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'profile',
        'action' => 'index'
      )
    ),

    'offers_upcoming' => array(
      'route' => 'offers/upcoming/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'index',
        'action' => 'browse',
        'filter' => 'upcoming',
      )
    ),

    'offers_past' => array(
      'route' => 'offers/past/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'index',
        'action' => 'browse',
        'filter' => 'past',
      )
    ),

    'offers_mine' => array(
      'route' => 'offers/manage/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'index',
        'action' => 'browse',
        'filter' => 'mine',
      )
    ),

    'offer_page_backend' => array(
      'route' => 'offers/:controller/:action/:page_id/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'page',
        'action' => 'gateway',
      ),
      'reqs' => array(
        'controller' => '(page)',
        'action' => '(gateway|gateway-edit|transactions|detail|row-order-detail|row-transaction-detail|detail-transaction)',
      )
    ),

    'offer_admin_manage' => array(
      'route' => 'admin/offers/manage/:action/:offer_id/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'admin-manage',
        'action' => 'index',
        'offer_id' => 0
      ),
      'reqs' => array(
        'offer_id' => '\d+'
      )
    ),

    'offer_map' => array(
      'route' => 'large-offer-map/:offer_id/*',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'offer',
        'action' => 'large-map'
      )
    ),

    'offer_photos' => array(
      'route' => 'upload-photo/:action',
      'defaults' => array(
        'module' => 'offers',
        'controller' => 'index',
        'action' => 'upload-photo'
      )
    )
  ),

//hooks---------------------------------------------------------------->

  'hooks' => array(
    array(
      'event' => 'onInviterSendInvite',
      'resource' => 'Offers_Plugin_Core'
    ),
    array(
      'event' => 'onUserLoginBefore',
      'resource' => 'Offers_Plugin_Core',
    ),
    array(
      'event' => 'onItemCreateAfter',
      'resource' => 'Offers_Plugin_Core'
    ),
    array(
      'event' => 'onOfferDeleteBefore',
      'resource' => 'Offers_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Offers_Plugin_Core',
    ),
  ),

  'offers' => array(
    'page' => array(
      'likepage' => array(
        'defaultParams' => array('count' => 1),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'OFFERS_MIN_COUNT', 'allowEmpty' => false, 'required' => true, 'disabled' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_LikePage',
        'require_link' => null,
        'module' => 'like',
        'onlyCount' => true
      ),
      'review' => array(
        'defaultParams' => array('count' => 1),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'OFFERS_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_Review',
        'require_link' => null,
        'module' => 'rate',
        'onlyCount' => true
      ),
      'suggest' => array(
        'defaultParams' => array('count' => 5),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_Suggest',
        'require_link' => null,
        'module' => 'suggest',
        'onlyCount' => true
      ),
      'pagedocument' => array(
        'defaultParams' => array('count' => 5),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_PageDocument',
        'require_link' => null,
        'module' => 'pagedocument',
        'onlyCount' => true
      ),
      'pageblog' => array(
        'defaultParams' => array('count' => 5),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_PageBlog',
        'require_link' => null,
        'module' => 'pageblog',
        'onlyCount' => true
      ),
      'playlist' => array(
        'defaultParams' => array('count' => 5),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_Playlist',
        'require_link' => null,
        'module' => 'pagemusic',
        'onlyCount' => true
      ),
      'pagevideo' => array(
        'defaultParams' => array('count' => 5),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_PageVideo',
        'require_link' => null,
        'module' => 'pagevideo',
        'onlyCount' => true
      ),
      'pagealbum' => array(
        'defaultParams' => array('count' => 5),
        'adminForm' => array(
          'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
        ),
        'plugin' => 'Offers_Plugin_Require_PageAlbum',
        'require_link' => null,
        'module' => 'pagealbum',
        'onlyCount' => true
      ),
    ),
    'friend' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Friend',
      'require_link' => null,
      'module' => 'user',
      'onlyCount' => true
    ),
    'comment' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Comment',
      'require_link' => null,
      'module' => 'core',
      'onlyCount' => true
    ),
    'login' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Login',
      'require_link' => null,
      'module' => 'core',
      'onlyCount' => true
    ),
    'status' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Status',
      'require_link' => null,
      'module' => 'activity',
      'onlyCount' => true
    ),
    'photo' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Photo',
      'require_link' => null,
      'module' => 'album',
      'onlyCount' => true
    ),
    'blog' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Blog',
      'require_link' => null,
      'module' => 'blog',
      'onlyCount' => true
    ),
    'event' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Event',
      'require_link' => null,
      'module' => 'event',
      'onlyCount' => true
    ),
    'group' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Group',
      'require_link' => null,
      'module' => 'group',
      'onlyCount' => true
    ),
    'forum' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Forum',
      'require_link' => null,
      'module' => 'forum',
      'onlyCount' => true
    ),
    'classified' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Classified',
      'require_link' => null,
      'module' => 'classified',
      'onlyCount' => true
    ),
    'invite' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Invite',
      'require_link' => null,
      'module' => 'invite',
      'onlyCount' => true
    ),
    'referral' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Referral',
      'require_link' => null,
      'module' => 'invite',
      'onlyCount' => true
    ),
    'poll' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Poll',
      'require_link' => null,
      'module' => 'poll',
      'onlyCount' => true
    ),
    'pollpassed' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_PollPassed',
      'require_link' => null,
      'module' => 'poll',
      'onlyCount' => true
    ),
    'music' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Music',
      'require_link' => null,
      'module' => 'music',
      'onlyCount' => true
    ),
    'video' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Video',
      'require_link' => null,
      'module' => 'video',
      'onlyCount' => true
    ),
    'checkin' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Checkin',
      'require_link' => null,
      'module' => 'checkin',
      'onlyCount' => true
    ),
    'like' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Like',
      'require_link' => null,
      'module' => 'like',
      'onlyCount' => true
    ),
    'likeme' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_LikeMe',
      'require_link' => null,
      'module' => 'like',
      'onlyCount' => true
    ),
    'quiz' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Quiz',
      'require_link' => null,
      'module' => 'quiz',
      'onlyCount' => true
    ),
    'quizpassed' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_QuizPassed',
      'require_link' => null,
      'module' => 'quiz',
      'onlyCount' => true
    ),
    'rate' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Rate',
      'require_link' => null,
      'module' => 'rate',
      'onlyCount' => true
    ),
    'review' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Review',
      'require_link' => null,
      'module' => 'rate',
      'onlyCount' => true
    ),
    'store' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Store',
      'require_link' => null,
      'module' => 'store',
      'onlyCount' => true
    ),
    'storeorder' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_StoreOrder',
      'require_link' => null,
      'module' => 'store',
      'onlyCount' => true
    ),
    'suggest' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text', 'count', array('label' => 'Offers_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Offers_Plugin_Require_Suggest',
      'require_link' => null,
      'module' => 'suggest',
      'onlyCount' => true
    )
  ));
?>