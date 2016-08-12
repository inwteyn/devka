<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl.'application/modules/Offers/externals/styles/main.css'); ?>

<h2><?php echo $this->translate("OFFERS_Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render();
  ?>
</div>
<?php endif; ?>

<br />
<div class="offers_admin_manage_menu">
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'index'), 'offer_admin_manage');?>"><?php echo $this->translate('OFFERS_View Offers'); ?></a>
  <span class="offers_admin_manage_menu_items active_item"><?php echo $this->translate('OFFERS_Create Offer');?></span>
</div>
<div class="settings">
  <?php echo $this->form->render(); ?>
</div>