<?php if (count($this->navigation)): ?>
    <div class='page_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php echo $this->content()->renderWidget('page.admin-settings-menu', array('active_item' => 'page_admin_main_instagram')); ?>

<?php echo $this->action("frame","index","hecore"); ?>

<div class="settings admin_home_middle" style="clear: none;">
    <div class="settings">
        <?php
        $this->form->getDecorator('description')->setOption('escape', false);
        echo $this->form->render($this);

        ?>
    </div>
</div>