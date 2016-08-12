<script type="text/javascript">
    //<![CDATA[
    window.addEvent('domready', function(){
        $('level_id').addEvent('change', function(){
            window.location.href = en4.core.baseUrl + 'admin/hecontest/index/level-settings?level_id='+this.get('value');
        });
    });
    //]]>
</script>
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