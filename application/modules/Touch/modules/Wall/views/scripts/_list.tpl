
<li>
  <a href="javascript:void(0);" rev="recent" class="item <?php if ($this->list_params['mode'] == 'recent'):?>is_active<?php endif;?> wall_blurlink">
    <span class="icon_active"></span>
    <span class="icon wall-most-recent"></span>
    <?php echo $this->translate('WALL_RECENT')?>
  </a>
</li>

<li class="separator"></li>

<?php if (count($this->types)):?>

  <?php foreach ($this->types as $type):?>
    <li>
      <a href="javascript:void(0);" rev="type-<?php echo $type?>" class="item <?php if ($this->list_params['mode'] == 'type' && $type == $this->list_params['type']):?>is_active<?php endif;?> wall_blurlink">
        <span class="icon_active"></span>
        <span class="icon wall-type-<?php echo $type?>"></span>
        <?php echo $this->translate('WALL_TYPE_' . strtoupper($type) )?>
      </a>
    </li>
  <?php endforeach ;?>

  <li class="separator"></li>

<?php endif;?>

<?php if (count($this->lists)):?>

  <?php foreach ($this->lists as $list):?>
    <li>
      <div class="options">
        <a href="javascript:void(0);" class="edit" title="<?php echo $this->translate('Edit 2')?>" rev="list_<?php echo $list->list_id?>"></a>
        <div style="display:inline; width:10px;"></div>
        <a href="javascript:void(0);" class="remove" title="<?php echo $this->translate('Delete 2')?>" rev="list_<?php echo $list->list_id?>"></a>
      </div>
      <a href="javascript:void(0);" rev="list-<?php echo $list->list_id?>" class="item <?php if ($this->list_params['mode'] == 'list' && $list->list_id == $this->list_params['list_id']):?>is_active<?php endif;?> wall_blurlink">
        <span class="icon_active"></span>
        <span class="icon wall-type-list"></span>
        <?php echo $list->label?>
      </a>
    </li>
  <?php endforeach ;?>

  <li class="separator"></li>

<?php endif;?>

<li>
  <a href="javascript:void(0);" rev="create-new" class="item wall_blurlink">
    <span class="icon_active"></span>
    <span class="icon wall-list-new"></span>
    <?php echo $this->translate('WALL_TYPE_CREATE_NEW')?>
  </a>
</li>

