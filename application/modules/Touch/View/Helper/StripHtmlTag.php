<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: StripHtmlTag.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_StripHtmlTag extends Zend_View_Helper_Abstract
{
  public function stripHtmlTag($str, $tags = array(), $stripContent = true)
  {
    $content = '';

    if (!is_array($tags)) {
      $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
      if (end($tags) == '') {
        array_pop($tags);
      }
    }

    foreach ($tags as $tag) {
      if ($stripContent) {
        $content = '(.+</'.$tag.'[^>]*>|)';
      }
      $str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
    }

    return $str; 
  }
}
