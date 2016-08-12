<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: music.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php
$session = new Zend_Session_Namespace("wall_service_soundcloud_token");
$baseUrl = $this->wallBaseUrl();
  $this->headScript()
       ->appendFile($baseUrl . 'externals/soundmanager/script/soundmanager2'
           . (APPLICATION_ENV == 'production' ? '-nodebug-jsmin' : '' ) . '.js')
    ->appendFile($baseUrl . 'application/modules/Music/externals/scripts/core.js')
    ->appendFile($baseUrl . 'application/modules/Music/externals/scripts/player.js')
    ->appendFile($baseUrl . 'application/modules/Wall/externals/scripts/composer_music.js')
    ->appendFile($baseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($baseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($baseUrl . 'externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($baseUrl . 'externals/fancyupload/fancyupload.css');
  $this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
    'or paste SoundCloud link above',
  ));
?>

<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    var type = 'wall';
    if (feed.compose.options.type) type = feed.compose.options.type;
    feed.compose.addPlugin(new Wall.Composer.Plugin.Music({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Music')) ?>',
      lang : {
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Loading song, please wait...': '<?php echo $this->string()->escapeJavascript($this->translate('Loading song, please wait...')) ?>',
        'Unable to upload music. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload music. Please click cancel and try again')) ?>',
        'Song got lost in the mail. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Song got lost in the mail. Please click cancel and try again')) ?>'
      },
      session :<?php if($session->token)echo "'".$session->token."'"; else echo 0; ?>,
      requestOptions : {
        'url'  : en4.core.baseUrl  + 'music/playlist/add-song/format/json?ul=1'+'&type='+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl  + 'music/playlist/add-song/format/json?ul=1'+'&type='+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf',
        'verbose' : <?php echo ( APPLICATION_ENV == 'development' ? 'true' : 'false') ?>,
        'appendCookieData' : true,
        'typeFilter' : {
          '<?php echo $this->translate('Music') ?> (*.mp3,*.m4a,*.aac,*.mp4)' : '*.mp3; *.m4a; *.aac; *.mp4'
        }
      }
    }));

  });

</script>
