<style>
  #global_page_store-admin-fields-index ul.admin_fields .field_extraoptions .field_extraoptions_contents_wrapper {
    display: block;
  }
  #global_page_store-admin-fields-index ul.admin_fields .field_extraoptions_contents_wrapper {
    position: inherit;
    border: none;
    background-color: #fff;
  }
</style>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php
  // Render the admin js
  echo $this->render('_jsStoreAdmin.tpl')
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active'=>$this->activeMenu)); ?>

<p>
  <?php echo $this->translate("STORE_Create your own field system in Store Plugin."); ?>
</p>

<br />

<div class="admin_fields_type">
  <h3><?php echo $this->translate("STORE_Editing Store Category"); ?>:</h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>

  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate("STORE_Rename Category"); ?></a>
  <?php if( $this->option_id != 1 ): ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate("STORE_Delete Category"); ?></a>
  <?php endif; ?>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate("STORE_Create New Category"); ?></a>
</div>

<br />

<div class="admin_fields_options">
  <a style="display: <?php echo (count($this->secondLevelMaps) ? 'none' : '')?>;" href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("STORE_Add sub category"); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate("STORE_Add Heading"); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order"); ?></a>
</div>

<br />

<ul class="admin_fields">
    <?php foreach( $this->secondLevelMaps as $map ): ?>
      <?php echo $this->storeAdminFieldMeta($map) ?>
    <?php endforeach; ?>
</ul>

<br />
<br />