<h2><?php echo $this->translate("Page Addons"); ?></h2>

<?php if( count($this->navigation) ): ?>
    <div class='page_admin_tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
        ?>
    </div>
<?php endif; ?>

<?php echo $this->content()->renderWidget('page.admin-settings-menu'); ?>