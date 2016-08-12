<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-06 17:53 idris $
 * @author     Idris
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Pagealbum/externals/scripts/jquery-1.7.2.min.js')
  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Pagealbum/externals/scripts/jquery.ad-gallery.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Pagealbum/externals/scripts/hap.js')
  ->appendScript('jQuery.noConflict();');
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl.'application/modules/Pagealbum/externals/scripts/album.js')
  ->appendFile($this->layout()->staticBaseUrl.'externals/moolasso/Lasso.js')
  ->appendFile($this->layout()->staticBaseUrl.'externals/moolasso/Lasso.Crop.js');
$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Pagealbum/externals/styles/main_album.css');
$this->headTranslate(
  array(
    'Start', 'View', 'Stop'
  )
);
if($this->album_open>0){
  if($this->photoviewer==1){
    ?>
    <script type="text/javascript">
      window.addEvent('domready', function() {
        opinions = {
          album_id:<?php echo $this->album_open?>,
          photo_id:<?php echo $this->photo_open?>,
          isPage: 1,
          events:2
        }
        PhotoViewer.open(opinions)
        PhotoViewer.view(<?php echo $this->photo_open?>)

      });


    </script>
  <?php
  }else{
    ?>
    <script type="text/javascript">
      window.addEvent('domready', function() {
        page_album.view(<?php echo $this->album_open?>,<?php echo $this->photo_open?>)
      });


    </script>
  <?php
  }


}
?>
<!--
opinions = {
album_id:7,
photo_id:560,
isPage: 1,
events:2
}
PhotoViewer.open(opinions)
PhotoViewer.view(560)
-->
  <script type="text/javascript">

    var galleries = {};

    page_album.url.album.list = "<?php echo $this->url(array('action' => 'index'), 'page_album'); ?>";
    page_album.url.page = "<?php echo $this->subject->getHref(); ?>"; /// for SEO by Kirill
    page_album.url.album.create = "<?php echo $this->url(array('action' => 'upload'), 'page_album'); ?>";
    page_album.url.album.view = "<?php echo $this->url(array('action' => 'view'), 'page_album'); ?>";
    page_album.url.album.manage = "<?php echo $this->url(array('action' => 'mine'), 'page_album'); ?>";
    page_album.url.album.delete_url = "<?php echo $this->url(array('action' => 'delete'), 'page_album'); ?>";
    page_album.url.album.edit = "<?php echo $this->url(array('action' => 'edit'), 'page_album'); ?>";
    page_album.url.album.save = "<?php echo $this->url(array('action' => 'save'), 'page_album'); ?>";
    page_album.url.photo.view = "<?php echo $this->url(array('action' => 'view-photo'), 'page_album'); ?>";
    page_album.url.photo.manage = "<?php echo $this->url(array('action' => 'manage-photo'), 'page_album'); ?>";
    page_album.url.photo.edit = "<?php echo $this->url(array('action' => 'edit-photo'), 'page_album'); ?>";
    page_album.url.photo.comments = "<?php echo $this->url(array('action' => 'load-comments'), 'page_album'); ?>";

    page_album.albums = <?php echo $this->albums_js; ?>;
    page_album.page_id = <?php echo (int)$this->subject->getIdentity(); ?>;
    page_album.ipp = <?php if ($this->ipp) echo $this->ipp; else echo 10; ?>;
    page_album.allowed_post = <?php echo (int)$this->isAllowedPost; ?>;
    page_album.allowed_comment = <?php echo (int)$this->isAllowedComment; ?>;

    en4.core.runonce.add(function(){
      page_album.init();
      page_album.init_forms();
    });

    en4.core.runonce.add(function(){
      <?php echo $this->init_js_str; ?>
    });

    var updateTextFields = function()
    {
      var album = document.getElementById("album");
      var name = document.getElementById("title-wrapper");
      var description = document.getElementById("description-wrapper");
      var tags = document.getElementById("tags-wrapper");

      if (album.value == 0)
      {
        name.style.display = "block";
        description.style.display = "block";
        tags.style.display = "block";
        $('form-upload-page-album').title.value = '';
        $('form-upload-page-album').description.value = '';
        $('form-upload-page-album').tags.value = '';
      }
      else
      {
        name.style.display = "none";
        description.style.display = "none";
        tags.style.display = "none";
        $('form-upload-page-album').title.value = page_album.albums[album.value].title;
        $('form-upload-page-album').description.value = page_album.albums[album.value].description;
        $('form-upload-page-album').tags.value = page_album.albums[album.value].tags;
      }
    }

    en4.core.runonce.add(updateTextFields);

    var findIndex = function(array, value) {
      var ctr = "";
      for (var i=0; i < array.length; i++) {
        if (array[i] == value) {
          return i;
        }
      }
      return ctr;
    };

  </script>

  <div id="page_album_navigation">
    <?php echo $this->render('navigation.tpl'); ?>
  </div>
  <div id="page_album_container">
    <?php if($this->content_info['content'] == 'pagealbum') {
      if(!empty($this->content_info['content_id'])){

        $tmp = $this->action('view', 'index', 'pagealbum', array('page_id'=>$this->subject->getIdentity(), 'album'=>$this->content_info['content_id']));
        echo $tmp;
      }
    } else {
      ?>

      <?php if ($this->isTeamMember): ?>
        <?php echo $this->render('manage.tpl'); ?>
      <?php else: ?>
        <?php echo $this->render('index.tpl'); ?>
      <?php endif; ?>

    <?php } ?>
  </div>

<?php echo $this->render('create.tpl'); ?>

<?php echo $this->render('edit.tpl'); ?>