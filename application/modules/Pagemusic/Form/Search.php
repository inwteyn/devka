<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Search.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pagemusic_Form_Search extends Engine_Form
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

    parent::init();

    $this->addElement('Text', 'search', array(
      'label' => 'Search Music:'
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Playlists',
        '2' => 'My Friend\'s Playlists',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
      'onchange' => 'this.form.submit();',
    ));
  }
}
