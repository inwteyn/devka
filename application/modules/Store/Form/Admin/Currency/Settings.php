<?php

class Store_Form_Admin_Currency_Settings extends Engine_Form
{
    public function init()
    {
        $this->setTitle('Store Currency Settings');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('checkbox', 'multi_currency_enabled', array(
            'value' => $settings->getSetting('hestore.multi_currency.enabled', true),
            'description' => 'Use multi currency for your stores.'
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => 'true'
        ));
    }
}