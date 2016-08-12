<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: activeTouchThemeStyles.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_ActiveTouchThemeStyles extends Zend_View_Helper_Abstract
{
  public function activeTouchThemeStyles($counter)
  {
    $config = array();
		$_GET['c'] = $counter;
		include_once APPLICATION_PATH . '/application/settings/scaffold.php';
    include_once APPLICATION_PATH . '/application/libraries/Scaffold/libraries/Bootstrap.php';

		// Scaffold constants
		define('SCAFFOLD_SYSPATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Scaffold' . DIRECTORY_SEPARATOR);
		define('SCAFFOLD_DOCROOT', $config['document_root']);

		define('SCAFFOLD_URLPATH', dirname($_SERVER["SCRIPT_NAME"]));
    try{
		set_include_path(
			APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Scaffold' . PATH_SEPARATOR .
			get_include_path()
		);
    } catch(Exception $e){
    }

		$theme = Zend_Registry::get('Zend_View')->touchActiveTheme();

		// Double check some of the config options
		if( isset($config['log_path']) && !@is_dir($config['log_path']) ) {
			@mkdir($config['log_path'], 0777, true);
		}
		if( isset($config['cache']) && !@is_dir($config['cache']) ) {
			@mkdir($config['cache'], 0777, true);
		}

		$files = array();
		$files[] = '/application/modules/Touch/themes/' . $theme . '/theme.css';

		//@Todo
		/*$supportedModules = Engine_Api::_()->touch()->getSupportedModules();
		foreach ($supportedModules as $m){
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled( $m ))
			{
				$tmp_file =  Engine_Api::_()->touch()->getPath($m, array('externals', 'styles', 'main.css'));
				if ($tmp_file) $files[] = $tmp_file;
			}
		}*/
    try{
		$result = Scaffold::parse($files, $config, array(), 1);
    $content = $result['content'];
    } catch (Exception $e){
      throw $e;
    }
    // Externals Packs
    $calendar_css = APPLICATION_PATH . '/application/modules/Touch/externals/calendar/styles.css';
		if (file_exists($calendar_css)){
        $content .= file_get_contents($calendar_css);
		}

		return $content;
	}
}
