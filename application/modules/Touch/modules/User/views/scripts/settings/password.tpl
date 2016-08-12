
<?php if(!$this->posted && count($this->navigation) > 0 ): ?>
<h3 class="settings_headline">
  <?php echo $this->translate('My Settings');?>
</h3>

<?php
		// Render the menu
		echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>
<div>
  <div id="navigation_content">
    <?php echo $this->form->setAttrib('class', 'global_form touchform')->render($this) ?>
  </div>
</div>
