
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>




<?php if (count($this->paginator)):?>
  <div class="pageevent_list">
    <?php foreach ($this->paginator as $event):?>
      <div class="item">
        <div class="photo"><?php echo $this->itemPhoto($event, 'thumb.icon')?></div>
        <div class="event_info">
         <div class="title"><a href="<?php echo $event->getHref(); ?>" onclick="Pageevent.view(<?php echo $event->getIdentity()?>); return false;"><?php echo $event->getTitle()?></a></div>
          <div class="details">
            <?php echo $this->locale()->toDateTime($event->starttime)?>
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $event->page_id)->getOwner() != $event->getOwner()):?>
              <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
            <?php else:?>
              <?php echo $this->htmlLink(Engine_Api::_()->getItem('page', $event->page_id)->getHref(),Engine_Api::_()->getItem('page', $event->page_id)->getTitle())?>
            <?php endif;?>
          </div>
          <div class="description"><?php echo $event->getDescription()?></div>
        </div>
          <div class="options">
            <?php if ($this->isTeamMember || $this->viewer->isSelf($event->getOwner())):?>
              <a href="javascript:Pageevent.formEvent(<?php echo $event->getIdentity()?>);"><?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Pageevent/externals/images/edit.png', $this->translate('edit'))?></a>
              <a href="javascript:Pageevent.remove(<?php echo $event->getIdentity()?>);"><?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Pageevent/externals/images/delete.png', $this->translate('delete'))?></a>
            <?php endif;?>
          </div>
        <div class="clr"></div>
      </div>
    <?php endforeach;?>
  </div>
  <br />

  <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","pageevent"), array(
    'page' => $this->pageObject
  ))?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('PAGEEVENT_NOITEMS');?>
      <?php if ($this->isAllowedPost):?>
        <?php echo $this->translate('PAGEEVENT_NOITEMS_CREATE'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif;?>