<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 19:14 michael $
 * @author     Michael
 */
?>

<div class="he_rate_cont">

  <?php if ($this->period) : ?>
  <ul class="rate_list_switcher">
    <li><a href="javascript://" onclick="showRatesList(this, 'all');" class="active"><?= $this->translate('RATE_Overall'); ?></a></li>
    <li><a href="javascript://" onclick="showRatesList(this, 'month');"><?= $this->translate('RATE_This Month'); ?></a></li>
    <li><a href="javascript://" onclick="showRatesList(this, 'week');"><?= $this->translate('RATE_This Week'); ?></a></li>
  </ul>
  <div class="clr"></div>
  <?php endif; ?>

  <div class="rate_list rates_all active_list">
    <?php $this->rate_items = $this->all_rates; ?>
    <?php echo $this->render('_items.tpl'); ?>
  </div>

  <?php if ($this->period) : ?>
    <div class="rate_list rates_month">
      <?php $this->rate_items = $this->month_rates; ?>
      <?php echo $this->render('_items.tpl'); ?>
    </div>
    <div class="rate_list rates_week">
      <?php $this->rate_items = $this->week_rates; ?>
      <?php echo $this->render('_items.tpl'); ?>
    </div>
  <?php endif; ?>

</div>