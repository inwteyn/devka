<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageinstagram_Form_Admin_Global extends Engine_Form
{
    public function init()
    {
        $this->setTitle('PAGEINSTAGRAM_SETTINGS_TITLE')->setDescription('PAGEINSTAGRAM_SETTINGS_DESCRIPTION');

        $settings = Engine_Api::_()->getDbTable('settings', 'core');

        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);


        $this->addElement('Text', 'page_item_on_page', array(
            'label' => 'Page_item_on_page',
            'description' => 'Page_item_on_page_description',
            'value' => $settings->getSetting('page.count.item.on.page', 20)
        ));

        $this->addElement('Text', 'page_instagram_option', array(
            'label' => 'Page_instagram_option',
            'value' => $settings->getSetting('page.instagram.option')
        ));


        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}