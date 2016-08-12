<?php

class Optimizer_AdminSettingsController extends Core_Controller_Action_Admin
{

  protected $mod_dir = './application/modules/Optimizer/external-pack/optimizer-files';
  // Save to public directory because by default in SE fot the dir set 0777
  //protected $backup_dir = './application/modules/Optimizer/external-pack/backup-files';
  protected $backup_dir = './public';

  public function indexAction()
  {
    // check installed optimizer
    $installed = true;
    foreach (Engine_Api::_()->optimizer()->getExternalFiles() as $path => $file){
      if (!$this->_isModifiedByOptimizer($path)){
        $installed = false;
      }
    }
    $this->view->installed = $installed;

    // Check timeline version
    $timeline = Engine_Api::_()->getDbTable('modules', 'core')->getModule('timeline');
    if ($timeline && version_compare($timeline->version, '4.2.1p2', '<')){
      $this->view->old_timeline = true;
    }

    // Get all allowed pages
    $allowedPages = Engine_Api::_()->optimizer()->getAllowedPages();
    $pageTable = Engine_Api::_()->getDbTable('pages', 'core');
    $pages = array();
    $select = $pageTable->select()
        ->where('name IN (?)', $allowedPages);

    foreach ($pageTable->fetchAll($select) as $page){
      $pages[$page->page_id] = $page;
    }

    $this->view->pages = $pages;

    $page_ids = array(0);
    foreach ($pages as $page){
      $page_ids[] = $page->page_id;
    }

    // Get allowed widget for replacing of the pages
    $contentTable = Engine_Api::_()->getDbTable('content', 'core');
    $allowedWidgets = Engine_Api::_()->optimizer()->getAllowedWidgets();
    $select = $contentTable->select()
        ->where('page_id IN (?)', $page_ids)
        ->where('name IN (?)', $allowedWidgets);

    $widgets = $contentTable->fetchAll($select);

    // Prepare visible structure
    $structure = array();
    foreach ($widgets as $widget){
      if (empty($structure[$widget->page_id])){
        $structure[$widget->page_id] = array();
      }
      $structure[$widget->page_id][] = $widget;
    }

    $this->view->structure = $structure;


    if (!$this->getRequest()->isPost()){
      return;
    }

    // Save ajax for widgets
    $ajax_widgets = $this->_getParam('widgets');
    foreach ($widgets as $widget){
      $new_params = $widget->params;
      $new_params['ajaxPostLoading'] = 0;
      if (in_array($widget->content_id, $ajax_widgets)){
        $new_params['ajaxPostLoading'] = 1;
      }
      $widget->setFromArray(array(
        'params' => $new_params
      ));
      $widget->save();
    }

    // Save changes
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('optimizer.ajax.enabled', $this->_getParam('ajax_enabled', 1));
    $settings->setSetting('optimizer.minify.enabled', $this->_getParam('minify_enabled', 1));

    $this->view->saved = true;

  }

  public function managerAction()
  {
    $status = true;

    if ($this->_getParam('replace')){
      try {
        $this->_backupFiles();
      } catch (Exception $e){
        $this->view->message = 'Backup process stopped: ' . $e->getMessage();
        $status = false;
      }
      if ($status){
        try {
          $this->_replaceFile();
        } catch (Exception $e){
          $this->view->message = 'Replace process stopped: ' . $e->getMessage();
          $status = false;
        }
      }
    } else if ($this->_getParam('revert')){
      try {
        $this->_revertFiles();
      } catch (Exception $e){
        $this->view->message = 'Revert process stopped: ' . $e->getMessage();
        $status = false;
      }
    }

    if ($status){
      $this->view->message = $this->view->translate('All files successfully has been saved!');
      $this->view->status = true;
      return $this->_forward('success' ,'utility', 'core', array(
        'parentRefresh' => true,
        'messages' => array($this->view->message)
      ));
    } else {
      $this->view->status = false;
      return $this->_forward('success' ,'utility', 'core', array(
        //'parentRefresh' => true,
        'messages' => array($this->view->message)
      ));
    }

  }

  protected function _isModifiedByOptimizer($file)
  {
    $structure = Engine_Api::_()->optimizer()->getExternalFiles();
    if (empty($structure[$file])){
      return false;
    }
    try {
      $current_file = md5($this->_readFile($file));
      $original = md5($this->_readFile($this->mod_dir . '/' . $structure[$file]));
    } catch (Exception $e){
      return false;
    }
    return $current_file == $original;
  }

  protected function _replaceFile()
  {
    $structure = Engine_Api::_()->optimizer()->getExternalFiles();
    foreach ($structure as $file => $new_file){
      $mod_file = $this->mod_dir . '/' . $new_file;
      $this->_writeToFile($file, $this->_readFile($mod_file));
    }
    return true;
  }

  protected function _revertFiles()
  {
    $structure = Engine_Api::_()->optimizer()->getExternalFiles();
    foreach ($structure as $file => $new_file){
      $backup_file = $this->backup_dir . '/' . $new_file;
      $this->_writeToFile($file, $this->_readFile($backup_file));
    }
    return true;
  }

  protected function _backupFiles()
  {
    $structure = Engine_Api::_()->optimizer()->getExternalFiles();
    foreach ($structure as $file => $new_file){
      $backup_file = $this->backup_dir . '/' . $new_file;
      $this->_writeToFile($backup_file, $this->_readFile($file));
    }
    // Save date of backup
    $date_file = $this->backup_dir . '/' . 'date.txt';
    $this->_writeToFile($date_file, date('d.m.y H:i.s'));
    return true;
  }

  protected function _writeToFile($file, $content)
  {
    $f = fopen($file, 'w');
    if (!$f){
      throw new Exception('Cannot to open file '.$file.' to write.');
    }
    fwrite($f, $content);
    fclose($f);
    return true;
  }

  protected function _readFile($file)
  {
    if (!is_readable($file)){
      throw new Exception($file . ' is not exists or not readable. Try set 0777 by FTP or SSH');
    }
    $f = fopen($file, 'r');
    if (!$f){
      throw new Exception($file . ' is not readable. Try set 0777 by FTP or SSH');
    }
    $content = fread($f, filesize($file));
    fclose($f);
    return $content;
  }

}