
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php echo $this->render('_jsTips.tpl'); ?>

<div class="highlight_settings_description" style="margin: 10px 5px;font-size: 12px;">
  <p>
    <?php echo $this->translate('HIGHLIGHT_Add fields that will be shown on highlight profile widgets') ?>
  </p>
</div>
<div class="tips_select">
  <?php echo @$this->formSelect('subjectOption', (($this->option_id > 1)? $this->option_id  : 1), array(), $this->topLevelOptions); ?>
  <?php echo $this->formSelect('subjectTips', array_keys($this->tipsMeta), array(), $this->tipsMeta); ?>
  <?php echo $this->formButton('addTips', 'Add'); ?>
  <ul class="tips_list">
    <?php if(count($this->tipsMaps) > 0): ?>
      <?php foreach( $this->tipsMaps as $tip ): ?>
        <?php echo $this->adminTipsMeta($tip); ?>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="tip">
    <span>
        <?php echo $this->translate("HIGHLIGHT_In this category are no tips!"); ?>
      </span>
      </div>
    <?php endif; ?>
  </ul>
</div>