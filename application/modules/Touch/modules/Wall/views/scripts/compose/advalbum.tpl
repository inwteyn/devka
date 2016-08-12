<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composePhoto.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>

<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var type = 'wall';

    var composeInstance = feed.compose;

    if (composeInstance.options.type) type = composeInstance.options.type;

    composeInstance.addPlugin(new Wall.Composer.Plugin.Photo({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
      lang : {
        'Add Photo' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl + 'advalbum/album/compose-upload/type/'+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl + 'advalbum/album/compose-upload/format/json/type/'+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>