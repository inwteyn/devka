<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 13.09.12
 * Time: 16:27
 * To change this template use File | Settings | File Templates.
 */
?>

<?php if($this->donations->getTotalItemCount() > 0): ?>
  <div id="page_donation_container" class="he-items">
    <ul class="he-item-list">
      <?php foreach($this->donations as $item): ?>
      <li>
        <div class="he-item-photo">
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
        </div>
        <div class="he-item-options">
          <?php if($item->status == 'expired'): ?>
            <div>
              <img align="left" style="margin-right: 5px;" src="<?php echo $this->baseUrl() . '/application/modules/Donation/externals/images/tick32.png'; ?>">
              <h3><?php echo $this->translate("Completed"); ?></h3>
            </div>
          <?php else: ?>
            <?php
              echo $this->htmlLink($this->url(array(
                'controller' => $item->type,
                'action' => 'edit',
                'donation_id' => $item->getIdentity(),
              ),
              'donation_extended', true), $this->translate('DONATION_Edit Donation'), array('class' => 'buttonlink donation_manage'));
            ?>
            <br>
            <?php
              echo $this->htmlLink($this->url(array(
                'controller' => $item->type,
                'action' => 'delete',
                'donation_id' => $item->getIdentity(),
                'format' => 'smoothbox'
              ),
              'donation_extended', true), $this->translate('DONATION_Delete Donation'), array('class' => 'buttonlink smoothbox donation_delete'));
            ?>
            <?php if($item->type != 'fundraise'): ?>
              <br>
              <?php
                echo $this->htmlLink($this->url(array(
                  'controller' => $item->type,
                  'action' => 'fininfo',
                  'donation_id' => $item->getIdentity(),
                  ),
                  'donation_extended', true), $this->translate('DONATION_Edit Financial Information'), array('class' => 'buttonlink donation_edit_money'));
              ?>
            <?php endif; ?>
            <?php if($item->approved): ?>
              <br>
              <?php
                echo $this->htmlLink($this->url(array(
                  'controller' => 'statistics',
                  'action' => 'index',
                  'donation_id' => $item->getIdentity(),
                ),
                'donation_extended', true), $this->translate('DONATION_Profile_statistic'), array('class' => 'buttonlink donation_statistics'));
              ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <div class="he-item-info">
          <div class="he-item-title">
            <h3><?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 60))?></h3>
          </div>
          <div class="he-item-details">
            <span class="float_left"><?php echo $this->translate('DONATION_Raised:'); ?>&nbsp;</span>
            <span class="float_left"><?php echo $this->locale()->toCurrency((double)$item->raised_sum, $this->currency) ?></span><br>
            <span class="float_left"><?php echo $this->translate('DONATION_Posted:'); ?>&nbsp;</span>
            <span class="float_left"><?php echo $this->timestamp($item->creation_date); ?></span><br>
          </div>
          <div class="he-item-desc">
            <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription()),250,1024,511,false) ?>
          </div>
        </div>
      </li>
     <?php endforeach; ?>
    </ul>
    <?php if( $this->donations->count() > 1 ): ?>
    <br />
    <?php echo $this->paginationControl(
      $this->donations, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->searchParams
    )); ?>
    <?php endif; ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('DONATION_You do not have any donations yet.');?>
      <?php if($this->canCreateCharity && $this->canCreateProject): ?>
      <?php
      echo $this->translate('DONATION_Get started by creating %1$scharity%2$s or %3$sproject%4$s donation.',
        '<a href="'.$this->url(array('controller' => 'charity','action' => 'create'), 'donation_extended', true).'">','</a>',
        '<a href="'.$this->url(array('controller' => 'project','action' => 'create'), 'donation_extended', true).'">','</a>');
      ?>
      <?php elseif($this->canCreateCharity && !$this->canCreateProject): ?>
      <?php
      echo $this->translate('DONATION_Get started by creating %1$scharity%2$s donation.',
        '<a href="'.$this->url(array('controller' => 'charity','action' => 'create'), 'donation_extended', true).'">','</a>');
      ?>
      <?php elseif($this->canCreateProject && !$this->canCreateCharity): ?>
      <?php
      echo $this->translate('DONATION_Get started by creating %1$sproject%2$s donation.',
        '<a href="'.$this->url(array('controller' => 'project','action' => 'create'), 'donation_extended', true).'">','</a>');
      ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
