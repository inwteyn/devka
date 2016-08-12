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
    $$('.hebadge_widget_pages_browse .item_photo a').each(function (item){
      Hebadge.elementClass(Hebadge.Tips, item, {'title': item.getParent('li').getElement('.item_info').get('html'), 'top': false, 'left': true});
    });
  });
</script>

<div class="hebadge_widget_description">
  <?php echo $this->translate('HEBADGE_WIDGET_DESCRIPTION_' . strtoupper($this->simple_name));?>
</div>

<?php if ($this->paginator->getTotalItemCount()):?>
  <ul class="hebadge_widget_pages_browse">
    <?php foreach ($this->paginator as $badge):?>
      <li>
        <div class="item_photo">
          <a href="<?php echo $badge->getHref()?>" onclick="if(window.page_manager){window.page_manager.setBadge(<?php echo $badge->getIdentity()?>);return false;}">
            <?php echo $this->itemPhoto($badge, 'thumb.profile');?>
          </a>
        </div>
        <div class="hebadge_item_count">
          <?php echo $badge->member_count;?>
        </div>
        <div class="item_body">
          <div class="item_title"><a href="<?php echo $badge->getHref()?>" onclick="if(window.page_manager){window.page_manager.setBadge(<?php echo $badge->getIdentity()?>);return false;}" class="hebadge_browse_pages_badge_<?php echo $badge->getIdentity()?>"><?php echo $badge->getTitle();?></a></div>
          <div class="item_description">
            <a href="<?php echo $badge->getHref()?>" onclick="if(window.page_manager){window.page_manager.setBadge(<?php echo $badge->getIdentity()?>);return false;}">
              <?php echo $this->translate(array('%1$s page', '%1$s pages', $badge->member_count), $badge->member_count);?>
            </a>
          </div>
          <div style="display: none;" class="item_info">
            <div class="item_title"><?php echo $badge->getTitle()?></div>
            <div class="item_description"><?php echo $badge->getDescription()?></div>
          </div>
        </div>
      </li>
    <?php endforeach;?>
  </ul>

<?php else:?>

  <?php if (!empty($this->params) && !empty($this->params['text'])):?>
    <div class="tip"><span><?php echo $this->translate('HEBADGE_WIDGET_NOITEMS_SEARCH_' . strtoupper($this->simple_name) );?></span></div>
  <?php else :?>
    <div class="tip"><span><?php echo $this->translate('HEBADGE_WIDGET_NOITEMS_' . strtoupper($this->simple_name) );?></span></div>
  <?php endif;?>

<?php endif;?>


<?php if ($this->paginator_type != 'hide'):?>

  <?php if ($this->paginator->count() > 1): ?>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","hebadge"), array(
      'ajax_url' => $this->url(array_merge(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity, 'container' => 0),$this->params), 'default', true),
      'ajax_class' => 'layout_' . $this->simple_name,
      'params' => $this->params,
      'mini' => ($this->paginator_type == 'mini')
    ))?>
    <br />
  <?php endif?>

<?php endif; ?>