

<?php foreach ($this->items as $item):?>

  <li class="item item_<?php echo $item->getGuid()?>" rev="item_<?php echo $item->getGuid()?>" onclick="window.parent.location='<?php echo $item->getHref();?>'">

    <div class="item_photo">
      <?php echo $this->itemPhoto($item, 'thumb.icon', array())?>
      <div class="inner"></div>
    </div>

    <div class="item_body">

      <div class="item_title">
        <?php echo Engine_String::substr($item->getTitle(), 0, 100)?>
      </div>

    </div>

  </li>

<?php endforeach ; ?>

