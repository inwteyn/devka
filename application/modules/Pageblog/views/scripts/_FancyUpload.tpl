<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _FancyUpload.tpl 2010-09-06 17:53 idris $
 * @author     Idris
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');

  $this->headLink()
    ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');
?>

<script type="text/javascript">
var blog_uploadCount = 0;
var extraData = <?php echo $this->jsonInline($this->data); ?>;
var fancy = {};

en4.core.runonce.add(function(){
  fancy = new FancyUpload2($('blog-demo-status'), $('blog-demo-list'), {
      verbose: false,
      multiple: false,
      appendCookieData: true,

      url: $('page_blog_create_form').action + '?ul=1',

      path: '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf'; ?>',
      typeFilter: {
          'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
      },
      target: 'blog-demo-browse',
      data: extraData,
      onLoad: function() {
          $('blog-demo-status').removeClass('hide');
          $('demo-fallback').destroy();
          this.target.addEvents({
              click: function() {
                  return false;
              },
              mouseenter: function() {
                  this.addClass('hover');
              },
              mouseleave: function() {
                  this.removeClass('hover');
                  this.blur();
              },
              mousedown: function() {
                  this.focus();
              }
          });
      },

      /**
       * Is called when files were not added, "files" is an array of invalid File classes.
       *
       * This example creates a list of error elements directly in the file list, which
       * hide on click.
       */
      onSelectFail: function(files) {
          files.each(function(file) {
              new Element('li', {
                  'class': 'validation-error',
                  html: file.validationErrorMessage || file.validationError,
                  title: MooTools.lang.get('FancyUpload', 'removeTitle'),
                  events: {
                      click: function() {
                          this.destroy();
                      }
                  }
              }).inject(this.list, 'top');
          }, this);
      },

      onComplete: function hideProgress() {
          var demostatuscurrent = document.getElementById("blog-demo-status-current");
          var demostatusoverall = document.getElementById("blog-demo-status-overall");

          demostatuscurrent.style.display = "none";
          demostatusoverall.style.display = "none";
      },

      onFileStart: function() {
          blog_uploadCount += 1;
      },

      onFileRemove: function(file) {
          blog_uploadCount -= 1;
          file_id = file.photo_id;
          request = new Request.JSON({
              'format' : 'json',
              'url' : '<?php echo $this->url(Array('action'=>'remove-photo'), 'page_blog') ?>?rp=1',
              'data': {
                  'photo_id' : file_id
              },
              'onSuccess' : function(responseJSON) {
                  return false;
              }
          });

          if(file_id)
            request.send();

          var fileids = document.getElementById('fancyblogphotoid');

          if (blog_uploadCount == 0)
          {
              var demolist = document.getElementById("blog-demo-list");

              demolist.style.display = "none";
          }
          fileids.value = fileids.value.replace(file_id, "");

          document.getElementById("blog-demo-browse").style.display = "block";
      },

      onSelectSuccess: function(file) {
          $('blog-demo-list').style.display = 'block';
          var demostatuscurrent = document.getElementById("blog-demo-status-current");
          var demostatusoverall = document.getElementById("blog-demo-status-overall");
          var demoaddlink = document.getElementById("blog-demo-browse");

          demostatuscurrent.style.display = "block";
          demostatusoverall.style.display = "block";
          demoaddlink.style.display = "none";

          fancy.start();
          blog_uploadCount++;
      } ,
      /**
       * This one was directly in FancyUpload2 before, the event makes it
       * easier for you, to add your own response handling (you probably want
       * to send something else than JSON or different items).
       */
      onFileSuccess: function(file, response) {
          console.log(response);
          var json = new Hash(JSON.decode(response, true) || {});

          if (json.get('status') == '1') {
              file.element.addClass('file-success');
              //file.info.set('html', '<span>Upload complete.</span>');
              file.info.set('html', '<img src="' + en4.core.baseUrl + json.photo.storage_path  + '" />');
              var fileids = document.getElementById('fancyblogphotoid');
              fileids.value = json.get('photo_id');
              file.photo_id = json.get('photo_id');

          } else {
              file.element.addClass('file-failed');
              file.info.set('html', '<span><?php echo $this->translate('An error occurred:') ?></span> ' + (json.get('error') ? (json.get('error')) : response));
          }
      },

      /**
       * onFail is called when the Flash movie got bashed by some browser plugin
       * like Adblock or Flashblock.
       */
      onFail: function(error) {
          switch (error) {
              case 'hidden': // works after enabling the movie and clicking refresh
                  // alert('<?php echo $this->translate('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).') ?>');
                  break;
              case 'blocked': // This no *full* fail, it works after the user clicks the button
                  // alert('<?php echo $this->translate('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).') ?>');
                  break;
              case 'empty': // Oh oh, wrong path
                  // alert('<?php echo $this->translate('A required file was not found, please be patient and we will fix this.') ?>');
                  break;
              case 'flash': // no flash 9+ :(
              // alert('<?php echo $this->translate('To enable the embedded uploader, install the latest Adobe Flash plugin.') ?>')
          }
      }

  });
});
</script>

<input type="hidden" name="file" id="fancyblogphotoid" value ="" />
<fieldset id="demo-fallback">
  <legend><?php echo $this->translate('File Upload') ?></legend>
  <p>
    <?php echo $this->translate('Click "Browse..." to select the file you would like to upload.') ?>
  </p>
  <label for="demo-photoupload">
    <?php echo $this->translate('Upload a Photo:') ?>
    <input type="file" name="Filedata" />
  </label>
</fieldset>

<div id="blog-demo-status" class="hide">

  <div>
    <a class="buttonlink icon_photos_new" href="javascript:void(0);" id="blog-demo-browse"><?php echo $this->translate('Pageblog_Add_Photo') ?></a>
  </div>
  <div class="blog-demo-status-overall" id="blog-demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress overall-progress" />
  </div>
  <div class="blog-demo-status-current" id="blog-demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress current-progress" />
  </div>
  <div class="current-text"></div>
</div>
<ul id="blog-demo-list"></ul>