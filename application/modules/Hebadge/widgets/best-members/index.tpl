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


<?php if ($this->paginator->getTotalItemCount()):?>
  <ul class="hebadge_widget_best_members_browse">
    <?php foreach ($this->items as $item):?>
      <li>
        <div class="item_photo">
          <a href="<?php echo $item->getHref()?>">
            <?php echo $this->itemPhoto($item, 'thumb.icon');?>
           </a>
        </div>
        <div class="item_body">
          <div class="item_title"><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle();?></a></div>
          <div class="item_description">
            <?php if (!empty($this->badge_count[$item->getGuid()])):?>
              <span class="hebadge_member_badge_count">
                <?php echo $this->translate(array('%1$s badge', '%1$s badges', $this->badge_count[$item->getGuid()]), $this->badge_count[$item->getGuid()]);?>
              </span>
            <?php endif;?>
          </div>
        </div>

      </li>
    <?php endforeach;?>
  </ul>

<?php endif;?>

