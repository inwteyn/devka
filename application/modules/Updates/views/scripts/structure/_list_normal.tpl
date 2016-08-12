<?php if(!isset($this->content['id'])):  ?>

  <div  style="font-size:11px;color:<?php echo $this->fontColor; ?>;" <?php if(isset($this->params['preview'])):?>class="<?php echo $this->content['name']; ?> fontcolors" id="<?php echo $this->content['name'] . '_' . $this->content['id']; ?>"<?php endif; ?>>
    <?php foreach( $this->items as $item ):?>
    <div style="float:left;margin:5px; text-align:center;"  <?php if(isset($this->params['preview'])):?>class="item_<?php echo $item->getIdentity(); ?> item" <?php endif; ?>>

      <?php echo $this->itemsHTML[$item->getIdentity()][0]; ?>
      <?php echo $this->itemsHTML[$item->getIdentity()][1]; ?>

    </div>
    <?php	endforeach; ?>
  </div>
  <br/>
  <div style="float:right;font-weight:bold;font-size:11px">
    <?php if (isset($item)):  echo $this->itemsHTML[$item->getIdentity()][2]; endif;?>
  </div>

<?php else: ?>

  <div  style="font-size:11px;color:<?php echo $this->fontColor; ?>;" <?php if(isset($this->params['preview'])):?>class="<?php echo $this->content['name']; ?> fontcolors" id="<?php echo $this->content['name'] . '_' . $this->content['id']; ?>"<?php endif; ?>>
    <?php $i = 0; $flag1 = false; $flag2 = false; ?>
    <table cellpadding="0" cellspacing="0" border="0">
      <?php foreach( $this->items as $item ):?>
        <?php if($this->contentParentType($this->content) == 'right' || $this->contentParentType($this->content) == 'left'): ?>
          <tr>
            <td>
              <div style="float:left;margin:5px; text-align:center;"  <?php if(isset($this->params['preview'])):?>class="item_<?php echo $item->getIdentity(); ?> item" <?php endif; ?>>
                <?php echo $this->itemsHTML[$item->getIdentity()][0]; ?>
                <?php echo $this->itemsHTML[$item->getIdentity()][1]; ?>
              </div>
            </td>
          </tr>
        <?php endif; ?>

        <?php if($this->contentParentType($this->content) == 'middle'): ?>
          <?php $flag1 = true; $flag2 = false; ?>
          <?php $i++; ?>
          <?php if($i == 1 || $i == 4 || $i == 7 || $i == 10 || $i == 13): ?>
            <tr>
          <?php endif; ?>
            <td>
              <div style="float:left;margin:5px; text-align:center;"  <?php if(isset($this->params['preview'])):?>class="item_<?php echo $item->getIdentity(); ?> item" <?php endif; ?>>
                <?php echo $this->itemsHTML[$item->getIdentity()][0]; ?>
                <?php echo $this->itemsHTML[$item->getIdentity()][1]; ?>
              </div>
            </td>
          <?php if($i == 3 || $i == 6 || $i == 9 || $i == 12 || $i == 15): ?>
            </tr>
            <?php $flag2 = true; ?>
          <?php endif; ?>
        <?php endif; ?>
      <?php	endforeach; ?>
      <?php if ($flag1 && !$flag2): ?>
        </tr>
      <?php endif; ?>
    </table>
  </div>
  <br/>
  <div style="float:right;font-weight:bold;font-size:11px">
    <?php if (isset($item)):  echo $this->itemsHTML[$item->getIdentity()][2]; endif;?>
  </div>
<?php endif; ?>