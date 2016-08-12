<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php 
 $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/tinymce/tiny_mce.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pageblog/externals/scripts/blog.js');
?>

<script type="text/javascript">
//<![CDATA[
en4.core.runonce.add(function (){
  tinyMCE.init({
    mode: "exact",
    plugins: "table,fullscreen,media,preview,paste,code,image,textcolor",
    theme_advanced_buttons1: "undo,redo,cleanup,removeformat,pasteword,|,code,media,image,fullscreen,preview",
    theme_advanced_buttons2: "",
    theme_advanced_buttons3: "",
    theme_advanced_toolbar_align: "left",
    theme_advanced_toolbar_location: "top",
    element_format: "html",
    height: "225px",
    convert_urls: false,
    media_strict: false,
    elements: "blog_body",
    language: "en",
    directionality: "ltr"
  });
});
en4.core.runonce.add(function (){
page_blog.url.list = "<?php echo $this->url(array(), 'page_blog'); ?>";
page_blog.url.page = "<?php echo $this->subject->getHref(); ?>"; /// for SEO by Kirill
page_blog.url.create = "<?php echo $this->url(array('action' => 'create'), 'page_blog'); ?>";
page_blog.url.view = "<?php echo $this->url(array('action' => 'view'), 'page_blog'); ?>";
page_blog.url.my_blogs = "<?php echo $this->url(array('action' => 'mine'), 'page_blog'); ?>";
page_blog.url.delete_url = "<?php echo $this->url(array('action' => 'delete'), 'page_blog'); ?>";
page_blog.url.edit = "<?php echo $this->url(array('action' => 'edit'), 'page_blog'); ?>";
page_blog.url.save = "<?php echo $this->url(array('action' => 'save'), 'page_blog'); ?>";
page_blog.url.remove_photo = "<?php echo $this->url(array('action' => 'remove-photo'), 'page_blog') . '?rp=1'?>";

page_blog.page_id = <?php echo $this->subject->getIdentity(); ?>;
page_blog.form_id = 'page_blog_create_form';
page_blog.ipp = <?php if ($this->ipp) echo $this->ipp; else echo 5;?>;
page_blog.container_id = 'page_blog_container';
page_blog.allowed_post = <?php echo (int)$this->isAllowedPost; ?>;
page_blog.allowed_comment = <?php echo (int)$this->isAllowedComment; ?>;

  page_blog.init();
  <?php echo $this->init_js_str; ?>
});
//]]>
</script>

<div id="page_blog_navigation">
	<?php echo $this->render('navigation.tpl'); ?>
</div>
	
<div id="page_blog_container">
<?php
    if(!empty($this->content_info['content'])) {
        if($this->content_info['content'] == 'blog'){
            if(!empty($this->content_info['content_id'])){
                $tmp = $this->action('view', 'index', 'pageblog', array('page_id'=>$this->subject->getIdentity(), 'blog_id'=>$this->content_info['content_id']));
                echo $tmp;
            }
        }
}?>
    <?php if($this->content_info['content'] != 'blog') : ?>
	<?php if ($this->subject->isTeamMember()): ?>
		<?php echo $this->render('list_edit.tpl'); ?>
	<?php else: ?>
		<?php echo $this->render('list.tpl'); ?>
	<?php endif; ?>
    <?php endif; ?>
</div>

<?php echo $this->render('form.tpl'); ?>