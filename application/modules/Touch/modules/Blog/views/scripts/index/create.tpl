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

<div id="navigation_content">

<?php if($this->form->isErrors()): ?>

	<?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>

<?php else: ?>

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

  <div class="layout_content">
    <?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>
  </div>

<?php endif;?>

</div>



