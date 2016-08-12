<div <?php if(isset($this->params['preview'])):?>class="<?php echo $this->content['name']; ?>" id="<?php echo $this->content['name'] . '_' . $this->content['id']; ?>" <?php endif; ?>>
<table cellspacing="0" cellpadding="0" border="0">
  <?php $i = 0; $flag = false; ?>
    <?php foreach( $this->items as $item ):?>
      <?php $i++; $flag = false; ?>
      <?php if($i == 1 || $i == 11 || $i == 21): ?>
        <tr>
      <?php endif; ?>
      <td width="60" valign="top">
        <div style="width:60px;height:100px;overflow:hidden;float:left;margin:3px;" <?php if(isset($this->params['preview'])):?>class="item_<?php echo $item->getIdentity(); ?> item"<?php endif; ?>>
          <?php echo $this->itemsHTML[$item->getIdentity()][0]; ?>
          <?php echo $this->itemsHTML[$item->getIdentity()][1]; ?>
        </div>
      </td>
      <?php if($i == 10 || $i == 20 || $i == 30): ?>
        </tr>
        <?php $flag = true; ?>
      <?php endif; ?>
	  <?php	endforeach; ?>
    <?php if (!$flag): ?>
      </tr>
    <?php endif; ?>
  </table>
</div>
<br/>
<div style="float:right;font-weight:bold;font-size:11px">
  <?php if (isset($item)): echo $this->itemsHTML[$item->getIdentity()][2]; endif;?>
</div>
