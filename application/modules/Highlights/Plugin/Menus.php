<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Menus.php 9770 2012-08-30 02:36:05Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Highlights_Plugin_Menus
{
    public function onMenuInitialize_UserProfileCredit()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        $label = "Credit for Highlight";
        if( !$viewer->isSelf($subject) ) {
            $label = "Credit for Highlight";
        }

        if( $subject->authorization()->isAllowed($viewer) ) {
            return array(
                'label' => $label,
                'icon' => 'application/modules/Credit/externals/images/current.png',
                'route' => 'highlight_extends',
                'class' => 'smoothbox',
                'params' => array(
                    'controller' => 'credit',
                    'action' => 'profile',
                    'id' => ( $viewer->getGuid(false) == $subject->user_id ? null : $subject->getIdentity() ),
                )
            );
        }

        return false;
    }
}