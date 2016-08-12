<div style="float: right;">
    <?php echo $this->htmlLink($this->url(array(), 'register_url', true), $this->translate('Main Page'),
    array('style' => 'font-size: 15px; color: red; font-weight: bold')
    )?>
</div>

<?php echo $this->form->render($this); ?>