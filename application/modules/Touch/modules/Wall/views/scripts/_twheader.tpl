<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _twheader.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

  $this->headScript()
      ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/core.js');
// Wall Scripts
// Support Other Plugins
$modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();




// Translates
$translate_list = array(
  'WALL_CONFIRM_ACTION_REMOVE_TITLE',
  'WALL_CONFIRM_ACTION_REMOVE_DESCRIPTION',
  'WALL_CONFIRM_COMMENT_REMOVE_TITLE',
  'WALL_CONFIRM_COMMENT_REMOVE_DESCRIPTION',
  'WALL_CONFIRM_LIST_REMOVE_TITLE',
  'WALL_CONFIRM_LIST_REMOVE_DESCRIPTION',
  'WALL_LIKE',
  'WALL_UNLIKE',
  'Save',
  'Cancel',
  'delete',
  'WALL_LOADING',
  'WALL_STREAM_EMPTY_VIEWALL',
  'WALL_EMPTY_FEED',
  'WALL_CAMERA_FREEZE',
  'WALL_CAMERA_CANCEL',
  'WALL_CAMERA_UPLOAD',
  'WALL_COMPOSE_CAMERA'
);


$services = Engine_Api::_()->wall()->getManifestType('wall_service', true);

foreach ($services as $service){
  $translate_list[] = 'WALL_SHARE_' . strtoupper($service) . '';
  $translate_list[] = 'WALL_SHARE_' . strtoupper($service) . '_ACTIVE';
  $translate_list[] = 'WALL_STREAM_' . strtoupper($service) . '_LOGIN';
}

$this->headTranslate($translate_list);




?>

<script type="text/javascript">

  Wall.liketips_enabled = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.liketips', true)?>;
  Wall.rolldownload = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.rolldownload', true)?>;
  Wall.dialogConfirm = <?php echo (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.dialogconfirm', true)?>;

<?php
  if ($this->viewer()->getIdentity()){
    
    $services_request = array();
    foreach ($services as $service){
      $class = Engine_Api::_()->wall()->getServiceClass($service);
      if (!$class){
        continue ;
      }
      $config = array(
        'provider' => $service,
        'enabled' => false,
      );
      $tokenRow = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($this->viewer(), $service);
      if ($tokenRow){
        $config = array_merge($config, $tokenRow->publicArray());
        $services_request[$service] = true;
      }

      $setting_key = 'share_' . $service . '_enabled';
      $setting = Engine_Api::_()->wall()->getUserSetting($this->viewer());

      if (isset($setting->{$setting_key}) && $setting->{$setting_key}){
        $config['share_enabled'] = true;
      }
      
      echo 'Wall.runonce.add(function (){ Wall.services.add("'.$service.'", new Wall.Service.'.ucfirst($service).'('.$this->jsonInline($config).')); });';
      
    }
    if (count($services_request)) {
      echo "Wall._servicesRequest = new Wall.ServicesRequest(".$this->jsonInline($services_request)."); Wall.runonce.add(function (){ Wall._servicesRequest.send(); });";
    }



  }
?>

</script>
