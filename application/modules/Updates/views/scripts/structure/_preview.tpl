<?php
/**
* SocialEngine
*
* @category Application_Extensions
* @package Store
* @copyright Copyright Hire-Experts LLC
* @license http://www.hire-experts.com
* @version _preview.tpl: Preview 10/27/11 10:50 AM mt.uulu $
* @author Mirlan
*/
?>

<div class="content-edit" style="height: 10px">
  <a href="javascript:content.editContent('<?php echo $this->name; ?>', '<?php echo $this->id; ?>') ">
  <div style="font-weight: bold; color: #ffffff; font-size: 11px; padding: 0px 2px; background:#4B4B4B; border: 1px solid #A5A5A5; -moz-border-radius:2px;">
    <?php echo $this->translate('edit'); ?>
  </div>
  </a>
  <?php if ( isset( $this->blacklist) ): ?>
  <script type="text/javascript">
    content.blacklist["<?php echo $this->name; ?>"] = <?php echo Zend_Json::encode($this->blacklist); ?>
  </script>
  <?php endif; ?>
</div>