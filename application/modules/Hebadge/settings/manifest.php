<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manifest.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'hebadge',
    'version' => '4.2.2p1',
    'path' => 'application/modules/Hebadge',
    'title' => 'Badges',
    'description' => 'Badges',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'callback' =>
    array (
      'class' => 'Hebadge_Installer',
      'path' => 'application/modules/Hebadge/settings/install.php',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Hebadge',
      1 => 'public/hebadge_badge',
      2 => 'public/hebadge_pagebadge',
      4 => 'public/hebadge_creditbadge',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/hebadge.csv',
    ),
  ),

  'hooks' => array(
    array(
      'event' => 'onInviterSendInvite',
      'resource' => 'Hebadge_Plugin_Core'
    ),
    array(
      'event' => 'onUserLoginBefore',
      'resource' => 'Hebadge_Plugin_Core',
    ),
    array(
      'event' => 'onItemCreateAfter',
      'resource' => 'Hebadge_Plugin_Core'
    ),
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Hebadge_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Hebadge_Plugin_Core',
    ),
    array(
      'event' => 'onCreditBalanceCreateAfter',
      'resource' => 'Hebadge_Plugin_Core',
    ),
    array(
      'event' => 'onCreditBalanceUpdateAfter',
      'resource' => 'Hebadge_Plugin_Core',
    ),
    array(
      'event' => 'onInviterRefered',
      'resource' => 'Hebadge_Plugin_Core'
    ),
  ),

  'items' => array(
    'hebadge_badge',
    'hebadge_complete',
    'hebadge_require',
    'hebadge_member',
    'hebadge_pagebadge',
    'hebadge_pagemember',
    'hebadge_creditbadge',
    'hebadge_creditmember',
  ),

  'routes' => array(
    'hebadge_general' => array(
      'route' => 'badges/:controller/:action/*',
      'defaults' => array(
        'module' => 'hebadge',
        'controller' => 'index',
        'action' => 'index',
      )
    ),
    'hebadge_profile' => array(
      'route' => 'badge/:id/:slug/*',
      'defaults' => array(
        'module' => 'hebadge',
        'controller' => 'index',
        'action' => 'view',
        'id' => 0,
        'slug' => ''
      )
    ),
    'hebadge_credit_profile' => array(
      'route' => 'credit-ranks/:id/:slug/*',
      'defaults' => array(
        'module' => 'hebadge',
        'controller' => 'credit',
        'action' => 'index',
        'id' => 0,
        'slug' => ''
      )
    ),
  ),


  'hebadge' => array(
    'friend' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Friend',
      'require_link' => null,
      'module' => 'user',
      'onlyCount' => true
    ),
    'comment' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Comment',
      'require_link' => null,
      'module' => 'core',
      'onlyCount' => true
    ),
    'login' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Login',
      'require_link' => null,
      'module' => 'core',
      'onlyCount' => true
    ),
    'status' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Status',
      'require_link' => null,
      'module' => 'activity',
      'onlyCount' => true
    ),
    'photo' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Photo',
      'require_link' => null,
      'module' => 'album',
      'onlyCount' => true
    ),
    'blog' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Blog',
      'require_link' => null,
      'module' => 'blog',
      'onlyCount' => true
    ),
    'event' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Event',
      'require_link' => null,
      'module' => 'event',
      'onlyCount' => true
    ),
    'group' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Group',
      'require_link' => null,
      'module' => 'group',
      'onlyCount' => true
    ),
    'forum' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Forum',
      'require_link' => null,
      'module' => 'forum',
      'onlyCount' => true
    ),
    'classified' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Classified',
      'require_link' => null,
      'module' => 'classified',
      'onlyCount' => true
    ),
    'invite' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Invite',
      'require_link' => null,
      'module' => 'invite',
      'onlyCount' => true
    ),
    'referral' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Referral',
      'require_link' => null,
      'module' => 'invite',
      'onlyCount' => true
    ),
    'poll' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Poll',
      'require_link' => null,
      'module' => 'poll',
      'onlyCount' => true
    ),
    'pollpassed' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_PollPassed',
      'require_link' => null,
      'module' => 'poll',
      'onlyCount' => true
    ),
    'music' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Music',
      'require_link' => null,
      'module' => 'music',
      'onlyCount' => true
    ),
    'video' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Video',
      'require_link' => null,
      'module' => 'video',
      'onlyCount' => true
    ),
    'checkin' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Checkin',
      'require_link' => null,
      'module' => 'checkin',
      'onlyCount' => true
    ),
    'like' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Like',
      'require_link' => null,
      'module' => 'like',
      'onlyCount' => true
    ),
    'likeme' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_LikeMe',
      'require_link' => null,
      'module' => 'like',
      'onlyCount' => true
    ),
    'quiz' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Quiz',
      'require_link' => null,
      'module' => 'quiz',
      'onlyCount' => true
    ),
    'quizpassed' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_QuizPassed',
      'require_link' => null,
      'module' => 'quiz',
      'onlyCount' => true
    ),
    'rate' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Rate',
      'require_link' => null,
      'module' => 'rate',
      'onlyCount' => true
    ),
    'review' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Review',
      'require_link' => null,
      'module' => 'rate',
      'onlyCount' => true
    ),
    'store' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Store',
      'require_link' => null,
      'module' => 'store',
      'onlyCount' => true
    ),
    'storeorder' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_StoreOrder',
      'require_link' => null,
      'module' => 'store',
      'onlyCount' => true
    ),
    'suggest' => array(
      'defaultParams' => array('count' => 5),
      'adminForm' => array(
        'elements' => array(array('text','count', array('label' => 'HEBADGE_MIN_COUNT', 'allowEmpty' => false, 'required' => true)))
      ),
      'plugin' => 'Hebadge_Plugin_Require_Suggest',
      'require_link' => null,
      'module' => 'suggest',
      'onlyCount' => true
    )

  )

); ?>
