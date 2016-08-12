<div id="widget_content">
  <?php if (count($this->paginator)):?>
  <div class="page_sub_navigation">
    <ul class="touch_sub_navigation">
      <li>
        <a href="<?php echo $this->url(array('action' => 'index', 'page_id' => $this->subject()->page_id), 'page_event', true)?>"
          class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("PAGEEVENT_UPCOMING"); ?>
        </a>
      </li>

      <li>
        <a href="<?php echo $this->url(array('action' => 'past', 'page_id' => $this->subject()->page_id), 'page_event', true)?>"
          class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("PAGEEVENT_PAST"); ?>
        </a>
      </li>

      <li>
        <a href="<?php echo $this->url(array('action' => 'manage', 'page_id' => $this->subject()->page_id), 'page_event', true)?>"
          class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("PAGEEVENT_USER"); ?>
        </a>
      </li>
      
    <?php if ($this->can_create){?>
      <li>
        <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_event', true)?>"
          class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("PAGEEVENT_CREATE"); ?>
        </a>
      </li>
    <?php } ?>
    </ul>
  </div>
  <div style="height: 8px; clear: both;"></div>
  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >
    <?php echo $this->paginationControl($this->paginator, null, array('pagination/page_filter.tpl', 'touch'),
      array(
        'search'=>$this->form_filter->getElement('search')->getValue(),
        'filter_default_value'=>$this->translate('TOUCH_Search Events'),
        'filterUrl'=>$this->url(array('module'=>'pageevent', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_event', true),
      )
    ); ?>
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
              <a href="<?php echo $this->url(array('action' => 'view', 'event_id' => $event->getIdentity()), 'page_event', true)?>" onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                <?php echo $event->getTitle()?>
              </a>
            </div>
            <div class="item_date">
              <?php echo $this->locale()->toDateTime($event->starttime)?>
                <?php echo $this->translate(array('%s guest', '%s guests',
                  $event->membership()->getMemberCount()),
                  $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
                <?php echo $this->translate('led by') ?>
                <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle(), array('class' =>
                          'touchajax')) ?>
            </div>
            <?php echo $this->touchSubstr($event->getDescription()) ?>
          </div>
        </li>
        <?php endforeach;?>
      </ul>
    </div>
  </div>


<?php else:?>
  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >
    <div id="filter_block">
      <div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?>
          <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_event', true)?>"
            onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
              <?php echo $this->translate("PAGEEVENT_NOITEMS"); ?>
          </a>
        </span>
      </div>
    </div>
  </div>
  <?php endif?>
</div>