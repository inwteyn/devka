<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _ads.tpl  30.11.12 16:53 TeaJay $
 * @author     Taalay
 */
?>

<?php

$api = Engine_Api::_()->page();
$widgets = array(
  'communityad.left-1-ads',
  'communityad.left-2-ads',
  'communityad.right-ads',
  'communityad.right-1-ads',
  'communityad.right-2-ads',
  'communityad.middle-1-ads',
  'communityad.middle-2-ads',
);

foreach($widgets as $widget){
  $id = $api->getCommunityAdId($widget);
  if($id) {
    echo $this->content()->renderWidget($widget, array('identity' => $id));
    break;
  }
}
?>