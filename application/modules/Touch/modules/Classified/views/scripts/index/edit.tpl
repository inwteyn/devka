<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>


<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    //'topLevelId' => (int) @$this->topLevelId,
    //'topLevelValue' => (int) @$this->topLevelValue
  ))
?>

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

<div id="navigation_content">

  <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have already created the maximum number of classified listings allowed.');?>
        <?php echo $this->translate('If you would like to create a new listing, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'classified_extended'));?>
      </span>
    </div>
    <br/>
  <?php else:?>
    <div class="layout_content">
      <?php echo $this->form->setAttrib('class', 'global_form touchupload')->render($this);?>
    </div>
  <?php endif; ?>

</div>

