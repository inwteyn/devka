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
?>

<?php if ($this->paginator->count() > 0): ?>
  <?php foreach ($this->paginator as $counter => $tmp): $row = $tmp->toArray(); ?>
    <?php
      if (Engine_Api::_()->core()->hasSubject('store_product')) {
        $subject = Engine_Api::_()->core()->getSubject('store_product');
        $href = $subject->getHref() . '/content/review/content_id/';
      } else {
        $href = $this->subject->getHref() . '/content/review/content_id/';
      }
    ?>
    <div class="productreview<?php if ($counter !=0 ):?> border<?php endif; if ($row['is_owner']):?> owner<?php endif;?>">
      <div class="header">
        <a onclick='ProductReview.view(<?php echo $row['productreview_id']?>); return false;'
           href="<?php echo $href . $row['productreview_id']; ?>">
            <?php echo $row['title'] ?>
        </a>
        <div style="float: right;">
          <?php if ($row['is_owner']): echo $this->htmlLink('javascript:ProductReview.edit('.$row['productreview_id'].');', $this->htmlImage($this->baseUrl().'/application/modules/Rate/externals/images/edit16.png', '', array('border' => 0)), array('title' => $this->translate('edit')) ); endif; ?>
          <?php if ($row['is_owner'] || $this->isAllowedRemove): echo $this->htmlLink('javascript:ProductReview.remove('.$row['productreview_id'].');', $this->htmlImage($this->baseUrl().'/application/modules/Rate/externals/images/delete16.png', '', array('border' => 0)), array('title' => $this->translate('delete'))); endif; ?>
        </div>
      </div>
      <div class="posted">
        <?php if ($this->countOptions): ?>
          <div class="he_rate_small_cont">
            <?php echo $this->reviewRate($row['rating'], true)?> <div class="productreview_count">(<?php echo round($row['rating'],2);?>)</div>
            <div class="clr"></div>
          </div>
        <?php endif;?>
        <div class="productreview-description">
          <?php echo $this->heViewMore($row['body'], 100); ?>
        </div>
        <div class="productreview-posted-line">
          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($row->creation_date)?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <br />

  <?php if ($this->paginator->count() > 1): ?>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","rate"), array(
      'page' => $this->pageObject
    ))?>
  <?php endif?>

  <?php if ($this->isAllowedPost):?>
    <a href="javascript:ProductReview.goCreate();" class="productreview_create"><?php echo $this->translate('RATE_REVIEW_CREATE')?></a>
  <?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('RATE_REVIEW_TIP');?>
      <?php if ($this->isAllowedPost):?>
        <?php echo $this->translate('RATE_REVIEW_TIP_CREATE',  '<a href="javascript:void(0);" onClick="ProductReview.create();">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

