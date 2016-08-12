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
    <?php if( $this->isLastSuperAdmin ):?>
      <div class="tip">
        <span>
          <?php echo $this->translate('This is the last super admin account. Please reconsider before deleting this account.'); ?>
        </span>
      </div>
    <?php endif;?>

    <?php echo $this->form->setAttrib('id', 'user_form_settings_delete')->render($this) ?>
  </div>
</div>

