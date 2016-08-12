<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  01.06.12 16:48 TeaJay $
 * @author     Taalay
 */
?>
<h2><?php echo $this->translate("Store Addons") ?></h2>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active'=>$this->activeMenu)); ?>

<?php echo $this->content()->renderWidget('store.admin-addons-menu', array('active'=>$this->activeMenu)); ?>
<div class="tip">
  <span>
    <?php echo $this->translate('STORE_You have no any addons yet'); ?>
  </span>
</div>


<?php

echo $this->action("frame","index","hecore");

?>

<!--<script>
  window.addEvent('domready',function(){
    var url_request_name_module = "<?php /*echo $this->module;*/?>";
    var src_url_for_frame = "http://dev.hire-experts.com/product-ads-view.php?plugin=";
    var all_plugins = "<?php /*echo $this->all_modules;*/?>";
    if(url_request_name_module){
      var url = src_url_for_frame+url_request_name_module+"&modules=<?php /*echo $this->on_site_modules;*/?>";

      if($('frame_on_page')){
        $('frame_on_page').set('src',url);
      }
    }
  });
</script>

<div id="frame" style="float: right;">
<iframe id="frame_on_page" width="225" height="650" align="middle" src="http://dev.hire-experts.com/product-ads-view.php?plugin=wall"></iframe>
</div>-->