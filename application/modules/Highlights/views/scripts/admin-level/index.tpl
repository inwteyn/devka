<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class="highlight_settings_description" style="margin: 10px 5px;font-size: 12px;">
  <p><?php echo $this->translate('HIGHLIGHT_admin_level')?></p>
</div>
<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/highlights/level/?id='+level_id;
  }
</script>
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>