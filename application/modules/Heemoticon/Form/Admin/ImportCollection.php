<?php

/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 22.04.2015
 * Time: 13:45
 */
class Heemoticon_Form_Admin_ImportCollection extends Engine_Form
{
    public function init()
    {
        $this->addElement('File', 'import',array(
          'accept'=> ".zip"
        ));
        $this->import->addValidator('Extension', false, 'zip');

        $this->addElement('Button', 'Import', array(
            'label' => 'Import Collection',
            'type' => 'submit'
        ));
    }
}