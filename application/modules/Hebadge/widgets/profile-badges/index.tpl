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
    console.log()
    $$('.hebadge_widget_browse .item_photo a').each(function (item){
      Hebadge.elementClass(Hebadge.Tips, item, {'title': item.getParent('li').getElement('.item_info').get('html'), 'top': false, 'left': true});
    });
  });
</script>



<div class="hebadge_widget_description">
  <?php echo $this->translate('HEBADGE_WIDGET_DESCRIPTION_PROFILE_BADGES');?>
</div>

<?php if ($this->paginator->getTotalItemCount()):?>
  <ul class="hebadge_widget_browse">
    <?php foreach ($this->paginator as $badge):?>
      <li>
        <div class="item_photo">
          <a href="<?php echo $badge->getHref()?>">
            <?php echo $this->itemPhoto($badge, 'thumb.profile');?>
           </a>
        </div>
        <div class="item_body">
          <div class="item_title"><a href="<?php echo $badge->getHref()?>"><?php echo $badge->getTitle();?></a></div>
          <div class="item_description">
            <a href="<?php $this->url(array('action' => 'view', 'badge_id' => $badge->getIdentity(), 'tab' => 'members'), 'hebadge_general', true)?>">
              <?php echo $this->translate(array('%1$s member', '%1$s members', $badge->member_count), $badge->member_count);?>
            </a>
          </div>
        </div>

        <div style="display: none" class="item_info">
          <div class="item_title"><?php echo $badge->getTitle();?></div>
          <div class="item_description"><?php echo $badge->getDescription();?></div>
        </div>

      </li>
    <?php endforeach;?>
  </ul>

<?php else:?>

  <div class="tip"><span><?php echo $this->translate('HEBADGE_WIDGET_NOITEMS_PROFILE_BADGES');?></span></div>

<?php endif;?>

<br />

<?php if ($this->paginator->count() > 1): ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","hebadge"), array(
    'ajax_url' => $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'hebadge.profile-badges', 'subject' => $this->subject()->getGuid()), 'default', true),
    'ajax_class' => 'layout_hebadge_profile_badges'
  ))?>
  <br />
<?php endif?>