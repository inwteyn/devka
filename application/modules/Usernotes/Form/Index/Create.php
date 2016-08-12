<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Form_Index_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Usernote')
      ->setDescription('Leave your note about this user below:')
      ->setMethod('post');

    $this->addElement('Textarea', 'note', array(
      'label' => 'Note',
      'filters' => array('StripTags'),
    ));

    $this->addElement('Hidden', 'user_id');
    $this->addElement('Submit', 'he_usernotes_save', array(
      'label' => 'Save',
      'onclick' => 'return false;',
    ));
  }

  public function save()
  {
    $db = Engine_Api::_()->getDbtable('usernote', 'usernotes');

    $owner_id      = Engine_Api::_()->user()->getViewer()->getIdentity();
    $user_id       = $this->getElement('user_id')->getValue();
    $note          = $this->getElement('note')->getValue();
    $creation_date = new Zend_Db_Expr('NOW()');

    $usernote = Engine_Api::_()->usernotes()->getUsernoteByOwner($owner_id, $user_id);

    if ($usernote)
    {
      $data = array(
        'note'=>$note,
        'creation_date'=>$creation_date
      );

      $where = $db->getAdapter()->quoteInto('usernote_id = ?', $usernote->usernote_id);
      $db->update($data, $where);
    }
    else
    {
      // try/catch being done in controller
      $usernote = $db->createRow();
      $usernote->owner_id      = $owner_id;
      $usernote->user_id       = $user_id;
      $usernote->note          = $note;
      $usernote->creation_date = $creation_date;
      $usernote->save();
    }

    return $usernote->usernote_id;
  }
}