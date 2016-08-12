<?php if( count($this->navigation) > 0 ){ ?>
<?php echo $this->paginationControl($this->paginator, null, array('pagination/page_filter.tpl', 'touch'),
  array(
  'search'=>$this->form_filter->getElement('search')->getValue(),
  'filter_default_value'=>$this->translate('TOUCH_Search Events'),
  'filterUrl'=>$this->url(array('module'=>'pageevent', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_event', true),
  )
); ?>
<?php if( $this->paginator->getTotalItemCount() > 0 ){ ?>
<div id="filter_block">
    <ul class="items">
    <?php foreach ($this->paginator as $event):?>
      <li>
        <div class="item_photo">
            <a href="<?php echo $this->url(array('action' => 'view', 'event_id' => $event->getIdentity()), 'page_event', true)?>"
               onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                <?php echo $this->itemPhoto($event, 'thumb.icon'); ?>
            </a>
        </div>

        <div class="item_body">
            <div class="item_title">
                <a href="<?php echo $this->url(array('action' => 'view', 'event_id' => $event->getIdentity()), 'page_event', true)?>"
                    onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                    <?php echo $event->getTitle()?>
                </a>
            </div>

            <div class="item_date">
                <?php echo $this->locale()->toDateTime($event->starttime)?>
                <?php echo $this->translate(array('%s guest', '%s guests',
                $event->membership()->getMemberCount()),
                $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
                <?php echo $this->translate('led by') ?>
                <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
            </div>
            <?php echo $this->touchSubstr($event->getDescription()) ?>
          <div class="item_options">
              <a href="<?php echo $this->url(array('action' => 'delete', 'event_id' => $event->getIdentity()), 'page_event', true)?>"
                 class="smoothbox">
                  <?php echo $this->translate('Delete')?>
              </a>
          </div>

        </div>
      </li>
    <?php endforeach;?>
</ul>
  </div>
  <?php } else {?>
  <div class="tip">
    <span>
      <?php echo $this->translate('PAGEEVENT_NOITEMS');?>
      <?php if ($this->isAllowedPost):?>
        <?php echo $this->translate('PAGEEVENT_NOITEMS_CREATE'); ?>
      <?php endif; ?>
    </span>
  </div>
  <?php } ?>
<?php } ?>