<form method="post" class="global_form_popup">
  <div>
    <h4><?php echo $this->translate("HIGHLIGHT_Highlight my profile?") ?></h4>

    <p>
      <?php echo $this->translate("HIGHLIGHT_You can highlight your profile and display your photo and details to whole network.") ?>
    </p>
    <div class="highlight_cost_description">
      <img src="application/modules/Highlights/externals/images/current.png">
      <span><b><?php echo Engine_Api::_()->getDbTable('settings', 'core')->getSetting('highlight.cost', 10)?> <?php echo $this->translate('credits');?>
          </b> <?php echo $this->translate('HIGHLIGHT_for');?> <b><?php echo Engine_Api::_()->getDbTable('settings', 'core')->getSetting('highlight.num.days', 10)?> <?php echo $this->translate('days');?></b></span>
    </div>
    <br/>
    <p>
      <input type="hidden" value="1">
      <button type='submit'><?php echo $this->translate("Confirm & Proceed") ?></button>
      <?php echo $this->translate(" or ") ?>
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>