<?php

class Touch_Form_Admin_Rate_Settings extends Engine_Form
{

  public function init()
  {
    $this
      ->setTitle('TOUCH_SETTING_TITLE')
      ->setDescription('TOUCH_SETTING_DESCRIPTION');

    $this->addElement('Checkbox', 'touch_blog_rate_browse', array(
      'label' => 'TOUCH_BLOG_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_BLOG_RATE_BROWSE',
    ));

    $this->addElement('Checkbox', 'touch_blog_rate_widget', array(
      'label' => 'TOUCH_BLOG_RATE_WIDGET_LABEL',
      'description' => 'TOUCH_BLOG_RATE_WIDGET'
    ));

    $this->addElement('Checkbox', 'touch_member_rate_browse', array(
      'label' => 'TOUCH_MEMBER_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_MEMBER_RATE_BROWSE'
    ));

    $this->addElement('Checkbox', 'touch_event_rate_browse', array(
      'label' => 'TOUCH_EVENT_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_EVENT_RATE_BROWSE'
    ));

    $this->addElement('Checkbox', 'touch_group_rate_browse', array(
      'label' => 'TOUCH_GROUP_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_GROUP_RATE_BROWSE'
    ));

    $this->addElement('Checkbox', 'touch_classified_rate_browse', array(
      'label' => 'TOUCH_CLASSIFIED_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_CLASSIFIED_RATE_BROWSE'
    ));

    $this->addElement('Checkbox', 'touch_album_rate_browse', array(
      'label' => 'TOUCH_ALBUM_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_ALBUM_RATE_BROWSE'
    ));

    $this->addElement('Checkbox', 'touch_album_rate_widget', array(
        'label' => 'TOUCH_ALBUM_RATE_WIDGET_LABEL',
        'description' => 'TOUCH_ALBUM_RATE_WIDGET'
    ));

    $this->addElement('Checkbox', 'touch_article_rate_browse', array(
      'label' => 'TOUCH_ARTICLE_RATE_BROWSE_LABEL',
      'description' => 'TOUCH_ARTICLE_RATE_BROWSE'
    ));

    $this->addElement('Checkbox', 'touch_article_rate_manage', array(
        'label' => 'TOUCH_ARTICLE_RATE_MANAGE_LABEL',
        'description' => 'TOUCH_ARTICLE_RATE_MANAGE'
    ));

    $this->addElement('Checkbox', 'touch_article_rate_widget', array(
        'label' => 'TOUCH_ARTICLE_RATE_WIDGET_LABEL',
        'description' => 'TOUCH_ARTICLE_RATE_WIDGET'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));



  }

}