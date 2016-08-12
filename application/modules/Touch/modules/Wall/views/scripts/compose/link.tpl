<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composeLink.tpl 8468 2011-02-15 23:11:09Z shaun $
 * @author     John
 */
?>
<?php
$this->headScript()
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/core.js')
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/composer_link.js');
?>
<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    feed.compose.addPlugin(new Wall.Composer.Plugin.Link({
      title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Link')) ?>',
      lang : {
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Last' : '<?php echo $this->string()->escapeJavascript($this->translate('Last')) ?>',
        'Next' : '<?php echo $this->string()->escapeJavascript($this->translate('Next')) ?>',
        'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Don\'t show an image' : '<?php echo $this->string()->escapeJavascript($this->translate('Don\'t show an image')) ?>',
        'Choose Image:' : '<?php echo $this->string()->escapeJavascript($this->translate('Choose Image:')) ?>',
        '%d of %d' : '<?php echo $this->string()->escapeJavascript($this->translate('%d of %d')) ?>'
      },
      requestOptions : {
        'url' :en4.core.baseUrl + 'core/link/preview'
      }

    }));

  });

</script>
