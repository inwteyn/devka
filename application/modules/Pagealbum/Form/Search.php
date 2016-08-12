<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 17.02.12
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Pagealbum_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();

    $this->addElement('Text', 'search', array(
      'label' => 'Search Albums:',
      'onchange' => '$(this).getParent("form").submit();',
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
      'onchange' => '$(this).getParent("form").submit();',
    ));


    $this->addElement('Select', 'view', array(
      'label' => 'View',
      'multiOptions' => array(
        '1' => 'Everyone\'s Albums',
        '2' => 'My Friend\'s Albums',
      ),
      'onchange' => '$(this).getParent("form").submit();',
    ));

    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('album');

    // prepare categories
    if( $module && $module->enabled ){
      $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
      if( count($categories) > 0 ) {
        $categories = array_merge(array('0' => 'All Categories'), $categories);
        $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories,
          'onchange' => '$(this).getParent("form").submit();',
        ));
      }
    }
  }
}
