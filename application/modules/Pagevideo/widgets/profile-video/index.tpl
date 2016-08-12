<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-20 17:53 idris $
 * @author     Idris
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pagevideo/externals/scripts/video.js')
  ;
$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();

if(version_compare($coreItem['version'], '4.8.10')>=0){
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js');
  echo '<script type="text/javascript">
page_video.flowplayer_url  = en4.core.baseUrl + "externals/flowplayer/flowplayer-3.2.18.swf"
</script>';

}else{
  echo '<script type="text/javascript">
page_video.flowplayer_url  = en4.core.baseUrl + "externals/flowplayer/flowplayer-3.1.5.swf"
</script>';
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
}
?>

<div id="page_video_init_js_block">
  <?php echo $this->render('init_js.tpl'); ?>
</div>

<div id="page_video_navigation_block">
  <?php echo $this->render('navigation.tpl'); ?>
</div>
<div id="page_video_errors">
  <div id="page_video_form_errors">
    Video Title<br/>
    Please complete this field - it is required.
  </div>
</div>
<div id="page_video_forms_block">
  <?php echo $this->render('forms.tpl'); ?>
</div>
<div id="page_video_main_container">
  <?php echo $this->render('index.tpl'); ?>
</div>
