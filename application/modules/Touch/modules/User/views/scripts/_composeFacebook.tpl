<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeFacebook.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<?php
  $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
  $facebookApi = $facebookTable->getApi();
  // Disabled
  if( !$facebookApi ||
      'publish' != Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
    return;
  }
  // Not logged in
  if( !$facebookTable->isConnected() ) {
    return;
  }
  // Not logged into correct facebook account
  if( !$facebookTable->checkConnection() ) {
    return;
  }

  // Add script
  $this->headScript()
      ->appendFile($this->baseUrl() . '/application/modules/User/externals/scripts/composer_facebook.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Facebook({
      lang : {
        'Publish this on Facebook' : '<?php echo $this->translate('Publish this on Facebook') ?>'
      }
    }));
  });
</script>
