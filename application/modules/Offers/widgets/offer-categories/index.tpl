<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-08-12 11:42:11 ratbek $
 * @author     Ratbek
 */
?>

<ul class="offer-categories">
  <?php if ( count( $this->categories ) > 1): ?>
    <li>
      <a class="offer_sort_buttons" id="offer_all_categories"
        title="<?php echo $this->translate("OFFERS_All Categories")?>"
        href="<?php echo $this->url(array('sort_type'=>'category_name', 'sort_value'=>0), 'offers_general')?>"
        onclick="offers_manager.setCategory(0, '<?php echo $this->filter ?>', '<?php echo $this->my_offers_filter; ?>');return false;">
        <?php echo $this->string()->truncate($this->translate("OFFERS_All Categories"), 15, '...'); ?>
      </a>
    </li>
  <?php endif; ?>
  <?php $cnt = 0; ?>
  <?php foreach ( $this->categories as $category):?>
    <li>
      <?php if($category['count']<=0) continue; ?>
        <a class="category_<?php echo $category['category_id']?> offer_sort_buttons"
          id="offer_sort_category_<?php echo $category['category_id']; ?>"
          title="<?php echo $this->translate($category['title'])?>"
          href="<?php echo $this->url(array('sort_type'=>'category', 'sort_value'=>$category['title']), 'offers_general')?>"
          onclick="offers_manager.setCategory(<?php echo $category['category_id']?>, '<?php echo $this->filter ?>', '<?php echo $this->my_offers_filter; ?>'); return false;">
             <?php echo $this->string()->truncate($this->translate($category['title']), 15, '...'); ?>
        </a>(<?php echo $category['count']; ?>)
    </li>
    <?php $cnt++; ?>
  <?php endforeach; ?>
</ul>