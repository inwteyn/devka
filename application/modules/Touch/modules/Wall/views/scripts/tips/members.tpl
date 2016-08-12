<?php
  if ($this->members instanceof Zend_Paginator){
    $this->members->setItemCountPerPage(5);
  }
?>

<ul class="items users">
  <?php foreach ($this->members as $member):?>
    <li>
      <a href="<?php echo $member->getHref()?>" title="<?php echo $member->getTitle()?>">
        <?php echo $this->itemPhoto($member, 'thumb.icon')?>
      </a>
    </li>
  <?php endforeach;?>
</ul>