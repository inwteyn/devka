<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photo.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Avatarstyler_Form_Photo extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'EditPhoto')
        ->setAction('avatarstyler/index/update');


    $this->addElement('Image', 'current', array(
      'label' => 'Current Photo',
      'ignore' => true,
      'onclick' => 'return false;',
    ));
    $this->addElement('Image', 'preview', array(
        'alt'=>'Select below photos as style to your avatar',
      'label' => 'Preview',
      'ignore' => true,
      'onclick' => 'return false;',
    ));
    $this->addDisplayGroup(array('current', 'preview'), 'images');

    $this->addElement('Button', 'done', array(
      'label' => 'Change Photo',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
      $this->addElement('Hidden','imgId',
          array(
              'type'=>'submit'
          ));

    $this->addDisplayGroup(array('done', 'remove'), 'buttons');
  }
}