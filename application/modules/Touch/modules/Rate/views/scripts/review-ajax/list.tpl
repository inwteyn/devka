<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-08-31 17:53 michael $
 * @author     Michael
 */

if ($this->paginator->count() > 0):

  $counter = 0;
  foreach ($this->paginator as $row):

  ?>

  <div class="pagereview<?php if ($counter !=0):?> border<?php endif; if ($row['is_owner']):?> owner<?php endif;?>">
    <div class="header">
      <?php echo $this->htmlLink('javascript:Review.view('.$row['pagereview_id'].');', $row['title'])?>
      <div style="float: right;">
        <?php if ($row['is_owner']): echo $this->htmlLink('javascript:Review.edit('.$row['pagereview_id'].');', $this->htmlImage($this->baseUrl().'/application/modules/Rate/externals/images/edit16.png', '', array('border' => 0)), array('title' => $this->translate('edit')) ); endif; ?>
        <?php if ($row['is_owner'] || $this->isAllowedRemove): echo $this->htmlLink('javascript:Review.remove('.$row['pagereview_id'].');', $this->htmlImage($this->baseUrl().'/application/modules/Touch/externals/images/delete.png', '', array('border' => 0)), array('title' => $this->translate('delete'))); endif; ?>
      </div>
    </div>
    <div class="posted">
      <?php if ($this->countOptions): ?>
      <div class="he_rate_small_cont">
        <?php echo $this->reviewRate($row['rating'], true)?> <div class="pagereview_count">(<?php echo round($row['rating'],2)?>)</div>
        <div class="clr"></div>
      </div>
      <?php endif;?>
      <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($row->creation_date)?>
    </div>
    <div class="body"><?php echo Engine_String::substr($row->body, 0, 350); if (Engine_String::strlen($row->body)>349): echo $this->translate("..."); endif;?></div>
  </div>

<?php

  $counter++;
  endforeach;

?>

<br />

<?php if ($this->paginator->count() > 1): ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","rate"))?>
<?php endif?>

<?php if ($this->isAllowedPost):?>
  <a href="javascript:Review.goCreate();" class="pagereview_create"><?php echo $this->translate('RATE_REVIEW_CREATE')?></a>
<?php endif; ?>

<?php else: ?>

<div class="tip">
  <span>
    <?php echo $this->translate('RATE_REVIEW_TIP');?>
    <?php if ($this->isAllowedPost):?>
      <?php echo $this->translate('RATE_REVIEW_TIP_CREATE',  '<a href="javascript:void(0);" onClick="Review.create();">', '</a>'); ?>
    <?php endif; ?>
  </span>
</div>

<?php endif; ?>

