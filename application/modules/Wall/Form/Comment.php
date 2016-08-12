<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Comment.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Comment extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->setAttrib('class', 'wall-comment-post');

    $view = Zend_Registry::get('Zend_View');

    //$allowed_html = Engine_Api::_()->getApi('settings', 'core')->core_general_commenthtml;
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowed_html = "";
    if($viewer->getIdentity()){
      $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
    }
    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.smile', 1)) {
      $sml = <<<COVER
<a class="hei hei-smile-o heemoticons" href="javascript:void(0)" onclick="getSmile(this)"></a>
COVER;
      $this->addElement('Dummy', 'smile_composer_comment', array(
        'content' => $sml
      ));
    }

    $this->addElement('Textarea', 'body', array(
      'rows' => 1,
      'decorators' => array(
        'ViewHelper'
      ),
      'filters' => array(
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)),
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
      'placeholder' => $view->translate("WALL_COMMENT_WRITE"),
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
          'imgUrl' => $this->getView()->baseUrl().'/public/temporary',
          'font' => APPLICATION_PATH . '/application/modules/Core/externals/fonts/arial.ttf'
        )));
    }

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'style' => 'display:none;',
      'label' => 'Post Comment',
      'decorators' => array(
        'ViewHelper',
      )
    ));
    
    $this->addElement('Hidden', 'action_id', array(
      'order' => 990,
      'filters' => array(
        'Int'
      ),
    ));

  }

  public function setActionIdentity($action_id)
  {
    $this
      ->setAttrib('id', 'activity-comment-form-'.$action_id)
      ->setAttrib('class', 'wall-comment-form');
    $this->action_id
      ->setValue($action_id);
    $button_submit = <<<COVER
<script>
window.addEvent('domready', function(){
var id = {$action_id};
if($('activity-comment-form-'+id)){
$('activity-comment-form-'+id).getChildren('#body')[0].addEvent('keyup',function(){
  if($('activity-comment-form-'+id).getChildren('#body')[0].value.trim() != '' ||  $('comment_attach_preview_image_wall'+id).get('html').trim() != ''){
$('activity-comment-form-'+id).getChildren('#submit')[0].setStyle('display','block');
  }else{
    $('activity-comment-form-'+id).getChildren('#submit')[0].setStyle('display','none');
  }
})
}
})

</script>
COVER;
    $this->addElement('Dummy', 'button_change_style', array(
      'content' => $button_submit
    ));
    $this->addElement('File', 'photo_comment_'.$action_id, array(
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'class' =>'wall_file_in_comment',
      'accept' => "image/*",
      'style' =>'display:none;',
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
  var file_button = $('photo_comment_{$action_id}');
  if(file_button){
    file_button.addEvent('change', function(e){
      if(window.change_select == 1){
        return;
      }
      window.change_select = 1;
      var id = {$action_id};
      var container =  $('comment_attach_preview_image_wall' + id);
      var loading =  $('comment_attach_loading_wall'+id);
      var extension = this.value.split('.');
      var last = extension.pop();
      var ext  = ['png','jpeg','jpg','gif','bmp'];
      if(!ext.contains(last)){
        window.comment_photo_query = 0;
        window.change_select = 0;
        return;
      }
      loading.setStyle('display','block');
      var  url = en4.core.baseUrl + 'wall/index/commentphoto?action_id='+id;

      var data = new FormData();
      data.append('photo_comment', this.files[0]);
      var request = new XMLHttpRequest();
      request.onreadystatechange = function(){
        if(request.readyState == 4){
          try {

            var resp = request.response;
          } catch (e){

          }
        }
        if(resp) {

          container.set('html',resp);
          $('activity-comment-form-'+id).getChildren('#submit')[0].setStyle('display','block');
          var delete_button = new Element('div', {
            'id': 'delete_' + id,
            'class': 'wpClose hei hei-times delete_photo_in_comment_button'
          }).inject(container);

          delete_button.addEvent('click', function(e){
            deleteImage(id);
          });

          loading.setStyle('display','none');
          container.setStyle('display', 'block');
          $('select_photo_'+id).setStyle('display','none');
          window.comment_photo_query = 0;
          window.change_select = 0;

        }

      };

      request.open('POST', url);
      request.send(data);

    });
  }else{
    setTimeout(function(){
    var file_button = $('photo_comment_{$action_id}');
      file_button.addEvent('change', function(e){
        if(window.change_select == 1){
          return;
        }
        window.change_select = 1;
        var id = {$action_id};
        var container =  $('comment_attach_preview_image_wall' + id);
        var loading =  $('comment_attach_loading_wall'+id);
        var extension = this.value.split('.');
        var last = extension.pop();
        var ext  = ['png','jpeg','jpg','gif','bmp'];
        if(!ext.contains(last)){
          window.comment_photo_query = 0;
          window.change_select = 0;
          return;
        }
        loading.setStyle('display','block');
        var  url = en4.core.baseUrl + 'wall/index/commentphoto?action_id='+id;

        var data = new FormData();
        data.append('photo_comment', this.files[0]);
        var request = new XMLHttpRequest();
        request.onreadystatechange = function(){
          if(request.readyState == 4){
            try {

              var resp = request.response;
            } catch (e){

            }
          }
          if(resp) {

            container.set('html',resp);

            var delete_button = new Element('div', {
              'id': 'delete_' + id,
              'class': 'wpClose hei hei-times delete_photo_in_comment_button'
            }).inject(container);

            delete_button.addEvent('click', function(e){
              deleteImage(id);
            });

            loading.setStyle('display','none');
            container.setStyle('display', 'block');
            $('select_photo_'+id).setStyle('display','none');
            window.comment_photo_query = 0;
            window.change_select = 0;

          }

        };

        request.open('POST', url);
        request.send(data);

      });
    },1500);

  }
</script>

COVER;
      $this->addElement('Dummy', 'file_button_container', array(
        'content' => $sml
      ));
    }

      //->setAttrib('onfocus', "document.getElementById('activity-comment-submit-".$action_id."').style.display = 'block';")
      //->setAttrib('onblur', "if( this.value == '' ) { document.getElementById('activity-comment-form-".$action_id."').style.display = 'none'; }");

    return $this;
  }
  public function  setUploadPhotoButton($action_id){


    $this->addElement('File', 'Filedata_'.$action_id, array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
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