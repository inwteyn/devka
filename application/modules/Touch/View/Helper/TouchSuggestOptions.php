<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchSuggestOptions.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_View_Helper_TouchSuggestOptions extends Zend_View_Helper_Abstract
{
  /**
   * Name of current area
   *
   * @var string
   */
  protected $_name;

  /**
   * Render a content area by name
   * 
   * @param string $name
   * @return string
   */
  public function touchSuggestOptions(Core_Model_Item_Abstract $suggest)
  {
    $html = '';
    $object = $suggest->getObject();
    $type = $object->getType();
    $id = $object->getIdentity();
    $or = $this->view->translate('or');
    $label = $this->view->translate("suggest_view_this_".$type);
    
    switch ($type) {
      case 'group':
      case 'event':
        $url = $this->view->url(array(
            'controller' => 'member',
            'action' => 'join',
            $type.'_id' => $id
          ), $type.'_extended');
        
        $params = array('class' => 'smoothbox button');
      break;
      case 'user':
        $url = $this->view->url(array(
            'controller' => 'friends',
            'action' => 'add',
              'user_id' => $id
          ), 'user_extended');
        
        $params = array('class' => 'smoothbox button');
      break;
      case 'suggest_profile_photo':
        $url = $this->view->url(array(                    
          'action' => 'profile-photo',
          'photo_id' => $object->getIdentity(),
          'format' => 'smoothbox'), 'suggest_general');
        $params = array('class' => 'smoothbox button');
      break;
      default:
        $url = $this->view->url(array(
            'controller' => 'index',
            'action' => 'accept-suggest',
            'object_type' => $type,
            'object_id' => $id,
          ), 'suggest_general');

        $params = array('class' => 'button');
      break;
    }

    $link = $this->view->htmlLink($url, $label, $params);
    $cancel = $this->view->htmlLink($this->view->url(array(
        'action' => 'index',
        'controller' => 'index',
        'suggest_id' => $suggest->getIdentity()
      ), 'suggest_general'),
      $this->view->translate('suggest_cancel_suggest_'.$type),
      array(
        'class' => 'button disabled touchajax'
      )
    );

    $html .= $link . ' ' . $or . ' ' . $cancel;

    return $html;
  }

}