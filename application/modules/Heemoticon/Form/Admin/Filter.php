<?php
/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 14.03.2015
 * Time: 12:07
 */

class Heemoticon_Form_Admin_Filter extends Engine_Form
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

        $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
        $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

        $this->addElements(array(
            $title,
            $submit
        ));

        $params = array();
        foreach (array_keys($this->getValues()) as $key) {
            $params[$key] = null;
        }
    }
}