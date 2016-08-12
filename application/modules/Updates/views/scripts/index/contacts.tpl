<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contacts.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>
<?php if ($this->error): ?>
  <div class="contacts_error"><?php echo $this->message; ?></div>
<?php else: ?>

<script type="text/javascript">
window.addEvent('domready',function()
{
  he_contacts.callback = "<?php echo $this->callback; ?>";
  he_contacts.list_type = 'all';
  he_contacts.contacts = <?php echo Zend_Json_Encoder::encode($this->checkedItems); ?>;
  he_contacts.init();

	$('tmp_items').adopt($$('.blacklist_item'));

	$('content_tab_active').addEvent('click', function()
  {
		$('tmp_items').adopt($$('.blacklist_item'));
		$('he_contacts_list').adopt($$('.content_item'));

		$(this).setStyle('display', 'none');
		$('blacklist_tab_disabled').setStyle('display', 'none');
		$('blacklist_tab_active').setStyle('display', '');
		$('content_tab_disabled').setStyle('display', '');

		$('remove_from_blacklist').setStyle('display', 'none');
		$('add_to_blacklist').setStyle('display', '');

		$('select_all_contacs').checked = false;
		he_contacts.choose_all_contacts($('select_all_contacs'));
	});

	$('blacklist_tab_active').addEvent('click', function()
  {
    $('tmp_items').adopt($$('.content_item'));
		$('he_contacts_list').adopt($$('.blacklist_item'));

		$(this).setStyle('display', 'none');

		$('content_tab_disabled').setStyle('display', 'none');
		$('blacklist_tab_disabled').setStyle('display', '');
		$('content_tab_active').setStyle('display', '');

		$('add_to_blacklist').setStyle('display', 'none');
		$('remove_from_blacklist').setStyle('display', '');

		$('select_all_contacs').checked = false;
		he_contacts.choose_all_contacts($('select_all_contacs'));
	});
});
</script>
<?php $blacklist = explode(',', $this->params['blacklist']); ?>

<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>
<div class="he_contacts">
  <h4 class="contacts_header"><?php echo $this->translate('UPDATES_Edit list'); ?>

		<div style="float:right;">
		<a href="javascript://" style="display:none" id="content_tab_active">
			<?php echo $this->translate('Content'); ?>
		</a>

		<span id="content_tab_disabled">
			<?php echo $this->translate('Content'); ?>
		</span>

		&nbsp;-&nbsp;
		<span id="blacklist_tab_disabled" style="display:none">
			<?php echo $this->translate('UPDATES_Blacklist'); ?>
		</span>

		<a href="javascript://" id="blacklist_tab_active">
			<?php echo $this->translate('UPDATES_Blacklist'); ?>
		</a>
		</div>

	</h4>
	<div style="clear:both"></div>
	<div style="font-weight:normal; color:red;" id='content_modified'>
		<?php if ($this->params['modified'] == 'true'): ?>
		<?php echo $this->translate('UPDATES_Please save changes to see modified content!') ?>
		<?php endif; ?>
	</div>
	<p><?php echo $this->translate('UPDATES_EDIT_WIDGET_CONTENT_LIST_DESCRIPTION'); ?></p>

  <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
  <div class="options">
    <div class="select_btns">
      <a href="javascript:void(0)" class="active" onClick="he_contacts.select('all'); he_contacts.add_class(this, 'active', $$('.select_btns a')[1]); this.blur();">
          <?php echo $this->translate("All"); ?>
      </a>
      <a href="javascript:void(0)" onClick="he_contacts.select('selected'); he_contacts.add_class(this, 'active', $$('.select_btns a')[0]); this.blur();">
          <?php echo $this->translate("Selected"); ?>&nbsp;(<span id="selected_contacts_count">0</span>)
      </a>
    </div>
    <div class="contacts_filter">
      <input type="text" id="contacts_filter" class="filter" />
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <?php endif; ?>

  <div class="contacts">
    <div id="he_contacts_list">
      <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
        <?php foreach ($this->items as $item): $item2 = $item; ?>

          <?php if($item instanceof Suggest_Model_Recommendation):
            $item = Engine_Api::_()->getItem($item2->object_type, $item2->object_id);
          endif; ?>

          <?php $itemDisabled = in_array($item2->getIdentity(), $this->disabledItems); ?>
          <?php $itemChecked = in_array($item2->getIdentity(), $this->checkedItems); ?>
          <a <?php if ($itemDisabled && $this->disabled_label): ?>title = "<?php echo $this->disabled_label; ?>"<?php endif; ?>  class="item visible <?php if ($itemDisabled) echo "disabled" ?> <?php if ($itemChecked) echo "active" ?> <?php if(in_array($item2->getIdentity(), $blacklist)): echo 'blacklist_item'; else: echo 'content_item'; endif; ?>" id="contact_<?php echo $item2->getIdentity(); ?>" href='javascript:he_contacts.choose_contact(<?php echo $item2->getIdentity(); ?>);'>
            <span class='photo' style='background-image: url()'>
							<?php if( !empty($item->photo_id) ): ?>
              	<?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
							<?php elseif($this->params['content_name'] == 'new_forum_topics'): ?>
									<?php $userTb = Engine_Api::_()->getItemTable('user');
										$select = $userTb->select()->where("user_id = ".$item->user_id)->limit(1);
										$owner = $userTb->fetchRow($select);
										echo $this->itemPhoto($owner, 'thumb.icon'); ?>
							<?php else: ?>
								<?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon'); ?>
							<?php endif; ?>
              <span class="inner"></span>
            </span>
						<?php if ($this->params['content_name'] == 'new_actions'): ?>
							<span class="name"><?php echo Engine_String::strip_tags($item->getContent()); ?></span>
						<?php else: ?>
            	<span class="name"><?php echo $item->getTitle(); ?></span>
						<?php endif; ?>
            <div class="clr"></div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no"><?php echo $this->translate("No items"); ?></div>
      <?php endif; ?>
      <div id="no_result" class="hidden"><?php echo $this->translate("There is no items."); ?></div>
      <div class="clr" id="he_contacts_end_line"></div>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <div class="btn" style="width:450px">
      <button onclick="he_contacts.callback = 'content.addToBlacklist';  he_contacts.send();" style="float:left;" id="add_to_blacklist"><?php echo $this->translate('UPDATES_add to blacklist'); ?></button>
			<button onclick="he_contacts.callback = 'content.removeFromBlacklist'; he_contacts.send();" style="float:left;display:none"  id="remove_from_blacklist"><?php echo $this->translate('UPDATES_remove from blacklist'); ?></button>

      <div class="he_contacts_choose_all" style="width: 100px; float:left; margin:5px">
        <input type="checkbox" onclick="he_contacts.choose_all_contacts($(this))" id="select_all_contacs" name="select_all_contacs">
        <label for="select_all_contacs"><?php echo $this->translate('Select all');?></label>
      </div>

    </div>
  </div>
  </div>


</div>

<?php endif; ?>

<div id="tmp_items" style="display:none">
	
</div>