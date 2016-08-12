<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 27.02.12
 * Time: 14:27
 * To change this template use File | Settings | File Templates.
 */
class Touch_Model_Integration extends Core_Model_Item_Abstract
{
  private $status_assoc = array(
    'compatible',
    'potential',
    'expired',
    'incompatible'
  );
  public function getInfoUrl(){
    return $this->info_url;
  }
  public function isEnabled(){
    return $this->enabled ? true : false;
  }
  public function getAuthor(){
    return $this->author;
  }

  public function getStatus($assoc = false){
    $module_ver = substr(Engine_Api::_()->getDbTable('modules', 'core')->getModule($this->name)->version, 0, 5);

    $min_ver = substr($this->min_version, 0, 5);
    $max_ver = substr($this->max_version, 0, 5);
    if(!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($this->name))
      $status = 1;
    elseif($module_ver<=$max_ver && $module_ver>=$min_ver)
      $status = 0;
    elseif($module_ver>$max_ver){
        $status = 2;

    }else
      $status = 3;
    return $assoc ? $this->status_assoc[$status] : $status;
  }
  private function versionToInt($version){
    return ((int)str_replace('.', '', $version));
  }
}
