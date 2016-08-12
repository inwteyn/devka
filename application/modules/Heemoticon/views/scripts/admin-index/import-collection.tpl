<style>
 .global_form ul.form-errors > li{
   background-color: #e47c7c !important;
   border: 1px solid #cd6262 !important;
   color: #fff !important;
  }
</style>
<h2>
    <?php echo $this->translate('HE-Emoticon Emoticon Plugin') ?>
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


<div class='clear'>
    <div class='settings'>
            <?php echo $this->form->render($this) ?>
    </div>
</div>
