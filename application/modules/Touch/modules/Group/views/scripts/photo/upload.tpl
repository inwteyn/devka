<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: upload.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<script type="text/javascript">
  (function(){

  if(Touch.isIPhone())
  {
    window.uploadedPhotos = [];
    window.photoDeleteUrl = "<?php echo $this->url(array('module'=>'group', 'controller'=>'photo', 'action'=>'delete'), 'default'); ?>";
    window.Picup.responseCallback = function(response){
      if ($type(response.photo_id) == 'number'){
        uploadedPhotos[uploadedPhotos.length] = response.photo_id;
        $('photos').set('value', uploadedPhotos);
      }
      multiSelect.iPhone_addListRow($('iPhone-file-button'), photoDeleteUrl, response.photo_name, response, function (){
        if ($type(response.photo_id) == 'number'){
          var index = uploadedPhotos.indexOf(response.photo_id);
          if (index != -1){
            delete uploadedPhotos[index];
          }
          $('photos').set('value', uploadedPhotos);
        }
      });
    }
  }


  window.multiSelect = new MultiSelector();

  en4.core.runonce.add(function() {
    multiSelect.bind('file-wrapper', 2);
    multiSelect.addElement($('file'));
  });
  })();
</script>

<div id="navigation_content">
  <div class="layout_content">
    <?php echo $this->form->setAttrib('class', 'global_form touchupload touch-multi-upload')->render($this); ?>
  </div>
</div>

