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


<script type="text/javascript">
  en4.core.runonce.add(function (){
    $$('.hebadge_widget_last_members_badge .item_photo a').each(function (item){
      Hebadge.elementClass(Hebadge.Tips, item, {'title': item.getParent('li').getElement('.item_info').get('html'), 'top': false, 'left': true});
    });
  });
</script>


<?php if ($this->paginator->getTotalItemCount()):?>
  <ul class="hebadge_widget_last_members_browse">
    <?php foreach ($this->paginator as $item):?>
      <?php
        if (empty($this->badges['hebadge_badge_' . $item->badge_id]) || empty($this->objects[$item->object_type . '_' . $item->object_id])){
          continue ;
        }
        $badge = $this->badges['hebadge_badge_' . $item->badge_id];
        $object = $this->objects[$item->object_type . '_' . $item->object_id];
      ?>
      <li>
        <ul class="hebadge_widget_last_members_browse_line">
          <li class="hebadge_widget_last_members_member">
            <div class="item_photo">
              <a href="<?php echo $object->getHref()?>"><?php echo $this->itemPhoto($object, 'thumb.icon');?></a>
            </div>
            <div class="item_body">
              <div class="item_title"><a href="<?php echo $object->getHref()?>"><?php echo $object->getTitle();?></a></div>
            </div>
          </li>
          <li class="hebadge_widget_last_members_arrow">
            <?php echo $this->timestamp($item->creation_date) ?>
          </li>
          <li class="hebadge_widget_last_members_badge">
            <div class="item_photo">
              <a href="<?php echo $badge->getHref()?>">
                <?php echo $this->itemPhoto($badge, 'thumb.profile');?>
              </a>
            </div>
            <div class="item_body">
              <div class="item_title"><a href="<?php echo $badge->getHref()?>"><?php echo $badge->getTitle();?></a></div>
              <div class="item_description">
                <a href="<?php echo $this->url(array('action' => 'view', 'id' => $badge->getIdentity(), 'tab' => 'members'), 'hebadge_profile', true)?>">
                  <?php echo $this->translate(array('%1$s member', '%1$s members', $badge->member_count), $badge->member_count);?>
                </a>
              </div>
              <div style="display: none;" class="item_info">
                <div class="item_title"><?php echo $badge->getTitle()?></div>
                <div class="item_description"><?php echo $badge->getDescription()?></div>
              </div>
            </div>
          </li>
        </ul>

      </li>
    <?php endforeach;?>
  </ul>

<?php endif;?>

