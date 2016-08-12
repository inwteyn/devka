<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: avp.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */
?>
<?php
$this->headScript()
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/core.js')
  ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/composer_avp.js') ?>
<?php
    $allowed = 1;
    $user = Engine_Api::_()->user()->getViewer();
    if ($user->getIdentity() < 1) $allowed = 0;
    
    if ($allowed)
    {
        $plugin = new Avp_Plugin_Menus();
        $allowed_import = $plugin->onMenuInitialize_AvpMainImport(array());
        
        if ($allowed_import) $allowed = 1;
    }
?>



<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    var type = 'wall';
    if (feed.compose.options.type) type = feed.compose.options.type;
    feed.compose.addPlugin(new Wall.Composer.Plugin.AVP(
    {
      title : '<?php echo $this->translate('Add Video') ?>',
      allowed : <?php echo (int)$allowed;?>,
      import_allowed : <?php echo (int)$allowed_import;?>,
      upload_allowed : false
    }));

  });

</script>
