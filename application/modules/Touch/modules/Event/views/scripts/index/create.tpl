<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */


?>

<div class="touch_box">

  <?php if ($this->parent_type == 'group'):?>
    <h3>
      <?php echo $this->group->__toString() ?>
      <?php echo $this->translate('&#187; Events');?>
    </h3>

  <?php else :?>

    <?php if( count($this->navigation) > 0 ): ?>
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->setPartial(array('navigation/index.tpl', 'touch'))
          ->render();
      ?>
    <?php endif; ?>

  <?php endif;?>

</div>

<script type="text/javascript">

en4.core.runonce.add(function (){

  if(Touch.isIPhone())
  {
    window.Picup.responseCallback = function(response){
      if ($type(response.photo_id) == 'number'){
        $('photo_id').set('value', response.photo_id);
      }
    }
  }

});

</script>

<div id="navigation_content">
  <div class="layout_content">
    <?php echo $this->form->setAttrib('class', 'global_form touchupload')->render($this) ?>
  </div>
</div>


