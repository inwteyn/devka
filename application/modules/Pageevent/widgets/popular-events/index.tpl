<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

?>

<ul class="generic_list_widget">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
    if( $item['type'] == 'page' )
      $event = Engine_Api::_()->getItem('pageevent', $item['event_id']);
    else
      $event = Engine_Api::_()->getItem('event', $item['event_id']);
  ?>
  <li>

    <div class="photo">
      <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.icon'), array('class' => 'thumb')) ?>
    </div>

    <div class="info">
      <div class="title">
        <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
      </div>
      <div class="stats">
        <?php echo $this->timestamp(strtotime($event->creation_date)) ?>
        <br/>
        <?php $owner = $event->getOwner()?>
        <?php if( $item['type'] == 'page' && (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $event->page_id)->getOwner() != $event->getOwner())) : ?>
          <?php echo $this->translate('hosted by %1$s',
            $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle())) ?>
        <?php elseif ($item['type'] == 'page'):?>
          <?php echo $this->translate('hosted by %1$s',
            $this->htmlLink(Engine_Api::_()->getItem('page', $event->page_id)->getHref(), Engine_Api::_()->getItem('page', $event->page_id)->getTitle())) ?>
        <?php else:?>
          <?php echo $this->translate('hosted by %1$s',
            $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle())) ?>
        <?php endif;?>
        <?php if( $this->popularType == 'view' ): ?>
        <br/><?php echo $this->translate(array('%s view', '%s views', $event->view_count), $this->locale()->toNumber($event->view_count)) ?>
        <?php else /*if( $this->popularType == 'member' )*/: ?>
        <br/> <?php echo $this->translate(array('%s member', '%s members', $event->member_count), $this->locale()->toNumber($event->member_count)) ?>
        <?php endif; ?>
      </div>
    </div>
    <?php
    $desc = trim($this->string()->truncate($this->string()->stripTags($event->description), 200));
    if( !empty($desc) ): ?>
      <div class="description">
        <?php echo $desc ?>
      </div>
      <?php endif; ?>
  </li>
  <?php endforeach; ?>
</ul>