<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hecontest_Form_Paid extends Engine_Form
{
    protected $activeContest;

    public $_activeContestId = 0;
    protected $_isTouch;

    public function __construct($caption = "", $description = "", $isTouch = false)
    {
        if (Engine_Api::_()->core()->hasSubject('hecontest')) {
            $contest = Engine_Api::_()->core()->getSubject('hecontest');
        } else {
            $contestTbl = Engine_Api::_()->getDbTable("hecontests", "hecontest");
            $contest = $contestTbl->getActiveContest();
        }
        $this->activeContest = $contest;
        if ($this->activeContest)
            $this->_activeContestId = $this->activeContest->getIdentity();
        $this->_isTouch = $isTouch;
        parent::__construct();
    }

    public function init()
    {
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $view = Zend_Registry::get('Zend_View');
        $isParticipant = false;
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($this->activeContest) {
            $descr = $this->activeContest->getDescription();
            $img = $this->activeContest->getPhotoUrl();
            $isParticipant = $this->activeContest->isParticipant($viewer->getIdentity());
            $sponsorTxt = $this->activeContest->getSponsorHtml('hecontest_join_like_btn');

            $infoBody = <<<COVER
        <div id="hecontest_join_info">
            <div class="hecontest_sponsor_label">
                <span>{$view->translate('HECONTEST_This Contest Sponsored by:')}</span>
                <br/>
                <span>{$sponsorTxt}</span>
            </div>
            <div class="cover-wrapper">
                <div class="cover-wrapper-descr">
                    {$descr}
                </div>
                <div class="cover-wrapper-img">
                    <img src="{$img}">
                </div>
                <div class="clear"></div>
            </div>
        </div>
COVER;
            $this->addElement('Dummy', 'info-body', array(
                'content' => $infoBody
            ));
        }

        if (!$viewer->getIdentity() || $isParticipant) {
            return;
        }

        if(!$this->_isTouch) {
            $fancyUpload = new Engine_Form_Element_FancyUpload('file');
            $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array('viewScript' => 'application/modules/Hecontest/views/scripts/_FancyUpload.tpl', 'placement' => '')
                );

            Engine_Form::addDefaultDecorators($fancyUpload);
            $this->addElement($fancyUpload);
        }


        $this->addElement('Textarea', 'hecontestdescription', array(
            'required' => true,
            'maxlength' => '10000',
            'class' => 'hecontest-textarea',
            'placeholder' => 'Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_StringLength(array('max' => 10000)),
            ),
        ));

        if ($this->activeContest && $this->activeContest->terms) {
            $translator = Zend_Registry::get("Zend_Translate");
            $this->addElement('Checkbox', 'terms', array(
                'label' => sprintf($translator->translate('HECONTEST_Join form terms'), "terms"),
                'required' => true
            ));

            $termsBody = <<<COVER
            <div id="hecontest_join_terms">
              <div class="terms-wrapper">
                {$this->activeContest->terms}
              </div>
            </div>
COVER;
            $this->addElement('Dummy', 'terms-body', array(
                'content' => $termsBody
            ));
        }
        if ($this->activeContest) {
            $this->addElement('Hidden', 'contest_id', array(
              'value' => $this->activeContest->getIdentity(),
            ));
        }
        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'button',
            'ignore' => true,
            'onclick' => 'hecontestCore.joinContest(this);',
            'class' => 'hecontest_widget_button',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'Cancel',
            'link' => true,
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'href' => '',
            'onclick' => 'hecontestCore.hideJoinForm();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $dgBtns = array('submit', 'cancel');

        $this->addDisplayGroup($dgBtns, 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));

    }

}
