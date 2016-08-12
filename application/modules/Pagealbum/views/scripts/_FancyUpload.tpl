<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
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
var uploadCount = 0;
var extraData = <?php echo $this->jsonInline($this->data); ?>;

window.addEvent('domready', function(){
   var up = new FancyUpload2($('demo-status'), $('demo-list'), {
       verbose: false,
       appendCookieData: true,
       url: $('form-upload-page-album').action + '?ul=1',
       path: '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf'; ?>',
       typeFilter: {
           'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
       },
       target: 'demo-browse',
       data: extraData,
       onLoad: function() {
           $('demo-status').removeClass('hide');
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
           $('demo-clear').addEvent('click', function() {
               up.remove(); // remove all files
               var fileids = document.getElementById('fancyuploadfileids');

               fileids.value ="";
               return false;
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
           var demostatuscurrent = document.getElementById("demo-status-current");
           var demostatusoverall = document.getElementById("demo-status-overall");
           var demosubmit = document.getElementById("submit-wrapper");

           demostatuscurrent.style.display = "none";
           demostatusoverall.style.display = "none";
           demosubmit.style.display = "block";
       },

       onFileStart: function() {
           uploadCount += 1;
       },

       onFileRemove: function(file) {
           uploadCount -= 1;
           file_id = file.photo_id;
           request = new Request.JSON({
               'format' : 'json',
               'url' : '<?php echo $this->url(Array('action'=>'remove-photo'), 'page_album') ?>?rp=1',
               'data': {
                   'photo_id' : file_id
               },
               'onSuccess' : function(responseJSON) {
                   return false;
               }
           });

           request.send();
           var fileids = document.getElementById('fancyuploadfileids');

           if (uploadCount == 0)
           {
               var democlear = document.getElementById("demo-clear");
               var demolist = document.getElementById("demo-list");
               var demosubmit = document.getElementById("submit-wrapper");
               democlear.style.display = "none";
               demolist.style.display = "none";
               demosubmit.style.display = "none";
           }
           fileids.value = fileids.value.replace(file_id, "");
       },

       onSelectSuccess: function(file) {
           $('demo-list').style.display = 'block';
           var democlear = document.getElementById("demo-clear");
           var demostatuscurrent = document.getElementById("demo-status-current");
           var demostatusoverall = document.getElementById("demo-status-overall");

           democlear.style.display = "inline";
           demostatuscurrent.style.display = "block";
           demostatusoverall.style.display = "block";
           up.start();
       } ,
       /**
        * This one was directly in FancyUpload2 before, the event makes it
        * easier for you, to add your own response handling (you probably want
        * to send something else than JSON or different items).
        */
       onFileSuccess: function(file, response) {
           var json = new Hash(JSON.decode(response, true) || {});

           if (json.get('status') == '1') {
               file.element.addClass('file-success');
               file.info.set('html', '<span>Upload complete.</span>');
               var fileids = document.getElementById('fancyuploadfileids');
               fileids.value = fileids.value + json.get('photo_id') + " ";
               file.photo_id = json.get('photo_id');

           } else {
               file.element.addClass('file-failed');
               file.info.set('html', '<span><?php echo $this->translate('An error occurred:') ?></span> ' + (json.get('error') ? (json.get('error')) : response));
               //file.info.set('html', '<span>An error occurred:</span> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
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

<input type="hidden" name="file" id="fancyuploadfileids" value ="" />
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

<div id="demo-status" class="hide">
  <div>
    <?php echo $this->translate('PAGEALBUM_UPLOAD_DESCRIPTION') ?>
  </div>
  <div>
    <a class="buttonlink icon_photos_new" href="javascript:void(0);" id="demo-browse"><?php echo $this->translate('Add Photos') ?></a>
    <a class="buttonlink icon_clearlist" href="javascript:void(0);" id="demo-clear" style='display: none;'><?php echo $this->translate("Clear List"); ?></a>
  </div>
  <div class="demo-status-overall" id="demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress overall-progress" />
  </div>
  <div class="demo-status-current" id="demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress current-progress" />
  </div>
  <div class="current-text"></div>
</div>
<ul id="demo-list"></ul>