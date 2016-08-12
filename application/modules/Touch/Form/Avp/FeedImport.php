<?php
class Touch_Form_Avp_FeedImport extends Touch_Form_Standard
{


      public function init()
      {
            $view = Zend_Registry::get('Zend_View');
            
            $this->setTitle('Import New Video')
                 ->setAttrib('id', 'form-feed-import')
                 ->setAttrib('class', 'global_form touchform')
                 ->setAttrib('name', 'avp_create')
                 ->setAttrib('enctype','multipart/form-data')
                 ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
            
            $this->loadDefaultDecorators();
            $this->getDecorator('Description')->setOption('escape', false);
            
            $user = Engine_Api::_()->user()->getViewer();

            $this->addElement('Text', 'url', array(
                  'label' => '*Video URL',
                  'maxlength' => '100',
                  'onblur' => 'avpAcceptVideoOnlyFromYTnVM(this)',
                  'allowEmpty' => false,
                  'required' => true
            ));
            
            $this->url->addValidator(new Avp_Validate_Duplicate());

            $this->addElement('Text', 'title', array(
                  'label' => '*Video Title',
                  'maxlength' => '100',
                  'allowEmpty' => false,
                  'required' => true,
                  'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_StringLength(array('max' => '100')),
                  )
            ));

            $this->addElement('Textarea', 'description', array(
                  'label' => '*Video Description',
                  'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_EnableLinks(),
                  ),
                  'allowEmpty' => false,
                  'required' => true
            ));
            
            $availableLabels = array(
                  'everyone' => 'Everyone',
                  'owner_network' => 'Friends and Networks',
                  'owner_member_member' => 'Friends of Friends',
                  'owner_member' => 'Friends Only',
                  'owner' => 'Just Me'
            );

            $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('avp_video', $user, 'auth_view');
            $options = array_intersect_key($availableLabels, array_flip($options));

            $this->addElement('Select', 'auth_view', array(
                  'label' => 'Privacy',
                  'description' => 'Who may see this video?',
                  'multiOptions' => $options,
                  'value' => 'everyone',
            ));
            
            $this->addElement('Text', 'format', array(
                  'value' => 'smoothbox'
            ));
            
            $this->getElement('format')->getDecorator('HtmlTag2')->setOption('style', 'display: none;');

            $this->addElement('Button', 'upload', array(
                  'label' => 'Import Video',
                  'type' => 'submit',
                  'onclick' => 'setTimeout(function(){Touch.refresh(true)}, 4000)',
                  'ignore' => true
            ));
      }

}