<h2>
    <?php echo $this->translate('Hire-Expert Advanced Members') ?>
</h2>
<?php
$this->headLink()
  ->appendStylesheet($this->advmembersBaseUrl() . 'application/modules/Headvancedmembers/externals/styles/admin/core.css');?>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<h3>Global Settings</h3>
<div class="form_headvancedmembers">
    <?php echo $this->form->render($this) ?>
</div>