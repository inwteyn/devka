<table cellspacing="3px" style="font-size:11px;color:<?php echo $this->fontColor; ?>;" <?php if(isset($this->params['preview'])):?>class="<?php echo $this->content['name']; ?> fontcolors" id="<?php echo $this->content['name'] . '_' . $this->content['id']; ?>"<?php endif;?>>
	<?php foreach ($this->items as $item) :?>
	<tr  class="item_<?php echo $item->getIdentity(); ?> item">

		<td width="50px" valign="top">
			<?php echo $this->itemsHTML[$item->getIdentity()][0]; ?>
      <?php echo $this->itemsHTML[$item->getIdentity()][1]; ?>
		</td>

		<td>
			<div class="hebadge_widget_last_members_arrow">
        <?php echo $this->timestamp($item->creation_date); ?>
      </div>
		</td>

    <td>
      <?php $file = Engine_Api::_()->getItemTable('storage_file')->getFile($item->badge_photo_id, 'thumb.profile'); ?>
      <?php $view = Zend_Registry::get('Zend_View'); ?>
      <img src="http://<?php echo $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/'. $file->storage_path; ?>" width="48" height="48" style="margin-top: -24px">
      <div>
        <a href="/badge/<?php echo $item->badge_id; ?>" style="font-weight: bold; color:#1a80d9; font-size: 12px;text-decoration: none">
          <?php echo $item->title; ?>
        </a>
      </div>
    </td>
	</tr>
	<?php	endforeach; ?>

</table>
<br/>
<div style="float:right;font-weight:bold;font-size:11px">
	<?php	if (isset($item)):	echo $this->itemsHTML[$item->getIdentity()][2];	endif;?>
</div>