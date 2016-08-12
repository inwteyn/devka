<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<?php 
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pagemusic/externals/standalone/audio-player.js');
$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();
if(version_compare($coreItem['version'], '4.8.10')>=0){
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.18.min.js');

}else{
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
}
$this->headTranslate(
  array(
    'Add New Songs', 'Choose music from your computer to add to this playlist.', 'Edit Playlist',
    'Edit your playlist title, description, change playlist artwork, remove and order songs.'
  )
);
?>
  <script type="text/javascript">

    /*en4.core.runonce.add(function (){*/
    en4.core.runonce.add(function (){
      page_music.ipp = <?php echo $this->ipp;?>;
      page_music.init();
      <?php echo $this->init_js_str?>

    });

  </script>

<script type="text/javascript">
en4.core.runonce.add(function(){
		<?php echo $this->init_js_str; ?>
	});
</script>

<div id="page_music_navigation">
  <?php echo $this->render('navigation.tpl'); ?>
</div>

<div id="page_music_container">
  <?php echo $this->render('index.tpl'); ?>
</div>

<?php echo $this->render('forms.tpl'); ?>