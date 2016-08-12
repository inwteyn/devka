<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>


<?php
  $current = ($this->owner_credit) ? $this->owner_credit->earned_credit : 0;
  $total = ($this->owner_next_rank) ? $this->owner_next_rank->credit : (($this->owner_credit) ? $this->owner_credit->earned_credit : 0);
?>

<div class="hebadge_credit_loader_description">
  <?php echo $this->translate('HEBADGE_WIDGET_DESCRIPTION_' . strtoupper($this->simple_name));?>
</div>

<div class="hebadge_credit_loader">

  <?php if ($this->owner_rank):?>
    <ul class="hebadge_credit_current">
      <li><?php echo $this->translate('HEBADGE_CURRENT_RANK')?></li>
      <li><a href="<?php echo $this->owner_rank->getHref();?>"><?php echo $this->owner_rank->getTitle();?></a></li>
    </ul>
  <?php endif;?>

  <div class="hebadge_credit_loader_container">
    <div>
      <div class="hebadge_credit_loader_total">
        <div class="hebadge_credit_loader_progress" style="width: <?php echo $this->complete?>%">
          <span><?php echo $current;?></span>
        </div>
      </div>
    </div>
    <div class="hebadge_credit_loader_total_count">
      <?php if ($total != $current): // it is max?>
        <?php echo $total;?>
      <?php endif;?>
    </div>
  </div>

  <?php if ($this->owner_next_rank):?>
    <ul class="hebadge_credit_next">
      <li><?php echo $this->translate('HEBADGE_NEXT_RANK')?></li>
      <li><a href="<?php echo $this->owner_next_rank->getHref();?>"><?php echo $this->owner_next_rank->getTitle();?></a></li>
    </ul>
  <?php endif;?>

</div>