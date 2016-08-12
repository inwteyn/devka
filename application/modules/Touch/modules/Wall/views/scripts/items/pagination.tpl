
<?php echo $this->partial('items/_items.tpl', null, array('items' => $this->items))?>

<?php if (!count($this->items)):?>
  <li class="message">
    <div>
      <?php if (empty($this->search)):?>
        <span><?php echo $this->translate('WALL_ITEMS_EMPTY')?></span>
      <?php else: ?>
        <span><?php echo $this->translate('WALL_ITEMS_EMPTY_SEARCH')?></span>
      <?php endif;?>
    </div>
  </li>
<?php endif?>