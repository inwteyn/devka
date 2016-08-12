<?php
/**
 * SocialEngine
 *
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminThemesController.php 2012-12-13 15:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptablet_AdminThemesController extends Core_Controller_Action_Admin
{
  public function init()
  {

  }

  public function indexAction()
  {
    // Get themes
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'apptouch')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);

    // Install any themes that are missing from the database table
    $reload_themes = false;
    foreach (glob(APPLICATION_PATH . '/application/modules/Apptablet/externals/themes/*', GLOB_ONLYDIR) as $tablet_dir) {

      $touch_dir = $tablet_dir;
      $touch_dir = str_replace('Apptablet', 'Apptouch', $touch_dir);

      if (file_exists("$touch_dir/manifest.php") && is_readable("$touch_dir/manifest.php") && file_exists("$tablet_dir/theme.css") && is_readable("$tablet_dir/theme.css")) {
        $name = basename($tablet_dir);
        if (!$themes->getRowMatching('name', $name)) {
          $meta = include("$touch_dir/manifest.php");
          $row  = $themes->createRow();
          if( isset($meta['package']['meta']) ) {
            $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
            unset($meta['package']['meta']);
          }

          $row->title = $meta['package']['title'];
          $row->name  = $name;
          $row->description = isset($meta['package']['description']) ? $meta['package']['description'] : '';
          $row->active = 0;
          $row->save();
          $reload_themes = true;
        }
      }
    }

    foreach ($themes as $theme) {
      if (!is_dir(APPLICATION_PATH . '/application/modules/Apptablet/externals/themes/' . $theme->name)) {
        $theme->delete();
        $reload_themes = true;
      }
    }
    if ($reload_themes) {
      $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'apptouch')->fetchAll();
      $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
      if (empty($activeTheme)) {
        $themes->getRow(0)->active = 1;
        $themes->getRow(0)->save();
        $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
      }
    }

    // Process each theme
    $manifests = array();
    $writeable = array();
    $modified  = array();
    foreach( $themes as $theme ) {
      // Get theme manifest
      $themePath = "application/modules/Apptablet/externals/themes/{$theme->name}";
      $manifestPath = "application/modules/Apptouch/externals/themes/{$theme->name}";
      $manifest  = @include APPLICATION_PATH . "/$manifestPath/manifest.php";
      if( !is_array($manifest) )
        $manifest = array(
          'package' => array(),
        );

      // Pre-check manifest thumb
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      if( isset($manifest['package']['meta']) ) {
        $manifest['package'] = array_merge($manifest['package']['meta'], $manifest['package']);
        unset($manifest['package']['meta']);
      }

      if( !isset($manifest['package']['thumb']) ) {
        $manifest['package']['thumb'] = 'thumb.jpg';
      }
      $thumb = preg_replace('/[^A-Z_a-z-0-9\/\.]/', '', $manifest['package']['thumb']);
      if( file_exists(APPLICATION_PATH . "/$themePath/$thumb") ) {
        $manifest['package']['thumb'] = "$themePath/{$thumb}";
      } else {
        $manifest['package']['thumb'] = null;
      }

      // Check if theme files are writeable
      $writeable[$theme->name] = false;
      try {
        if( !file_exists(APPLICATION_PATH . "/$themePath/theme.css") ) {
          throw new Core_Model_Exception('Missing file in theme ' . $manifest['package']['title']);
        } else {
          $this->checkWriteable(APPLICATION_PATH . "/$themePath/theme.css");
        }
        $writeable[$theme->name] = true;
      } catch( Exception $e ) {
        if( $activeTheme->name == $theme->name ) {
          $this->view->errorMessage = $e->getMessage();
        }
      }

      // Check if theme files have been modified
      $modified[$theme->name] = array();
      $originalName = 'original.theme.css';
      if( file_exists(APPLICATION_PATH . "/$themePath/$originalName") ) {
        if( file_get_contents(APPLICATION_PATH . "/$themePath/$originalName") != file_get_contents(APPLICATION_PATH . "/$themePath/theme.css") ) {
          $modified[$theme->name][] = 'theme.css';
        }
      }
      $manifests[$theme->name] = $manifest;
    }

    $this->view->manifest  = $manifests;
    $this->view->writeable = $writeable;
    $this->view->modified  = $modified;

    // Get the first active file
    $this->view->fileContents = file_get_contents(APPLICATION_PATH . '/application/modules/Apptablet/externals/themes/'.$activeTheme->name.'/theme.css');
  }

  public function saveAction()
  {
    $theme_id = $this->_getParam('theme_id');
    $body = $this->_getParam('body');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if( !$theme_id || !$body ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'apptouch');
    $themeSelect = $themeTable->select()
      ->orWhere('theme_id = ?', $theme_id)
      ->orWhere('name = ?', $theme_id)
      ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    if( !$theme ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Missing theme");
      return;
    }

    // Check file
    $basePath     = APPLICATION_PATH . '/application/modules/Apptablet/externals/themes/' . $theme->name;

    $fullFilePath = $basePath . '/theme.css';
    try {
      $this->checkWriteable($fullFilePath);
    } catch( Exception $e ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Not writeable");
      return;
    }

    // Check for original file (try to create if not exists)
    if( !file_exists($basePath . '/original.theme.css') ) {
      if( !copy($fullFilePath, $basePath . '/original.theme.css') ) {
        $this->view->status = false;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Could not create backup");
        return;
      }
      chmod("$basePath/original.theme.css", 0777);
    }

    // Now lets write the custom file
    if( !file_put_contents($fullFilePath, $body) ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Could not save contents');
      return;
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

    $this->view->status = true;
  }

  public function revertAction()
  {
    $theme_id = $this->_getParam('theme_id');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if( !$theme_id ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'apptouch');
    $themeSelect = $themeTable->select()
      ->orWhere('theme_id = ?', $theme_id)
      ->orWhere('name = ?', $theme_id)
      ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    // Check file
    $basePath = APPLICATION_PATH . '/application/modules/Apptablet/externals/themes/' . $theme->name;
    if( file_exists("$basePath/original.theme.css") ) {
      // Check each file if writeable
      $this->checkWriteable($basePath . '/');
      $this->checkWriteable($basePath . '/theme.css');
      $this->checkWriteable($basePath . '/original.theme.css');

      // Now undo all of the changes
      unlink("$basePath/theme.css");
      rename("$basePath/original.theme.css", "$basePath/theme.css");
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

//    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function checkWriteable($path)
  {
    if( !file_exists($path) ) {
      throw new Core_Model_Exception('Path doesn\'t exist');
    }
    if( !is_writeable($path) ) {
      throw new Core_Model_Exception('Path is not writeable');
    }
    if( !is_dir($path) ) {
      if( !($fh = fopen($path, 'ab')) ) {
        throw new Core_Model_Exception('File could not be opened');
      }
      fclose($fh);
    }
  }

//  public function changeAction()
//  {
//    $themeName = $this->_getParam('theme');
//    $themeTable = Engine_Api::_()->getDbtable('themes', 'apptouch');
//    $themeSelect = $themeTable->select()
//      ->orWhere('theme_id = ?', $themeName)
//      ->orWhere('name = ?', $themeName)
//      ->limit(1)
//    ;
//    $theme = $themeTable->fetchRow($themeSelect);
//
//    if( $theme && $this->getRequest()->isPost() ) {
//      $db = $themeTable->getAdapter();
//      $db->beginTransaction();
//
//      try {
//        $themeTable->update(array(
//          'active' => 0,
//        ), array(
//          '1 = ?' => 1,
//        ));
//        $theme->active = true;
//        $theme->save();
//
//        // clear scaffold cache
//        Core_Model_DbTable_Themes::clearScaffoldCache();
//
//        // Increment site counter
//        $settings = Engine_Api::_()->getApi('settings', 'core');
//        $settings->core_site_counter = $settings->core_site_counter + 1;
//
//        $db->commit();
//
//      } catch( Exception $e ) {
//        $db->rollBack();
//        throw $e;
//      }
//    }
//
//    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
//  }
}