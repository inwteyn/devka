<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-07-22 11:18:13 ulan $
 * @author     Ulan
 */
?>

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
<div id="navigation_content">
  <div class="layout_content">
    <?php echo $this->form->setTitle('Edit Article')->setDescription('Edit your article below, then click "Save Changes" to save your article.')->setAttrib('class', 'global_form touchform')->render($this) ?>
  </div>
</div>
<?php endif;?>
