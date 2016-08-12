<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */
//print_die("asdzxc");
?>
<?php
 $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/tinymce/tinymce.min.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pagedocument/externals/scripts/document.js');
//  ->appendFile($this->baseUrl() . '/application/modules/Pagedocument/externals/scripts/scridb_api.js');
?>
<!--<script type='text/javascript' src='https://www.scribd.com/javascripts/view.js'></script>-->
<script type='text/javascript' src='https://www.scribd.com/javascripts/scribd_api.js'></script>
<script type="text/javascript">
  //<![CDATA[


  function hide_download_allow(){
    var download = null;
    if( null != (download = $('download_allow-wrapper'))){
      var secure = $('secure_allow-1').checked;
      if(secure){
        download.removeClass('hidden');
        return;
      }
      download.addClass('hidden');
    }
  }

page_document.url.page = "<?php echo $this->subject->getHref(); ?>";
page_document.url.list = "<?php echo $this->url(array(), 'page_document'); ?>";
page_document.url.get_create_form = "<?php echo $this->url(array('action' => 'getcreateform'), 'page_document'); ?>";
page_document.url.get_edit_form = "<?php echo $this->url(array('action' => 'geteditform'), 'page_document'); ?>";
page_document.url.create = "<?php echo $this->url(array('action' => 'create'), 'page_document'); ?>";
page_document.url.view = "<?php echo $this->url(array('action' => 'view'), 'page_document'); ?>";
page_document.url.my_documents = "<?php echo $this->url(array('action' => 'mine'), 'page_document'); ?>";
page_document.url.delete_url = "<?php echo $this->url(array('action' => 'delete'), 'page_document'); ?>";
page_document.url.save = "<?php echo $this->url(array('action' => 'save'), 'page_document'); ?>";

page_document.page_id = <?php echo $this->subject->getIdentity(); ?>;
page_document.ipp = <?php echo $this->ipp ? $this->ipp : 10; ?>;
page_document.form_id = 'page_document_create_form';
page_document.edit_form_id = 'page_document_edit_form';
page_document.container_id = 'page_document_container';
page_document.allowed_post = <?php echo (int)$this->isAllowedPost; ?>;
page_document.allowed_comment = <?php echo (int)$this->isAllowedComment; ?>;

en4.core.runonce.add(function(){
	page_document.init();
	<?php echo $this->init_js_str; ?>
});
//]]>
</script>

<div id="page_document_navigation">
	<?php echo $this->render('navigation.tpl'); ?>
</div>
	
<div id="page_document_container">
  <?php
    if($this->content_info['content'] == 'document') {
      if(!empty($this->content_info['content_id'])){
        $tmp = $this->action('view', 'index', 'pagedocument', array('page_id'=>$this->subject->getIdentity(), 'document_id'=>$this->content_info['content_id']));
        echo $tmp;
      }
    }
  ?>

  <?php if($this->content_info['content'] != 'document') : ?>
	  <?php echo $this->render('list.tpl'); ?>
  <?php endif; ?>
</div>