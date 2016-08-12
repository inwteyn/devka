<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 
class Touch_Api_Core extends Core_Api_Abstract
{
  const CONTROLLER_PREFIX = 'touch-';
  const MODULE_PREFIX = 'touch';
  const VIEW_BASE_PATH = 'touch-views';
  const VIEW_BASE_PATH_SPEC = ':moduleDir/touch-views';

  protected $deniedScripts = array(
        'externals/tagger/tagger.js',
        'http://maps.googleapis.com',
        'externals/moolasso/Lasso.js',
        'externals/swfobject/swfobject.js',
        'externals/moolasso/Lasso.Crop.js',
        'externals/autocompleter/Observer.js',
        'externals/calendar/calendar.compat.js',
        'externals/fancyupload/FancyUpload2.js',
        'externals/fancyupload/Fx.ProgressBar.js',
        'externals/fancyupload/Swiff.Uploader.js',
        'externals/autocompleter/Autocompleter.js',
        'externals/flowplayer/flashembed-1.0.1.pack.js',
        'externals/autocompleter/Autocompleter.Local.js',
        'externals/autocompleter/Autocompleter.Request.js',
        'application/modules/Avp/externals/scripts/core.js',
        'application/modules/Rate/externals/scripts/Rate.js',
        'application/modules/Like/externals/scripts/core.js',
        'application/modules/Album/externals/scripts/core.js',
        'application/modules/Hecore/externals/scripts/core.js',
        'application/modules/Hetips/externals/scripts/core.js',
        'application/modules/Like/externals/scripts/remote.js',
        'application/modules/Updates/externals/scripts/core.js',
        'application/modules/Inviter/externals/scripts/core.js',
        'application/modules/Core/externals/scripts/composer.js',
        'application/modules/Store/externals/scripts/manager.js',
        'application/modules/Activity/externals/scripts/core.js',
        'application/modules/Album/externals/scripts/composer_photo.js',
        'application/modules/Hecore/externals/scripts/imagezoom/core.js'
      );
  public function isTouch($only_touch = false)
  {

		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobile') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
		{
			if (array_key_exists('HTTP_USER_AGENT', $_SERVER)){
				$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);
        $tablets = '';
        if(Engine_Api::_()->getDbTable('settings', 'core')->getSetting('touch.include.tablets', false)){
          $tablets = 'iPad|GT-P1000|SGH-T849|SHW-M180S|';
        }
				if(preg_match('/'.$tablets.'ip(hone|od)|android|opera m(ob|in)i|blackberry|picup|imageuploader|windows (ce|phone)|iemobile/i', $useragent))
				  return true;
        else
          return false;
			}
    }
    if(!$only_touch)
		  return $this->isMobile();
    return false;

  }

  public function isMobile()
  {
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER)){
    	$useragent=$_SERVER['HTTP_USER_AGENT'];
    	return (bool) !$this->isTouch(true) && preg_match('/picup|imageuploader|android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
		}
		return false;
  }

  public function isMaintenanceMode(){
    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if( file_exists($global_settings_file) ) {
      $generalConfig = include $global_settings_file;
        } else {
      $generalConfig = array();
    }
    return (!empty($generalConfig['maintenance']['enabled'])) ? true : false;
  }
  public function siteMode()
  {
    if (isset($_COOKIE['windowwidth']) && $_COOKIE['windowwidth'] === "324"/*for simulator*/){
      return 'touch';
    }

    $session = new Zend_Session_Namespace('standard-mobile-mode');
    if ($session->__isset('mode'))
    {

			$mode	= $session->__get('mode');
			if (
        $mode === 'touch'
      )
				return 'touch';
      else if ($mode === "mobile")
        return "mobile";
			elseif($mode === 'standard')
				return 'standard';
			elseif($mode === 'simulator')
				return 'simulator';
    }

		if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('touch.default', false) || $this->isTouch())
		{
			return 'touch';
		}

    return 'standard';
  }

	public function isTouchMode()
  {
		return (bool)($this->siteMode() === 'touch');
	}

	public function getUserAgent(){
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER)){
    	return strtolower($_SERVER['HTTP_USER_AGENT']);
		}

		else '';
	}

  public function setLayout()
  {
    // Create layout
    $layout = Zend_Layout::startMvc();

    // Set options
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Touch/layouts", 'Core_Layout_View')
      ->setViewSuffix('tpl')
      ->setLayout(null);

    // Add themes
    $themeTable = Engine_Api::_()->getDbtable('themes', 'touch');
    $themeSelect = $themeTable->select()->where('active = ?', 1);
    $themes = array();
    $themesInfo = array();
    foreach( $themeTable->fetchAll($themeSelect) as $row ) {
      $themes[] = $row->name;
      $themesInfo[$row->name] = include APPLICATION_PATH_COR . DS . 'modules' . DS . 'Touch' . DS . 'themes' . DS . $row->name . DS . 'manifest.php';
    }
    $layout->themes = $themes;
    $layout->themesInfo = $themesInfo;
    Zend_Registry::set('Themes', $themesInfo);

    // Add global site title etc
    $siteinfo = Engine_Api::_()->getApi('settings', 'core')->__get('core.general.site', array());
    $siteinfo = array_filter($siteinfo);
    $siteinfo = array_merge(array(
      'title' => 'Social Network',
      'description' => '',
      'keywords' => '',
    ), $siteinfo);
    $layout->siteinfo = $siteinfo;

    return $layout;
  }

	public function redirectController($module)
	{
		$frontController = Zend_Controller_Front::getInstance();
		$moduleDir = $this->isInside($module, true);
		if ( $moduleDir !== false ) {
			$moduleDir .= DIRECTORY_SEPARATOR . $frontController->getModuleControllerDirectoryName();
			$frontController->addControllerDirectory($moduleDir, $module);
      return true;
		} else {
			return false;
		}
	}

	public function getPath($module, $params = array())
	{
		$moduleInflected = Engine_Api::inflect($module);

		$path = APPLICATION_PATH
			. DIRECTORY_SEPARATOR
			. "application"
			. DIRECTORY_SEPARATOR
			. "modules"
			. DIRECTORY_SEPARATOR
			. 'Touch'
			. DIRECTORY_SEPARATOR
			. 'modules'
			. DIRECTORY_SEPARATOR
			. $moduleInflected;

		foreach ($params as $dir)
		{
			$path .= DIRECTORY_SEPARATOR . $dir;
		}

		return $path;
	}

	public function getScriptPath($module)
	{
		$path = $this->getPath($module);
    if(is_dir($path))
		return $path . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts';
	}

	public function touchGetComments($item, $commentViewAll)
	{
    $comments = $item->comments();
    $table = $comments->getReceiver();
    $comment_count = $comments->getCommentCount();

    if( $comment_count <= 0 )
    {
      return;
    }
    // Always just get the last three comments
    $select = $comments->getCommentSelect();

    if( $comment_count <= 2 )
    {
      $select->limit(2);
    }
    else if( !$commentViewAll )
    {
      $select->limit(2, $comment_count - 2);
    }

    return $table->fetchAll($select);
	}

  protected function _getInfo(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->__get('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
			'order' => null,
    );

    $newParams = array();
    foreach( $args as $arg => $default ) {
      if( !empty($params[$arg]) ) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }

  public function getFriends($params = array(), $user = null)
  {
    if (!empty($params['object_id'])) {
      $viewer = Engine_Api::_()->getItem('user', $params['object_id']);
    } else {
      if ($user instanceof User_Model_User) {
        $viewer = $user;
      } elseif (is_numeric($user)) {
        $viewer = Engine_Api::_()->getItem('user', $user);
      } else {
        $viewer = Engine_Api::_()->user()->getViewer();
      }
    }

    $table = Engine_Api::_()->getItemTable('user');
    $prefix = $table->getTablePrefix();

    $select = $table->select();

    if (!empty($params['sort_list'])) {

			if (is_array($params['sort_list'])){
				$params['sort_list'] = (count($params['sort_list']) > 0)?implode($params['sort_list'], array()):'0';
			}

      $list = $params['sort_list'];
      $order = "IF ({$prefix}users.user_id IN ({$list}), 9999, RAND()) DESC";
      $select->order($order);
    }

    $select
      ->setIntegrityCheck(false)
      ->from($prefix.'users')
      ->joinLeft($prefix . 'user_membership', $prefix . 'user_membership.user_id = ' . $prefix . 'users.user_id', array())
      ->where($prefix . 'user_membership.resource_id = ?', $viewer->getIdentity())
      ->where($prefix . 'user_membership.resource_approved = 1')
      ->where($prefix . 'user_membership.user_approved = 1');

		if (!empty($params['keyword'])){
			$select->where($prefix . "users.displayname LIKE '%" . $params['keyword'] . "%'");
		}

    return Zend_Paginator::factory($select);
  }

	public function getItemsById($params, $user = null){
		$ids = $params['ids'];
		echo Zend_Json::encode($ids);
		exit();
	}

	public function isModuleEnabled($name){
		$modules = Engine_Api::_()->getDbtable('modules', 'core');

		return $modules->isModuleEnabled($name);
	}
	public function isIntegrationEnabled($name){
		$modules = Engine_Api::_()->getDbtable('modules', 'core');

		return $modules->isModuleEnabled($name);
	}

  public function customForm($form)
  {
    foreach ($form->getElements() as $element){
      if ($element instanceof Engine_Form_Element_Birthdate){
        $element->getDecorator('HtmlTag')->setOption('class', 'form-element birthdate-container');
      }
    }
    return $form;
  }

	public function resetMobi(Zend_Controller_Request_Abstract $request)
  {
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getActionName();

		if($module == "mobi")

			if ($controller == 'index' && $action == 'index') {
				$request->setModuleName('core');
				$request->setControllerName('index');
				$request->setActionName('index');
      }

			if($controller == "index" && $action == "userhome") {
				$request->setModuleName('user');
				$request->setControllerName('index');
				$request->setActionName('home');
			}

			if($controller == "index" && $action == "profile") {
				$request->setModuleName('user');
				$request->setControllerName('profile');
				$request->setActionName('index');
    }

			if($controller == "group" && $action == "profile") {
				$request->setModuleName('group');
				$request->setControllerName('profile');
				$request->setActionName('index');
    }

			if($controller == "event" && $action == "profile") {
				$request->setModuleName('event');
				$request->setControllerName('profile');
				$request->setActionName('index');
    }

		return $request;
    }

  public function checkPageWidget($page_id, $name)
  {
    $api = Engine_Api::_()->getDbTable('modules', 'core');

    if (!$api->isModuleEnabled('page')){
      return false;
    }

    $widget_list = array(
      'touch.page-feed' => 'page.feed',
      'touch.page-profile-photo' => 'page.profile-photo',
      'touch.page-profile-note' => 'page.profile-note',
      'touch.page-profile-admins' => 'page.profile-admins',
      'touch.page-profile-options' => 'page.profile-options',
      'touch.page-profile-fields' => 'page.profile-fields',
      'touch.page-profile-album' => 'pagealbum.profile-album',
      'touch.page-profile-blog' => 'pageblog.profile-blog',
      'touch.page-profile-discussion' => 'pagediscussion.profile-discussion',
      'touch.page-profile-event' => 'pageevent.profile-event',
      'touch.page-review' => 'rate.page-review',
      'touch.blog.rate-widget' => 'rate.widget-rate',
      'touch.rate-widget' => 'rate.widget-rate',
      'touch.page-profile-status' => 'page.profile-status',
    );



    if (!array_key_exists($name, $widget_list)){
      return false;
    }

    $external_name = $widget_list[$name];

    $parts = explode('.', $external_name);

    if (!$api->isModuleEnabled($parts[0])){
      return false;
    }

    $tbl = Engine_Api::_()->getDbTable('content', 'page');

    if ($external_name == 'page.profile-status'){

      $select = $tbl->select()
          ->where('page_id = ?', $page_id)
          ->where('name IN (?)', array('page.profile-status', 'like.status'));

    }  else {

      $select = $tbl->select()
          ->where('page_id = ?', $page_id)
          ->where('name = ?', $external_name);

    }

    $widget = $tbl->fetchRow($select);

    if (!$widget){
      return false;
    }


    return true;

  }
  public function deniedScripts(){
    return $this->deniedScripts;
  }






  public function setupRequest(Zend_Controller_Request_Abstract $request){
    /**
     * @var $viewRenderer Zend_Controller_Action_Helper_ViewRenderer
     */

    $moduleName = $request->getModuleName();
    $controllerName = $request->getControllerName();
    $actionName = $request->getActionName();
    $frontController = Zend_Controller_Front::getInstance();
    $viewRenderer = $this->getViewRenderer();
    $response = array(
      'level' => 0,
      'return' => null
    );

    // Configuring Request

    // if the integration is implemented inside the Touch Plugin
    if($moduleDir = $this->isInside($moduleName, true)){
      $moduleDir .= DIRECTORY_SEPARATOR . $frontController->getModuleControllerDirectoryName();
      $frontController->addControllerDirectory($moduleDir, $moduleName);
      $response['level'] = 4;
      return $response;

    } elseif ($this->isControllerPrefix($moduleName, $controllerName)){
      $request->setControllerName(Touch_Api_Core::CONTROLLER_PREFIX . $controllerName);
      $viewRenderer->setViewBasePathSpec(Touch_Api_Core::VIEW_BASE_PATH_SPEC);

      $response['level'] = 2;
      $response['return'] = $viewRenderer;
      return $response;

    } elseif ($this->isBasePathTouch($request->getModuleName())){
      $viewRenderer->setViewBasePathSpec(Touch_Api_Core::VIEW_BASE_PATH_SPEC);
      $response['level'] = 1;
      $response['return'] = $viewRenderer;
      return $response;

    } elseif ($this->isSeperateModule($moduleName)){
      $request->setModuleName(Touch_Api_Core::MODULE_PREFIX . $moduleName);
      $response['level'] = 3;
    }

    return $response;

  }

  private function getViewRenderer(){
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStack()->ViewRenderer;
    if(!$viewRenderer)
      throw new Exception('ViewRenderer is out of stack');
    return $viewRenderer;
  }



  private function isInside($module, $getdir = false){
    $path = $this->getPath($module);
    return  $getdir ? (is_dir($path)?$path:false) : is_dir($path);
  }

  private function isControllerPrefix($moduleName, $controllerName){
    return $this->getControllerFileName($moduleName, Touch_Api_Core::CONTROLLER_PREFIX . $controllerName, true);
  }

  /**
   * @param \Zend_Controller_Request_Abstract $request
   * @return bool
   */
  private function isBasePathTouch($moduleName){
    $path = APPLICATION_PATH
  			. DIRECTORY_SEPARATOR
  			. 'application'
  			. DIRECTORY_SEPARATOR
  			. 'modules'
  			. DIRECTORY_SEPARATOR
  			. ucfirst($moduleName)
        . DIRECTORY_SEPARATOR
        . Touch_Api_Core::VIEW_BASE_PATH;
    return is_dir($path);
  }

  /**
   *
   * @param string $moduleName
   * @param string $controllerName
   * @param bool $get_if_exists
   * @return bool|string
   */
  private function getControllerFileName($moduleName, $controllerName, $get_bool = false){
    $cname = '';
    foreach(explode('-', $controllerName) as $part){
      $cname .= ucfirst($part);

    };
    $cname .= 'Controller.php';
    $path = APPLICATION_PATH
  			. DIRECTORY_SEPARATOR
  			. "application"
  			. DIRECTORY_SEPARATOR
  			. "modules"
  			. DIRECTORY_SEPARATOR
  			. $moduleName
        . DIRECTORY_SEPARATOR
        . "controllers"
        . DIRECTORY_SEPARATOR
        . $cname;
    return $get_bool? (file_exists($path)?true:false):$path;
  }
  private function isSeperateModule($module){
    return Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled(Touch_Api_Core::MODULE_PREFIX . strtolower($module));
  }
  /**
   * @param \Zend_Controller_Request_Abstract $request
   */
  private function getIntegrationLevel(Zend_Controller_Request_Abstract $request){
  }
}