<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallSmiles.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallSmiles extends Zend_View_Helper_Abstract
{
  protected $wallSmiles;
  protected $version;
  public function __construct(){
    $this->wallSmiles = Engine_Api::_()->getDbTable('smiles', 'wall')->getPaginator()->getCurrentItems();
    $this->version = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core')->version;
  }
  public function _baseUrl()
  {
    $base_url = '';
    if (version_compare($this->version, '4.1.8', '>=')){
      $base_url = $this->view->layout()->staticBaseUrl;
    } else {
      $base_url = $this->view->baseUrl() . '/';
    }
    return $base_url;

  }

  public function wallSmiles()
  {
    return $this;
  }

  public function replaceContent($content)
  {
    $base_url = $this->_baseUrl();

    $smiles = array();

    foreach ($this->wallSmiles as $item){
      $smiles[] = $item;
    }
    usort($smiles, array($this, "sortSmiles"));


    foreach ($smiles as $item){

      $src = '';
      if ($item->file_id){

      } else {
        $src = $base_url . $item->file_src;
      }
      $html = '<img src="'.$src.'" class="wall_smile" alt="'.$item->title.'" />';
      $list_tag = array();
      foreach (explode(',', $item->tag) as $tag){
        $list_tag[] = trim($tag);
      }
      $content = str_ireplace($list_tag, $html, $content);
    }

    return $content;

  }

  protected  function sortSmiles($a, $b)
  {
    return strlen( (string) $a->tag ) < strlen( (string) $b->tag );
  }

  public function getJson()
  {
    $base_url = $this->_baseUrl();
    $data = array();


    foreach ($this->wallSmiles as $item){
      $src = '';
      if ($item->file_id){

      } else {
        $src = $base_url . $item->file_src;
      }
      $html = '<img src="'.$src.'" class="wall_smile" alt="'.$item->title.'" />';
      $json_item = $item->toArray();
      $json_item['html'] = $html;
      $json_item['title'] = $this->view->translate('WALL_' . strtoupper(str_replace(" ", "_", $json_item['title'])));

      $list_tag = array();
      foreach (explode(',', $item->tag) as $tag){
        $list_tag[] = trim($tag);
      }
      $index_tag = (empty($list_tag[0])) ? '': $list_tag[0];
      $json_item['index_tag'] = trim($index_tag);
      $data[] = $json_item;
    }

    return Zend_Json::encode($data);
  }

}
