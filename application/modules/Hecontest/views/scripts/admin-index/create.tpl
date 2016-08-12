<?php if ($this->pageEnabled) : ?>
    <?php $this->headScript()->prependFile('application/modules/Hecontest/externals/scripts/admin/core.js'); ?>
    <script type="text/javascript">
        window.addEvent('domready', function (e) {
            initCompleter("<?php echo $this->url(array('module'=>'hecontest', 'controller'=>'index', 'action'=>'page-autocompleter'), 'admin_default'); ?>");
        });
    </script>
<?php endif; ?>

<div id="hecontest-pages-popup" style="display: none;">
    <ul>
    </ul>
</div>
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class="clear">
    <div class="settings">
        <?php echo $this->form->render($this) ?>
    </div>
</div>