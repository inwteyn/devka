<table style="margin-top:5px;" <?php if(isset($this->params['preview'])):?>class="<?php echo $this->content['name']; ?>" id="<?php echo $this->content['name'] . '_' . $this->content['id']; ?>"<?php endif;?>>

	<?php foreach ($this->items as $item) :?>
	<tr  class="suggest_item_<?php echo $item->getType() . '_' . $item->getIdentity(); ?> item">

		<td width="50px" valign="top"  style="padding-bottom:4px;">
			<?php echo $this->itemsHTML[$item->getType() . '_' . $item->getIdentity()][0]; ?>
		</td>

		<td valign="top" style="font-size:11px;color:<?php echo $this->fontColor;?>" <?php if(isset($this->params['preview'])):?>class="fontcolors" <?php endif; ?>>
			<?php echo $this->itemsHTML[$item->getType() . '_' . $item->getIdentity()][1]; ?>
		</td>

	</tr>
	<?php	endforeach; ?>

</table>

<div style="float:right;font-weight: bold; clear: both;padding:0px;margin:0px;padding-top: 5px;font-size:12px">
	<?php	if (isset($item)):	echo $this->itemsHTML[$item->getType() . '_' . $item->getIdentity()][2];	endif;?>
</div>