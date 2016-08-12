<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<div class="theme_<?php echo $this->activeTheme->name; ?>">

<?php if ($this->error): ?>
<ul class="form-errors"><li><ul class="errors"><li><?php echo $this->message; ?></li></ul></li></ul>
<?php return; endif; ?>


<?php if( count($this->navigation) > 0 ): ?>
<?php
// Render the menu
echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
?>

<script type="text/javascript">
page.page_id = <?php echo $this->page->getIdentity(); ?>;
page.ajax_url = "<?php echo $this->url(array('action' => 'ajax'), 'admin_general'); ?>";
window.addEvent('domready', function(){
	page.init();
});
</script>

<div class="page_edit_title">
  <div class="l">
  	<?php echo $this->htmlLink( $this->page->getHref(), $this->itemPhoto($this->page, 'thumb.icon') ); ?>
  </div>
  <div class="r">
    <h3><?php echo $this->page->getTitle(); ?></h3>
	  <div class="pages_layoutbox_menu">
	  <ul>
	    <li id="pages_layoutbox_menu_createpage">
	      <?php echo $this->htmlLink( $this->url(array(), 'page_create'), $this->translate('Create Page'), array('class' => 'touchajax') ); ?>
	     </li>
	     <li id="pages_layoutbox_menu_viewpage">
	      <?php echo $this->htmlLink( $this->url(array( 'page_id' => $this->page->url ), 'page_view'), $this->translate('View Page'), array('class' => 'touchajax') ); ?>
	     </li>
	     <li id="pages_layoutbox_menu_deletepage">
	      <?php echo $this->htmlLink( $this->url(array('action' => 'delete', 'page_id' => $this->page->page_id), 'page_team'), $this->translate('Delete Page'), array('class' => 'smoothbox') ); ?>
	     </li>
	  </ul>
	  </div>
  </div>
  <div class="clr"></div>
</div>
<div class="clr"></div>

  <div class='layout_middle'>
  


    <div class="global_form_box">
      <div class="page_edit_info">
        <div class="page_edit_title"><?php echo $this->translate("Information"); ?></div>
        <div class="page_edit_options">
        <?php if ($this->edit == 'info'): ?>
        	<?php echo $this->htmlLink("javascript:void(0)", $this->translate('Edit'), array("id" => "page_edit_edit_info", "class" => "hidden", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_hide_info', 'page_edit_form_info', 'page_edit_desc_info', true);")); ?>
        	<?php echo $this->htmlLink("javascript:void(0)", $this->translate('Hide'), array("id" => "page_edit_hide_info", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_edit_info', 'page_edit_form_info', 'page_edit_desc_info', false);")); ?>
        <?php else: ?>
          <?php echo $this->htmlLink("javascript:void(0)", $this->translate('Edit'), array("id" => "page_edit_edit_info", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_hide_info', 'page_edit_form_info', 'page_edit_desc_info', true);")); ?>
          <?php echo $this->htmlLink("javascript:void(0)", $this->translate('Hide'), array("id" => "page_edit_hide_info", "class" => "hidden", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_edit_info', 'page_edit_form_info', 'page_edit_desc_info', false);")); ?>
        <?php endif; ?>
        </div>
        <div class="page_edit_desc" id="page_edit_desc_info"><?php echo $this->translate('Edit your Page title, description, location and other information.'); ?></div>
        <?php echo $this->fieldForm->render($this); ?>
      </div>
    </div>
    <br />


<!-- подгрузка фото НЕ реализована
 <div class="global_form_box">
      <div class="page_edit_photo">
        <div class="page_edit_title"><?php /*echo $this->translate('Photo'); */?></div>
        <div class="page_edit_options">
        <?php /*if ($this->edit == 'photo'): */?>
        	<?php /*echo $this->htmlLink("javascript:void(0)", $this->translate('Edit'), array("id" => "page_edit_edit_photo", "class" => "hidden", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_hide_photo', 'page_edit_form_photo', 'page_edit_desc_photo', true);")); */?>
	        <?php /*echo $this->htmlLink("javascript:void(0)", $this->translate('Hide'), array("id" => "page_edit_hide_photo", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_edit_photo', 'page_edit_form_photo', 'page_edit_desc_photo', false);")); */?>
	      <?php /*else: */?>
	        <?php /*echo $this->htmlLink("javascript:void(0)", $this->translate('Edit'), array("id" => "page_edit_edit_photo", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_hide_photo', 'page_edit_form_photo', 'page_edit_desc_photo', true);")); */?>
          <?php /*echo $this->htmlLink("javascript:void(0)", $this->translate('Hide'), array("id" => "page_edit_hide_photo", "class" => "hidden", "onclick" => "toggle_page_edit_tab(this.id, 'page_edit_edit_photo', 'page_edit_form_photo', 'page_edit_desc_photo', false);")); */?>
	      <?php /*endif; */?>
        </div>

        <div class="page_edit_desc <?php /*if ($this->edit == 'photo') echo "hidden"; */?>" id="page_edit_desc_photo"><?php /*echo $this->translate('Edit your Page photo.') */?></div>

        <div class="page_edit_form <?php /*if ($this->edit != 'photo') echo "hidden"; */?>" id="page_edit_form_photo">
          <div class="form-elements">
            <div class="page_edit_photo_current">
            	<?php /*echo $this->itemPhoto($this->page, 'thumb.profile'); */?>
            	<?php /*if ($this->page->photo_id > 0): */?>
            	<?php /*echo $this->htmlLink($this->url(array('action' => 'delete-photo', 'page_id' => $this->page->page_id), 'page_team'), $this->translate('Delete Photo')); */?>
            	<?php /*endif; */?>
            </div>
            <div class="page_edit_photo_new">
              <?php /*echo $this->photoForm->setAttrib('class', 'form-wrapper touchupload')->render($this); */?>
            </div>
            <br class="clr" />
          </div>
        </div>
      </div>
    </div>
-->



</div>

</div>