<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: photo.tpl 7695 2010-10-23 01:26:50Z john $
 * @author     John
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function (){

    if(Touch.isIPhone())
    {
      window.Picup.responseCallback = function(response){
        $('lassoImg').set('src', response.profile_photo);
        $('previewimage').set('src', response.icon_photo);
      }
    }

  });
</script>
<?php if( count($this->navigation) > 0 ): ?>
<h3 class="edit_profile_headline">
  <?php echo $this->translate('Edit My Profile');?>
</h3>
<?php
		// Render the menu
  $this->navigation()
        ->menu()
        ->getContainer()->title = $this->translate('Edit My Profile');
		echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('navigation/index.tpl', 'touch'))
      ->render();
?>
<?php endif; ?>
<div id="navigation_content">
<?php echo $this->form->setAttrib('class', 'global_form touchupload touch_profile_photo_upload')->render($this) ?>
  </div>