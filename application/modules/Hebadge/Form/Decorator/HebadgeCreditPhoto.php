<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HebadgeCreditPhoto.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Engine_Form_Decorator_HebadgeCreditPhoto extends Zend_Form_Decorator_Abstract
{
  public function render($content)
  {
    $item = $this->getOption('item');
    $type = $this->getOption('type');

    $view = $this->getElement()->getView();

    $html = $content;

    if (empty($item)){
      if ($type == 'thumb.profile'){
        $html = '<div class="hebadge_photo_div"><div class="hebadge_photo"><img src="'.$view->layout()->staticBaseUrl.'application/modules/Hebadge/externals/images/nophoto_badge_thumb_profile.png"/></div><div class="hebadge_photo_content">'.$content.'</div></div>';
      } else if ($type == 'thumb.icon'){
        $html = '<div class="hebadge_photo_div"><div class="hebadge_photo"><img src="'.$view->layout()->staticBaseUrl.'application/modules/Hebadge/externals/images/nophoto_badge_thumb_icon.png"/></div><div class="hebadge_photo_content">'.$content.'</div></div>';
      }
    } else {
      if ($type == 'thumb.profile'){
        $html = '<div class="hebadge_photo_div"><div class="hebadge_photo">'.$view->itemPhoto($item, 'thumb.profile').'<br /><a href="'.$view->url(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'remove-photo')).'">'.$view->translate('HEBADGE_REMOVE_PHOTO').'</a></div><div class="hebadge_photo_content">'.$content.'</div></div>';
      } else if ($type == 'thumb.icon'){
        $html = '<div class="hebadge_photo_div"><div class="hebadge_photo">'.$view->itemPhoto($item, 'thumb.icon').'<br /><a href="'.$view->url(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'remove-photo', 'type' => 'icon')).'">'.$view->translate('HEBADGE_REMOVE_ICON').'</a></div><div class="hebadge_photo_content">'.$content.'</div></div>';
      }
    }

    return $html;

  }


}