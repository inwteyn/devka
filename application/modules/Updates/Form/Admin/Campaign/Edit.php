<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Module.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Form_Admin_Campaign_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('UPDATES_Campaign Editor')
      ->setDescription('UPDATES_VIEWS_SCRIPTS_ADMINCNLAYOUT_INDEX_LAYOUTMANAGER_DESC')
      ->loadDefaultDecorators();


    $this->setAttribs(array(
      'id' => 'campaign_form',
      'class' => 'global_form_box',
      'onsubmit' => 'return cleanHiddenFields();'
    ));

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $recievers = array(
      'all_users'=>'UPDATES_All Users',
      'member_levels'=>'UPDATES_Member Levels',
      'networks'=>'UPDATES_Networks',
      'profile_types'=>'UPDATES_Profile Types',
      'custom'=>'UPDATES_Custom',
    );

    $i = 0;
    $this->addElement('select', 'users', array(
      'label' => 'UPDATES_Send to:',
      'multiOptions' => $recievers,
      'onchange' => "changeRecievers($(this))",
      'order' => $i++,
      //'style' => 'float:left',
      'class' => 'count_recipients',
      'description' => 'UPDATES_Send to Description',
    ));

    $total_subscribers = Engine_Api::_()->getDbtable('subscribers', 'updates')->getTotalSubscribedEmails();
    $this->addElement('checkbox', 'subscribers', array(
      'Label'=>'Include external subscribers. ( Total: '. $total_subscribers . ' )',
      'decorators'=>array(
        'ViewHelper',
        'Label',
        array('HtmlTag3', array('tag'=>'div', 'id'=>'include_subscribers', 'style'=>'float:left;margin-left: 5px'))
      ),
      'onclick' => '$(this).blur()',
      'order' => $i++,
      'class' => 'count_recipients'
    ));

    //MEMBER LEVELS
    $levelTb = Engine_Api::_()->getDbtable('levels', 'authorization');
    $user_levels = $levelTb->fetchAll($levelTb->select());
    $public = $levelTb->getPublicLevel();
    $memberLevels = array(''=>'...');
    foreach ($user_levels as $user_level)
    {
      if ($public->level_id != $user_level->level_id)
        $memberLevels[$user_level->level_id]= $user_level->getTitle();
    }
    $this->addElement('multiselect', 'member_levels', array(
      'Label'=>'UPDATES_Select Member Levels',
      'multiOptions'=>$memberLevels,
      'class'=>'selectors count_recipients',
      'decorators'=>array( 'ViewHelper', 'Label', 'ElementType',),
      'order' => $i++,
    ));
    $this->member_levels->addDecorator('HtmlTag3', array('tag'=>'div', 'class'=>'recievers_select_item custom_selectors', 'id'=>'member_levels_select', 'style'=>'display:none;',));

    //NETWORKS
    $networkTb = Engine_Api::_()->getDbtable('networks', 'network');
    $networkItems = $networkTb->fetchAll($networkTb->select());
    $networks = array(''=>'...');
    foreach($networkItems as $network){
      $networks[$network->network_id] = $network->title;
    }
    $this->addElement('multiselect', 'networks', array(
      'Label'=>'UPDATES_Select Networks',
      'multiOptions'=>$networks,
      'class'=>'selectors count_recipients',
      'decorators'=>array( 'ViewHelper', 'Label', 'ElementType',),
      'order' => $i++
    ));
    $this->networks->addDecorator('HtmlTag3', array('tag'=>'div', 'class'=>'recievers_select_item custom_selectors', 'id'=>'networks_select', 'style'=>'display:none;',));

    //PROFILE TYPES
    $fieldTb = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions('user');
    $profileTypes = array(''=>'...');
    foreach( $fieldTb->getRowsMatching('field_id', 1) as $option ) {
      $profileTypes[$option->option_id] = $option->label;
    }
    $path = Engine_Api::_()->getModuleBootstrap('updates')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $this->addElement('select', 'profile_type', array(
      'Label'=>'UPDATES_Select Profile Type',
      'multiOptions'=>$profileTypes,
      'class'=>'selectors count_recipients',
      'decorators'=>array( 'ViewHelper', 'Label', 'ElementType',),
      'onchange'=>"$$('.profile_type_options').setStyle('display', 'none'); $$('.'+$(this).value+'_options').setStyle('display', '');",
      'order' => $i++
    ));
    $this->profile_type->addDecorator('HtmlTag3', array('tag'=>'div', 'id'=>'profile_type_select', 'class'=>'custom_selectors', 'style'=>'display:none;',));

    //PROFILE FIELDS
    /*$form2 = new Fields_Form_Standard(array(
      'item' => Engine_Api::_()->user()->getViewer(),
      'topLevelId' => 0,
      'topLevelValue' => 0,
    ));


    $elements = $form2->getElements();
    $elementNames = array();
    foreach ($elements as $key => $element){
      $_pos1 = strpos($key, '_');
      $_pos2 = strpos($key, '_', $_pos1+1);
      $profile_type = substr($key, $_pos1+1, $_pos2-($_pos1+1));

      $element->addDecorator('HtmlTag3', array('tag'=>'div', 'id'=>$key.'_option', 'class'=>$profile_type.'_options' .' profile_type_options'));
      $element->setRequired(false);
      $element->setOrder($i++);
      $element->setOptions(array('allowEmpty'=>true,'onchange'=>'', 'class'=>'count_recipients'));

      switch(get_class($element)){
        case 'Fields_Form_Element_FirstName':;
                                             $elementNames[] = $element->getName();
                                             $element->setOptions(array('id'=>'first_name_'.$profile_type));
                                             $this->addElement($element);
                                             break;

        case 'Fields_Form_Element_LastName':;
                                            $elementNames[] = $element->getName();
                                            $element->setOptions(array('id'=>'last_name_'.$profile_type));
                                            $this->addElement($element);
                                            break;

        case 'Fields_Form_Element_Gender':;
                                          $elementNames[] = $element->getName();
                                          $element->setOptions(array('id'=>'gender_'.$profile_type));
                                          $this->addElement($element);
                                          break;

        case 'Fields_Form_Element_Birthdate':
          $ages = array();
          for ($j=13; $j<=100; $j++)
          {
            $ages[$j] = $j;
          }

          $this->addElement('Select', 'agemin_' . $profile_type, array(
            'multiOptions'=>$ages,
            'order'=>$i++,
            'decorators'=>array(
              'ViewHelper',
            ),
            'class'=>'count_recipients',
          ));

          $this->addElement('Select', 'agemax_' . $profile_type, array(
            'multiOptions'=>$ages,
            'order'=>$i++,
            'decorators'=>array(
              'ViewHelper',
            ),
            'class'=>'count_recipients',
          ));

          $this->addDisplayGroup(array('agemin_' . $profile_type, 'agemax_' . $profile_type), 'profile_ages', array(
            'Decorators'=>array(
              'Description',
              'FormElements',
              'DivDivDivWrapper',
              array('HtmlTag3', array('tag'=>'div', 'id'=>$key.'_option', 'class'=>$profile_type.'_options profile_ages' .' profile_type_options')),
            ),
          ));
          break;
      }
    }*/

    $this->addDisplayGroup(array('member_levels', 'networks', 'profile_type'), 'recievers_select', array(
      'decorators' => array(
        'Description',
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));

    $recievers_select = $this->getDisplayGroup('recievers_select');
    $recievers_select->addDecorator('HtmlTag3', array(
      'tag'=>'div',
      'class'=>'recievers_select',
      'id'=>'recievers_select',
      'style'=>'display:none',
    ));

    //CUSTOM FIELDS
    $customFields = new Updates_Form_Fields();
    $customFields->removeElement('profile_type');
    $customFields->removeElement('displayname');
    $customFields->removeElement('separator1');
    $customFields->removeElement('separator2');
    $this->addSubForms(array(
      'fields' => $customFields
      //'type' => 'user'
    ));

    //PROFILE TYPES MULTISELECT
    $this->addElement('multiselect', 'profile_types', array(
      'Description'=>'UPDATES_Select Profile Types',
      'multiOptions'=>$profileTypes,
      'class'=>'selectors count_recipients',
      'order'=>$i++
    ));
    $this->profile_types->addDecorator('HtmlTag3', array('tag'=>'div', 'class'=>'recievers_select_item', 'id'=>'profile_types_select', 'style'=>'display:none; margin-bottom: 5px',));

    $this->addElement('select', 'profile_photo', array(
      'label' => 'UPDATES_Profile photo:',
      'Description'=>'UPDATES_Select members with or without Profile Photo',
      'multiOptions'=>array('', 'UPDATES_Members with Profile Photo', 'UPDATES_Members without Profile Photo'),
      'class'=>'count_recipients',
      'order'=>$i++
    ));

    $this->addElement('text', 'last_logged_count', array(
      'label' => 'UPDATES_Last Logged in:',
      'order' => $i++,
      'style' => 'float:left',
      'class' => 'count_recipients',
      'description' => 'UPDATES_Enter a days/weeks/months of last time logged in members.',
    ));

    $this->addElement('select', 'last_logged_type', array(
      'Label'=>'UPDATES_ago.',
      'multiOptions'=>array('days'=>'UPDATES_days', 'weeks'=>'UPDATES_weeks', 'months'=> 'UPDATES_months'),
      'decorators'=>array(
        'ViewHelper',
        'Label',
        array('HtmlTag3', array('tag'=>'div', 'id'=>'last_logged_type-element', 'style'=>'float:left;margin-left: 5px'))
      ),
      'order' => $i++,
      'class' => 'count_recipients'
    ));

    $this->addElement('Heading', 'recipients', array(
      'label' => 'UPDATES_Total Recipients:',
      'order'=>$i++
    ));

    $this->addElement('Heading', 'spacer', array(
      'order'=>$i++
    ));

    //CAMPAIGN TYPE
    $this->addElement('Select', 'campaign_type', array(
      'Label'=>"UPDATES_Campaign Type",
      'Description'=>'UPDATES_Campaign Type Description',
      'multiOptions'=>array('instant'=>'UPDATES_Instant Campaign', 'schedule'=>'UPDATES_Schedule Campaign'),
      'onChange'=>"changeCampaignType()",
      'order'=>$i++
    ));

    $planned_date = new Engine_Form_Element_CalendarDateTime('planned_date');
    $planned_date->setDescription("UPDATES_Planned Time");
    $planned_date->setOptions(array('style'=>'display:none'));
    $planned_date->addDecorator('HtmlTag3', array('tag'=>'div', 'id'=>'planned_date_conteiner', 'style'=>'display:none;',));
    $planned_date->setOrder($i++);
    $this->addElement($planned_date);

    // OTHER FIELDS
    $this->addElement('text', 'subject', array(
      'label' => 'UPDATES_Subject:',
      'required'=>true,
      'trim'=>true,
      'style'=>'width: 430px',
      'order'=>$i++
    ));

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $coreItem = $modulesTbl->getModule('core')->toArray();

    //Activity
    if (version_compare($coreItem['version'], '4.7.0') < 0) {
      $editorOptions = array(
        'plugins'=>'insertdatetime, emotions, table, fullscreen, media, preview, paste, print, visualchars, advlink, advimage, template, searchreplace,directionality,nonbreaking, contextmenu,noneditable,xhtmlxtras, save',
        'theme_advanced_buttons1' => 'newdocument, code,|, fullscreen, |,preview, print, |, help',
        'theme_advanced_buttons2' => 'undo, redo,|, cleanup, removeformat, |,search,replace,|, cut,copy,paste,pastetext, pasteword, |, image, insertdate, inserttime, |, tablecontrols',
        'theme_advanced_buttons3' => 'link, unlink, anchor,|,hr,sub,sup,charmap,|, nonbreaking, |,visualchars,|, justifyleft, justifycenter, justifyright, justifyfull, |, bullist, numlist, |, outdent, indent, |, ltr,rtl, |, blockquote',
        'theme_advanced_buttons4' => 'formatselect, fontselect, fontsizeselect, bold, italic, underline, strikethrough, forecolor, backcolor, styleprops,|,cite,abbr,acronym,del,ins',
        'theme_advanced_statusbar_location' => "bottom",
        'height'=>'350px',
        'theme_advanced_resizing'=>true,
      );
    } else {
      $editorOptions = array(
        'height'=>'350px',
        'theme_advanced_resizing'=>true,
      );
    }

    $this->addElement('TinyMce', 'message', array(
      'editorOptions' => $editorOptions,
      'required'=>true,
      'decorators' => array(
        'ViewHelper',
        'AvailableVariables',
        array('HtmlTag2', array('tag' => 'div', 'id'=>'message-wrapper', 'class'=>'form-wrapper')),
        'Description',
      ),
      'allowEmpty' => false,
      'order'=>$i++
    ));

    $this->addElement('hidden', 'campaign_id', array(
      'type'=>'hidden',
      'order'=>$i++,
    ));

    $this->addElement('hidden', 'recipients_qty', array(
      'type'=>'hidden',
      'order'=>$i++,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'UPDATES_Send Campaign',
      'type' => 'submit',
      'ignore' => true,
      'order'=>$i++,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Button', 'test_email', array(
      'type'=>'button',
      'label' => 'UPDATES_Send test email',
      'link'=> true,
      'onclick' => 'testemail()',
      'order'=>$i++,
      'decorators' => array('ViewHelper'),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'type'=>'cancel',
      'label' => 'UPDATES_cancel',
      'link'=> true,
      'prependText' => ' or ',
      'href' => $this->getView()->url(array('module'=>'updates', 'controller'=>'campaign', 'action'=>'index')),
      'order'=>$i++,
      'decorators' => array('ViewHelper'),
    ));

    $this->addDisplayGroup(array('submit', 'test_email', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder($i);
    $button_group->loadDefaultDecorators();
  }

  public function isValid($data)
  {
    $status = parent::isValid($data);

    if ( 'schedule' == $data['campaign_type'] && strtotime(Engine_Api::_()->updates()->getDatetime())> strtotime($data['planned_date']))
    {
      $msg = $this->getTranslator()->translate("UPDATES_Wrong value given! Please check field 'Planned Time', try again.");
      $this->addError($msg);
      $this->_errorsExist = true;
      $status = false;
    }

    return $status;
  }
}