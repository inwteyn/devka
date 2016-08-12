<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composePhoto.tpl 7305 2010-09-07 06:49:55Z john $
 * @author     Sami
 */
?>

<?php
$this->headScript()
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/core.js')
   ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/composer_photo.js');
?>
<script type="text/javascript">
  Wall.runonce.add(function (){
    var feed = Wall.feeds.get("<?php echo $this->feed_uid; ?>");
    var photo_id = "<?php echo $this->photo_id; ?>";
    var photo_src = "<?php echo $this->photo_src; ?>";
    var photo_text = "<?php echo $this->photo_text; ?>";
    var callback_url = "<?php echo $this->callback_url; ?>";

    var type = 'wall';
    if (feed.compose.options.type) type = feed.compose.options.type;
    feed.compose.addPlugin(new Wall.Composer.Plugin.Photo({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
      lang : {
        'Add Photo' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl + 'album/album/compose-upload/type/'+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl + 'album/album/compose-upload/format/json/type/'+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
      var _photo = feed.compose.plugins.photo;
      if(callback_url != ''){
        _photo.src_url = callback_url;
      }
    if( photo_id != 0 ) {

      _photo.photo_id = photo_id;
      _photo.photo_src = photo_src;
      if(photo_text != ''){
            document.getElement('div.textareaBox').getElement('textarea').value = photo_text;
      }
      _photo.activate();
    }
  });
</script>