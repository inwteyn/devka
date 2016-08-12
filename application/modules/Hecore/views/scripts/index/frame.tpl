<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  01.06.12 16:48 TeaJay $
 * @author     Drujinin S
 */
?>
<script>
    window.addEvent('domready',function(){
        var url_request_name_module = "<?php echo $this->module;?>";
        var src_url_for_frame = "http://www.hire-experts.com/product-ads-view.php?plugin=";
        var all_plugins = "<?php echo $this->all_modules;?>";
        if(url_request_name_module){
            var url = src_url_for_frame+url_request_name_module+"&modules=<?php echo $this->all_modules;?>";
            console.log(url);
            console.log('<?php echo $this->all_modules;?>');

            if($('frame_on_page')){
                $('frame_on_page').set('src',url);
            }
        }
    });
</script>

<div id="frame_test" style="float: right;">
    <iframe id="frame_on_page" width="225" height="650" align="middle" src="http://www.hire-experts.com/product-ads-view.php?plugin=wall"></iframe>
</div>