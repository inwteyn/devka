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
  <p><?php echo $this->translate('HIGHLIGHT_Configure Profile highlight settings cost and number of days')?></p>
</div>
<div class="settings">
  <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit')): ?>
    <?php echo $this->form->render($this)?>
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
      <form>
        <div>
          <h3>Transactions</h3>
          <table class="admin_table">
            <thead>
            <tr>
              <th class="admin_table_short">ID</th>
              <th class="admin_table_short">Username</th>
              <th class="admin_table_short">Credits</th>
              <th class="admin_table_short">Date</th>
            </tr>
            </thead>
            <?php foreach ($this->paginator as $item): ?>
              <tr>
                <td>
                  <?php echo $item->getOwner()->getIdentity(); ?>
                </td>
                <td>
                  <?php echo $item->getOwner()->getTitle(); ?>
                </td>
                <td>
                  <?php echo $item->credit; ?>
                </td>
                <td>
                    <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
          <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
        </div>
      </form>
    <?php else: ?>
      <div class="tip">
    <span>
      <?php echo $this->translate('HIGHLIGHT_Nobody has bought highlight!'); ?>
    </span>
      </div>
    <?php endif; ?>
  <?php else:?>
    <p>
      Please install or enable Credits plugin. You can buy this plugin
      <a href="http://www.hire-experts.com/social-engine/credits-plugin">here.</a>
    </p>
  <?php endif;?>
</div>