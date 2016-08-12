<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 2012-06-07 11:40 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_Form_Admin_Manage_Filter extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ));

    $title = new Zend_Form_Element_Text('title');
    $title
      ->setLabel('Title')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $categoryTbl = Engine_Api::_()->getDbTable('categories', 'offers');
    $select = $categoryTbl->select()
      ->from(array('c'=>$categoryTbl->info('name')));
    $categories = $categoryTbl->fetchAll($select);
    $categories = $categories->toArray();
    $categoriesOptions = array('0' => '');
    foreach ($categories as $key => $value) {
      $categoriesOptions[$value['category_id']] = $value['title'];
    }

    $category = new Zend_Form_Element_Select('category');
    $category
      ->setLabel('Category')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($categoriesOptions);


    $this->addElements(array(
      $title,
      $category,
    ));

    $type = new Zend_Form_Element_Select('type');
    $type
      ->setLabel('Type')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
      '-1' => '',
      'paid' => 'Paid',
      'free' => 'Free',
      'condition' => 'Condition',
    ))
      ->setValue('-1');

    $enabled = new Zend_Form_Element_Select('enabled');
    $enabled
      ->setLabel('Enabled')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
      '-1' => '',
      '0' => 'Not Enabled',
      '1' => 'Enabled',
    ))
      ->setValue('-1');


    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    $this->addElements(array(
      $type,
      $enabled,
      $submit,
    ));

    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}