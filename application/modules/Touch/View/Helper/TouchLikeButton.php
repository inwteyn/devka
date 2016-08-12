<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchLikeButton.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_View_Helper_TouchLikeButton extends Engine_View_Helper_HtmlImage
{
  public function touchLikeButton($item, $attrs = array(), $requireUser = true, $viewer_liked = null)
  {
    if( !($item instanceof Core_Model_Item_Abstract)){
      throw new Zend_View_Exception("Item must be a valid item");
    }
		
		$viewer = Engine_Api::_()->user()->getViewer();
    if ($requireUser && !$viewer->getIdentity()) {
      return "";
    }

    if ($viewer_liked === null) {
      $viewer_liked = Engine_Api::_()->like()->isLike($item);
    }

    if (!$viewer_liked) {
      $label = $this->view->translate('like_Like');
      $class = 'like_button';
      $action = 'like';
      $onClick = (!empty($attrs['onLike'])) ? ' onClick = "' . $attrs['onLike'] . '"' : '';
    } else {
      $label = $this->view->translate('like_Unlike');
      $class = 'unlike_button';
      $action = 'unlike';
      $onClick = (!empty($attrs['onUnlike'])) ? ' onClick = "' . $attrs['onUnlike'] . '"' : '';
    }

    $attributes = "";
    if (!empty($attrs)) {
      unset($attrs['class']);
      unset($attrs['id']);
      unset($attrs['href']);
      unset($attrs['onLike']);
      unset($attrs['onUnlike']);

      foreach ($attrs as $key => $value) {
        $attributes .= " " .$key . "='" . $value . "'";
      }
    }

    $view = Zend_Registry::get('Zend_View');

    $wrapper = <<<HTML
      <script type="text/javascript">
      en4.core.runonce.add(function(){
        var options = {
          object_type: '{$item->getType()}',
          object_id: '{$item->getIdentity()}',
          likeBtn: '_{$item->getGuid()}',
          loader: 'like_loader_{$item->getGuid()}',
          menuHtml: false,
          menuId: 'like_menu_{$item->getGuid()}',
          likeUrl: '{$view->url(array("object" => $item->getType(), "object_id" => $item->getIdentity(), "action" => "like"), "like_default")}',
          unlikeUrl: '{$view->url(array("object" => $item->getType(), "object_id" => $item->getIdentity(), "action" => "unlike"), "like_default")}',
          switcher: 'like_switcher_{$item->getGuid()}'
        };
        if (!window.likeBtns) {
          window.likeBtns = {};
        }
        window.likeBtns.{$item->getGuid()} = new LikeButton(options);
      });
      </script>
HTML;


    $wrapper .= ' '
      . '<div class="like_button_container">'
      . '<div id="like_loader_'.$item->getGuid().'" class="like_button_loader hidden"></div>'
      . '<a ' . $attributes . '  id="_' . $item->getGuid() . '" '. $onClick .' onFocus="this.blur();" class="like_button_link ' . $action . '" href="javascript:void(0)">'
      . '<span class="' . $class . '">' . $label . '</span>'
      . '</a>';

		$wrapper .= ''
			. '<a class="like_menu_switcher" id="like_switcher_'.$item->getGuid().'"></a>';

		$wrapper .= ''
			. '<div class="clr"></div>'
      . '</div>';

    return $wrapper;
  }
}