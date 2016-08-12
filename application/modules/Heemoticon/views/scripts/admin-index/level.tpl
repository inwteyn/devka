<h2>
    <?php echo $this->translate('HE - Emoticon Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>