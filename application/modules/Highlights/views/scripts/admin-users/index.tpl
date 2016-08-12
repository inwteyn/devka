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
    <p><?php echo $this->translate('HIGHLIGHT_users_list')?></p>
  </div>
  <div class='admin_search'>
    <?php echo $this->filterForm->render($this) ?>
  </div>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <table class="admin_table">
    <thead>
    <tr>
      <th class="admin_table_short">ID</th>
      <th class="admin_table_short">Username</th>
      <th class="admin_table_short"><?php echo $this->translate('HIGHLIGHT_Date start')?></th>
      <th class="admin_table_short"><?php echo $this->translate('HIGHLIGHT_Date finish')?></th>
      <th class="admin_table_short"><?php echo $this->translate('HIGHLIGHT_Options')?></th>
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
          <?php if ($item->date_start != ''):?>
            <?php echo $this->timestamp(strtotime($item->date_start))?>
          <?php else:?>
            -
          <?php endif;?>
        </td>
        <td>
          <?php if ($item->date_finish != ''):?>
            <?php echo $this->timestamp(strtotime($item->date_finish)) ?>|
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'highlights', 'controller' => 'users', 'action' => 'edit', 'highlight_id' => $item->getIdentity()),
              $this->translate('edit'),
              array('class' => 'smoothbox')) ?>
          <?php else:?>
            -
          <?php endif;?>
        </td>
        <td>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'highlights', 'controller' => 'users', 'action' => 'view', 'user_id' => $item->user_id),
            $this->translate('HIGHLIGHT_view'),
            array('class' => 'smoothbox')) ?>
          <?php
          echo $this->htmlLink(
            array(
              'module' => 'highlights',
              'controller' => 'users',
              'action' => 'add',
              'page' => ((isset($this->page)?$this->page:1)),
              'value' => (strtotime($item->date_finish) < time())?1:0,
              'user_id' => $item->user_id,
            ),
            '<img title="Highlight" class="page-icon" src="application/modules/Highlights/externals/images/highlight'.((strtotime($item->date_finish) < time())?0:'').'.png">',
            array('class' => 'smoothbox')
          );?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->values,
  )); ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('HIGHLIGHT_No users found!'); ?>
    </span>
  </div>
<?php endif; ?>