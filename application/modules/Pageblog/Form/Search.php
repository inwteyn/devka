<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
class Pageblog_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('GET')
    ;

    $this->addElement('Text', 'search', array(
      'label' => 'Search Blogs',
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Blogs',
        '2' => 'My Friend\'s Blogs',
      ),
      'onchange' => 'this.form.submit();',
    ));



    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('blog');

    if( $module && $module->enabled ) {
      $categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();

      if( !empty($categories) && is_array($categories) ) {
        $this->addElement('Select', 'category', array(
          'label' => 'Category',
          'multiOptions' => array(
            '0' => 'All Categories',
          ),
          'onchange' => 'this.form.submit();',
        ));
        $this->getElement('category')->addMultiOptions($categories);
      }
    }

  }
}
