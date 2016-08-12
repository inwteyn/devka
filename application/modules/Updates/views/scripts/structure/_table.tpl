<table cellspacing="3px" style="font-size:11px;color:<?php echo $this->fontColor; ?>;" <?php if(isset($this->params['preview'])):?>class="<?php echo $this->content['name']; ?> fontcolors" id="<?php echo $this->content['name'] . '_' . $this->content['id']; ?>"<?php endif;?>>
	<?php foreach ($this->items as $item) :?>
	<tr  class="item_<?php echo $item->getIdentity(); ?> item">

		<td width="50px" valign="top">
			<?php echo $this->itemsHTML[$item->getIdentity()][0]; ?>
		</td>

		<td valign="top">
			<?php echo $this->itemsHTML[$item->getIdentity()][1]; ?>
		</td>

	</tr>
	<?php	endforeach; ?>

</table>
<br/>
<div style="float:right;font-weight:bold;font-size:11px">
	<?php	if (isset($item)):	echo $this->itemsHTML[$item->getIdentity()][2];	endif;?>
</div>