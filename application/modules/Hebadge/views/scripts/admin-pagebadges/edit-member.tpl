<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit-member.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<script type="text/javascript">

  function hebadgeUseBadge(element, badge_id)
  {
    element = $(element);
    if (element.hasClass('hebadge_active')){
      Hebadge.request(en4.core.baseUrl + 'admin/hebadge/pagebadges/edit-member/badge/' + badge_id + '/enabled/0/format/json/id/<?php echo $this->subject->getIdentity()?>');
      element.removeClass('hebadge_active');
    } else {
      Hebadge.request(en4.core.baseUrl + 'admin/hebadge/pagebadges/edit-member/badge/' + badge_id + '/enabled/1/format/json/id/<?php echo $this->subject->getIdentity()?>');
      element.addClass('hebadge_active');
    }

  }

</script>


<div class="hebadge_member_edit">

  <h3><?php echo $this->translate('HEBADGE_PAGE_EDIT', array($this->subject->getTitle()))?></h3>
  <p><?php echo $this->translate('HEBADGE_WIDGET_DESCRIPTION_' . strtoupper($this->simple_name) );?></p>

  <br />

  <?php if ($this->paginator->getTotalItemCount()):?>
    <ul class="hebadge_widget_browse">
      <?php foreach ($this->paginator as $badge):?>
        <li onclick="hebadgeUseBadge(this, <?php echo $badge->getIdentity()?>);" style="cursor:pointer;" class="<?php if (!empty($this->members[$badge->getIdentity()]) && $this->members[$badge->getIdentity()]->approved):?>hebadge_active<?php endif;?>">
          <div class="item_photo">
            <a href="javascript:void(0);">
              <?php echo $this->itemPhoto($badge, 'thumb.profile');?>
            </a>
          </div>
          <div class="item_body">
            <div class="item_title"><a href="javascript:void(0);"><?php echo $badge->getTitle();?></a></div>
            <div class="item_description">
              <a href="javascript:void(0);">
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



  <?php if ($this->paginator->count() > 1): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->params
    ))?>
    <br />
  <?php endif?>

</div>