<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
class PageVideo_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ))
      ->setMethod('GET')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Text', 'text', array(
      'label' => 'Search',
    ));

    $this->addElement('Hidden', 'tag');

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'view', array(
      'label' => 'View',
      'multiOptions' => array(
        '1' => 'Everyone\'s Videos',
        '2' => 'My Friend\'s Videos',
      ),
      'onchange' => 'this.form.submit();',
    ));
  }
}
