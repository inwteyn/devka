<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composeVideo.tpl 7716 2010-10-29 01:11:30Z jung $
 * @author     Jung
 */
?>
<?php $this->headScript()
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/core.js')
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/composer_video.js')
?>
<?php
    $user = Engine_Api::_()->user()->getViewer();
    $allowed_upload = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload');
    $ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    $allowed = 0;
    if($allowed_upload && $ffmpeg_path) $allowed = 1;
  ?>
<script type="text/javascript">
Wall.runonce.add(function (){
//  en4.core.runonce.add(function (){
  var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
  var type = 'wall';
  if (feed.compose.options.type) type = feed.compose.options.type;
  feed.compose.addPlugin(new Wall.Composer.Plugin.Video({
    title : '<?php echo $this->translate('Add Video') ?>',
    lang : {
      'Add Video' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
      'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
      'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
      'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
      'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
      'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
      'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
      'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube')) ?>',
      'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo')) ?>',
      'To upload a video from your computer, please use our full uploader.': '<?php echo addslashes($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create', 'type'=>3), 'video_general'))) ?>'
    },
    allowed : <?php echo $allowed?'true':'false';?>,
    type : type,
    requestOptions : {
      'url' : en4.core.baseUrl + 'video/index/compose-upload/format/json/c_type/'+type
    }
  }));
});
</script>


