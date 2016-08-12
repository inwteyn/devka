<?php
/**
 * SocialEngine
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Form_Admin_Settings extends Engine_Form
{
    public function init()
    {
        $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

        $this->setTitle('Global Settings');
        $description = $this->getTranslator()->translate('STORE_FORM_ADMIN_SETTINGS_PAYMENT');
        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        // Elements
        $this->addElement('Radio', 'show_cart', array(
            'label' => 'STORE_Show Default Cart?',
            'description' => 'STORE_Show the default sidebar cart on the selected pages.',
            'multiOptions' => array(
                '2' => 'On all pages',
                '1' => 'Only on Store',
                '0' => 'Do not show',
            ),
        ));
        $this->show_cart->getDecorator("Description")->setOption("placement", "prepend");

        $this->addElement('Radio', 'show_mini_cart', array(
            'label' => 'STORE_Show Mini Menu Cart?',
            'description' => 'STORE_Show the the Mini Menu Cart on the selected pages.',
            'multiOptions' => array(
                '2' => 'On all pages',
                '1' => 'Only on Store',
                '0' => 'Do not show',
            ),
        ));
        $this->show_mini_cart->getDecorator("Description")->setOption("placement", "prepend");

        $this->addElement('Radio', 'digital_product', array(
            'label' => 'STORE_Enable digital product?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
        ));

        $this->addElement('Radio', 'browse_mode', array(
            'label' => 'STORE_Set Default View Mode',
            'description' => 'STORE_Set default view mode on browse products.',
            'multiOptions' => array(
                'list' => 'List Mode',
                'icons' => 'Icon Mode',
            ),
        ));
        $this->browse_mode->getDecorator("Description")->setOption("placement", "prepend");

        $this->addElement('text', 'download_count', array(
            'label' => 'STORE_Allowed Download Count',
            'description' => 'STORE_Maximum allowed download count after checking out.',
            'required' => true,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => '0',
        ));
        $this->download_count->getDecorator("Description")->setOption("placement", "append");

        // Element minimum_price
        $this->addElement('text', 'minimum_price', array(
            'label' => 'STORE_Minimum Allowed Price',
            'description' => 'STORE_Minimum Allowed Price Description',
            'required' => true,
            'value' => '0.00',
        ));
        $this->minimum_price
            ->setRequired(true)
            ->setAttribs(array('required name' => 'price', 'maxlength' => '12'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('pregReplace', array('match' => '/\s+/', 'replace' => ''))
            ->addFilter('LocalizedToNormalized')
            ->addValidator('stringLength', true, array(1, 12))
            ->addValidator('float', true, array('locale' => 'en_US'))
            ->addValidator('greaterThan', true, array('min' => 0))
            ->addValidator(new Engine_Validate_AtLeast(0));
        $this->addElement($this->minimum_price);

        $this->minimum_price->getDecorator("Description")->setOption("placement", "append");

        if ($isPageEnabled) {
            $this->addElement('Select', 'payment_mode', array(
                'label' => 'STORE_Set Default Payment Mode',
                'description' => 'STORE_Set default payment mode for store owners. If you choose Direct Payment you won\'t able to use 2Checkout gateway',
                'multiOptions' => array(
                    'client_site_store' => 'Client-Site-Store Mode',
                    'client_store' => 'Client-Store(Direct Payment) Mode',
                ),
                "onchange" => "switchType()"
            ));
            $this->payment_mode->getDecorator("Description")->setOption("placement", "prepend");
            $this->payment_mode->getDecorator("Description")->setOption("escape", false);

            $this->addElement('text', 'request_amt', array(
                'label' => 'STORE_Minimum Request Amount',
                'description' => 'STORE_Minimum allowed requests for store owners per request.',
                'required' => true,
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => '100',
            ));
            $this->request_amt->getDecorator("Description")->setOption("placement", "append");

            // Element fixed_commission
            $this->addElement('text', 'commission_fixed', array(
                'label' => 'STORE_Fixed Commission fee',
                'description' => 'STORE_Fixed Commission Fee Description',
                'required' => true,
                'validators' => array(
                    array('Float', true),
                    'AtLeast' => new Engine_Validate_AtLeast(0),
                ),
                'value' => '0.00',
            ));
            $this->commission_fixed->getDecorator("Description")->setOption("placement", "append");

            // Element commission
            $this->addElement('text', 'commission_percentage', array(
                'label' => 'STORE_Commission fee (%)',
                'description' => 'STORE_Commission Fee as a PERCENT',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => '0',
            ));
            $this->commission_percentage->getDecorator("Description")->setOption("placement", "append");
        }

        $this->addElement('Radio', 'new_shipping', array(
            'label' => 'STORE_Shipping Settings',
            'description' => 'STORE_Use new shipping settings.',
            'multiOptions' => array(
                '1' => 'STORE_New shipping settings',
                '0' => 'STORE_Old shipping settings',
            ),
        ));
        $this->new_shipping->getDecorator("Description")->setOption("placement", "prepend");

        $this->addElement('Radio', 'free_products', array(
            'label' => 'STORE_Free Products',
            'description' => 'STORE_Free Allow',
            'multiOptions' => array(
                '1' => 'STORE_Allow free yes',
                '0' => 'STORE_Allow free no',
            ),
        ));
        $this->free_products->getDecorator("Description")->setOption("placement", "prepend");

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

    public function isValid($data)
    {
        if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
            /**
             * @var $atLeast Engine_Validate_AtLeast
             */
            $element = $this->getElement('minimum_price');
            $atLeast = $element->getValidator('AtLeast');
            $minimum_price = round(($data['commission_fixed'] / 100) * $data['commission_percentage'] + $data['commission_fixed'] + 0.15, 2);
            $atLeast->setMin($minimum_price);
        }

        return parent::isValid($data);
    }
}