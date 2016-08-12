<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Composer.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Heemoticon_Plugin_Composer extends Core_Plugin_Abstract
{
  public function onAttachHeemoticon_post($data)
  {
    if( !is_array($data) || empty($data['emoticon_id']) ) {
      return;
    }
    $storage = Engine_Api::_()->getItemTable('storage_file');
    $emoticon = $storage->getFile($data['emoticon_id']);

    // make the image public

    // CREATE AUTH STUFF HERE
    /*
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
    foreach( $roles as $i=>$role )
    {
      $auth->setAllowed($photo, $role, 'view', ($i <= $roles));
      $auth->setAllowed($photo, $role, 'comment', ($i <= $roles));
    }*/

    if(  !$emoticon->getIdentity() )
    {
      return;
    }

    return $emoticon;
  }
}