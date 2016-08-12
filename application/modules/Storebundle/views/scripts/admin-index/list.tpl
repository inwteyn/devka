<?php if ($this->items && $this->items->getTotalItemCount()): ?>
  <div>
    <?php if($this->items) :?>
      <table class="admin_table admin-storebundle-table">
        <?php foreach($this->items as $item): ?>
          <tr>
            <td class="admin-storebundle-title" width="90%">
              <a href="javascript://" onclick="StorebundleCore.showCreateForm('<?php echo $item->getIdentity(); ?>');">
                <?php echo $item->getTitle(); ?>
              </a>
            </td>
            <td class="admin-storebundle-options">
              <a href="javascript://" class="hei hei-check-circle-o <?php if($item->enabled) echo 'active-bundle-icon'?>"
                 onclick="StorebundleCore.enable('<?php echo $item->getIdentity(); ?>', this);"></a>
              <a href="javascript://" class="hei hei-trash-o"
                 onclick="StorebundleCore.remove('<?php echo $item->getIdentity(); ?>', this);"></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="tip">
      <span>
        <?php echo $this->translate('STOREBUNDLE_There is no bundles'); ?>
      </span>
  </div>
<?php endif; ?>