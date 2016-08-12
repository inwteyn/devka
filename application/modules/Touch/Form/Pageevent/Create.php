<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Form.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Pageevent_Create extends Touch_Form_Standard
{

  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Create New Event')
      ->setAttrib('id', 'event_create_form')
      ->setMethod("POST");

    $this->setAction($this->getView()->url());

    // Title
    $this->addElement('Text', 'title', array(
      'label' => 'Event Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $title = $this->getElement('title');

    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Event Description',
      'maxlength' => '512',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));


    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $start->helper = 'touchFormCalendarDateTime';
    $this->addElement($start);


    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $end->helper = 'touchFormCalendarDateTime';
    $this->addElement($end);

    // Host
    if ($this->_parent_type == 'user')
    {
      $this->addElement('Text', 'host', array(
        'label' => 'Host',
        'filters' => array(
          new Engine_Filter_Censor(),
        ),
      ));
    }
    // Location
    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    if (!isset($_FILES['photo'])){
      // ignore Zend_Validate_File_Upload::INI_SIZE
      $_FILES['photo'] = array(
        'name' => '',
        'type' => '',
        'tmp_name' => '',
        'error' => 4,
        'size' => 0
      );
    }

    $this->addElement('Hidden', 'photo_id', array());

    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Main Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // Approval
    $this->addElement('Checkbox', 'approval', array(
      'label' => 'People must be invited to RSVP for this event',
    ));

    // Invite
    $this->addElement('Checkbox', 'auth_invite', array(
      'label' => 'Invited guests can invite other people as well',
      'value' => True
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }

}
