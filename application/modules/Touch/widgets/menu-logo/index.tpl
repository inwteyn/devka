<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<style type="text/css">
  .layout_touch_main_header>h3{
    display: none !important;
  }
</style>
<nobr>
<?php
  $title  = $this->title;
if(!$title)
  $title = Engine_Api::_()->getApi('settings', 'core')->__get('core_general_site_title', $this->translate('_SITE_TITLE'));
$logo  = $this->logo;
$route = $this->viewer()->getIdentity()
             ? array('route'=>'user_general', 'action'=>'home')
             : array('route'=>'home');

echo ($logo)
     ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title)), array('id'=>'home_url'))
     : $this->htmlLink($route, $title, array('id'=>'home_url'));
?>
</nobr>