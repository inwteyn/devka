<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contacts.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<?php
	if ( !($this->items instanceof Zend_Paginator) && is_array($this->items) && array_key_exists('all', $this->items) ):
    $this->potentialItems = !empty($this->items['potential']) ? $this->items['potential'] : array();
    $this->items = !empty($this->items['all']) ? $this->items['all'] : $this->items;
	endif;
?>

<?php if ($this->error): ?>
  <div class="contacts_error"><?php echo $this->message; ?></div>
<?php else: ?>
<script type="text/javascript">
	(function(){

	window.HE_CONTACTS = null;
	en4.core.runonce.add(function(){
		$('contacts_filter').setStyle('width', ($('list_filter_cont').getSize().x - 31));
		var options = {
			c: "<?php echo $this->callback; ?>",
			listType: "all",
			m: "<?php echo $this->module; ?>",
			l: "<?php echo $this->list; ?>",
			t: "<?php echo $this->title; ?>",
			ipp: <?php echo (int)$this->ipp; ?>,
			p: <?php echo (int)$this->items->getCurrentPageNumber(); ?>,
			total: <?php echo (int)$this->items->getTotalItemCount(); ?>,
			params: <?php echo Zend_Json::encode($this->params); ?>,
			nli: <?php echo (int)$this->not_logged_in; ?>,
			contacts: <?php echo Zend_Json_Encoder::encode($this->checkedItems); ?>
		};

		for(key in options.contacts)
		{
			if ($type(options.contacts[key]) == 'string'){
				options.contacts[key] = parseInt(options.contacts[key]);
			}
		}

		window.HE_CONTACTS = new HEContacts(options);
		window.HE_CONTACTS.init();
		window.HE_CONTACTS.needPagination = <?php echo (int)$this->need_pagination; ?>;
	});

})();
</script>

<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>
<?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
<div class="he_contacts">
  <div class="header_title"><p class="title"><?php echo $this->translate($this->title); ?><p></div>
	<div class="select_btns">
		<a href="javascript:void(0)" id="he_contacts_list_all" class="active">
				<?php echo $this->translate("All"); ?>
		</a>
		<a href="javascript:void(0)" id="he_contacts_list_selected">
				<?php echo $this->translate("Selected"); ?>
		</a>
	</div>

  <?php if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
    <div class="options">
      <div class="contacts_filter">
        <div class="list_filter_cont" id="list_filter_cont">
          <input type="text"
								 class="list_filter filter_default_value"
								 title="Search"
								 id="contacts_filter"
								 value="<?php echo $this->translate("Search"); ?>"
								 name="q"
								 onfocus="Touch.focus($(this), 'filter_default_value')"
								 onblur="Touch.blur($(this), 'filter_default_value', '<?php echo $this->translate("Search"); ?>')"
						/>
          <a class="list_filter_btn" id="contacts_filter_submit" title="Search" href="javascript://"></a>
        </div>
      </div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  <?php endif; ?>

  <div class="contacts">
		<div id="he_contacts_loading" style="display:none;">&nbsp;</div>
    <div id="he_contacts_list">
      <?php echo $this->render('_contacts_items.tpl'); ?>
    </div>

    <?php if ($this->items->count() > $this->items->getCurrentPageNumber()): ?>
      <div class="clr"></div>
      <a class="pagination" id="contacts_more" href="javascript:void(0);"><?php echo $this->translate("More"); ?></a>
    <?php endif; ?>

    <div class="clr"></div>
  </div>
  <div class="clr"></div>

  <div class="btn" style="width:450px">
    <button id="submit_contacts" style="float:left;"><?php echo $this->translate((isset($this->params['button_label']))?$this->params['button_label']:"Send"); ?></button>

    <div class="he_contacts_choose_all" style="width: 100px; float:left; margin-left:10px;height:28px;">
      <input type="checkbox" id="select_all_contacs" name="select_all_contacs" style="height:28px;"/>
      <label for="select_all_contacs" style="line-height:28px;"><?php echo $this->translate('HECORE_Select all');?></label>
    </div>

    <div></div>

  </div>
</div>

<?php else : ?>
  <br>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have not friends'); ?>
    </span>
  </div>
<?php endif; ?>
<?php endif; ?>

<br /><br />