<h2><?php echo $this->translate("Page Video Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='page_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php echo $this->content()->renderWidget('page.admin-settings-menu', array('active_item' => 'page_admin_main_documents')); ?>

<?php echo $this->action("frame","index","hecore"); ?>

<h3><?php echo $this->translate("Document Settings") ?></h3>

<div class="settings admin_home_middle" style="clear: none;">
    <div class="settings">
        <?php $this->form->getDecorator('Description')->setOption('escape', false); ?>
        <?php echo $this->form->render($this); ?>
    </div>
</div>