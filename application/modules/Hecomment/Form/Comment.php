<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecomment
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */
class Hecomment_Form_Comment extends Engine_Form
{
    public function init()
    {
        $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->setAttrib('class', 'hecomment-form-class')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $view = Zend_Registry::get('Zend_View');

        //$allowed_html = Engine_Api::_()->getApi('settings', 'core')->core_general_commenthtml;
        $viewer = Engine_Api::_()->user()->getViewer();
        $allowed_html = "";
        if ($viewer->getIdentity()) {
            $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
        }
        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.smile', 1)) {
            $sml = <<<COVER
<a class="hei hei-smile-o heemoticons" href="javascript:void(0)" onclick="getSmile(this)"></a>
COVER;
            $this->addElement('Dummy', 'smile_composer_comment', array(
                'content' => $sml,
                'order' => 2
            ));
        }

        $this->addElement('Textarea', 'body', array(
            'rows' => 1,
            'decorators' => array(
                'ViewHelper'
            ),
            'filters' => array(
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)),
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
            'placeholder' => $view->translate("WALL_COMMENT_WRITE"),
            'order' => 1
        ));

        if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) {
            $this->addElement('captcha', 'captcha', array(
                'description' => 'Please type the characters you see in the image.',
                'captcha' => 'image',
                'required' => true,
                'captchaOptions' => array(
                    'wordLen' => 6,
                    'fontSize' => '30',
                    'timeout' => 300,
                    'imgDir' => APPLICATION_PATH . '/public/temporary/',
                    'imgUrl' => $this->getView()->baseUrl() . '/public/temporary',
                    'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
                )));
        }

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'label' => 'Post Comment',
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 4
        ));

        $this->addElement('Hidden', 'action_id', array(
            'order' => 990,
            'filters' => array(
                'Int'
            ),
        ));
        $this->addElement('Hidden', 'type', array(
            'order' => 991,
            'validators' => array(
                // @todo won't work now that item types can have underscores >.>
                // 'Alnum'
            ),
        ));

        $this->addElement('Hidden', 'identity', array(
            'order' => 992,
            'validators' => array(
                'Int'
            ),
        ));
    }

    public function setIdentity($action_id)
    {
        $this
            ->setAttrib('id', 'activity-comment-form-' . $action_id)
            ->setAttrib('class', 'hecomment-form-class');
        $this->action_id
            ->setValue($action_id);
        $this->addElement('File', 'photo_comment_' . $action_id, array(
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'multiFile' => 1,
            'class' => 'wall_file_in_comment',
            'accept' => "image/*",
            'style' => 'display:none;',
            'order' => -1,
            'validators' => array(
                array('Count', false, 1),
                array('Extension', false, 'jpg,jpeg,png,gif,jpeg'),
            )
        ));
        $coreModule = Engine_Api::_()->getDbTable('modules', 'core');
        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.smile', 1) && ($coreModule->isModuleEnabled('album') || $coreModule->isModuleEnabled('advalbum'))) {
            $sml = <<<COVER
<a class="hei hei-camera" href="javascript:void(0)" id="select_photo_{$action_id}" onclick="select_file_comment({$action_id})"></a>
<script>
    Hecomment.core.comments.attachBtnEvent({$action_id});
</script>

COVER;
            $this->addElement('Dummy', 'file_button_container', array(
                'content' => $sml,
                'order' => 3
            ));
        }

        $this->addElement('Dummy', 'block-div', array(
            'content' => '<div style="width: 100%"></div>',
            'decorators' => array(
                'ViewHelper',
            ),
            'order' => 5
        ));


        $hecomments_attachments_preview = <<<COVER
                <div id="preview_comment_reply_attach_wall">
                    <div class="comment_attach_loading_wall"
                         id="comment_attach_loading_wall{$action_id}"></div>
                    <div class="comment_attach_preview_image_wall"
                         id="comment_attach_preview_image_wall{$action_id}"></div>
                </div>
COVER;
        $this->addElement('Dummy', 'hecomments_attachments_preview', array(
            'content' => $hecomments_attachments_preview,
            'order' => 6
        ));

        return $this;
    }

    public function  setUploadPhotoButton($action_id)
    {


        $this->addElement('File', 'Filedata_' . $action_id, array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'multiFile' => 1,
            'validators' => array(
                array('Count', false, 1),
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
        ));
    }

    public function renderFor($action_id)
    {
        return $this->setActionIdentity($action_id)->render();
    }
}