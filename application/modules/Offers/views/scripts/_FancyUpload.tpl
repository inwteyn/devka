<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageoffers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: fancy_upload.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<?php
$this->headScript()
  ->appendFile($this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.js')
  ->appendFile($this->baseUrl() . '/externals/fancyupload/Fx.ProgressBar.js')
  ->appendFile($this->baseUrl() . '/externals/fancyupload/FancyUpload2.js');
?>

<script type="text/javascript">
  var offersUploadCount = 0;
  var offersUploaderSwf = '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf' ?>';
  var offers_up = {};
  en4.core.runonce.add(function(){
    offers_up = new FancyUpload2($('offers-demo-status'), $('offers-demo-list'), {
      verbose: false,
      appendCookieData: true,
      'url' : '<?php echo $this->url(array('action' => 'upload-photo'), 'offer_photos') ?>?ul=1',
      path: offersUploaderSwf,
      typeFilter: {
        'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
      },
      target: 'offers-demo-browse',
      onLoad: function() {
        $('offers-demo-status').removeClass('hide'); // we show the actual UI
        $('offers-demo-fallback').destroy(); // ... and hide the plain form

        // We relay the interactions with the overlayed flash to the link
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

        // Interactions for the 2 other buttons
        if ($('offers_submit-wrapper'))
          $('offers_submit-wrapper').hide();
        $('offers-demo-clear').addEvent('click', function() {
          offers_up.remove(); // remove all files
          if ($('offers_fancyuploadfileids'))
            $('offers_fancyuploadfileids').value = '';
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
        var demostatuscurrent = document.getElementById("offers-demo-status-current");
        var demostatusoverall = document.getElementById("offers-demo-status-overall");
        var demosubmit = document.getElementById("offers_submit-wrapper");
        var democlear = document.getElementById("offers-demo-clear");

        demostatuscurrent.style.display = "none";
        demostatusoverall.style.display = "none";

        if (democlear)
          democlear.style.display = "inline";

        if (demosubmit)
          demosubmit.style.display = "block";
      },

      onFileStart: function() {
        offersUploadCount += 1;
      },
      onFileRemove: function(file) {
        offersUploadCount -= 1;
        file_id = file.photo_id;
        request = new Request.JSON({
          'format' : 'json',
          'url' : '<?php echo $this->url(array('action' => 'remove-photo'), 'offer_photos') ?>?rp=1',
          'data': {
            'format': 'json',
            'photo_id' : file_id
          },
          'onSuccess' : function(responseJSON) {
            $$($('form-upload-offers').elements).each(function($element){
              $element.disabled = false;
            });
            return false;
          }
        });

        if (file_id) {
          $$($('form-upload-offers').elements).each(function($element){
            $element.disabled = true;
          });
          request.send();
        }

        var fileids = $('offers_fancyuploadfileids');

        if ($("offers-demo-list").getChildren('li').length == 0)
        {
          var democlear  = document.getElementById("offers-demo-clear");
          var demolist   = document.getElementById("offers-demo-list");
          var demosubmit = document.getElementById("offers_submit-wrapper");
          democlear.style.display  = "none";
          demolist.style.display   = "none";
          if (demosubmit){
            demosubmit.style.display = "none";
          }
          if (demolist){
            demolist.style.display = "none";
          }
        }
        if (fileids)
          fileids.value = fileids.value.replace(file_id, "");
      },
      onSelectSuccess: function(file) {
        $('offers-demo-list').style.display = 'block';
        var democlear = document.getElementById("offers-demo-clear");
        var demostatuscurrent = document.getElementById("offers-demo-status-current");
        var demostatusoverall = document.getElementById("offers-demo-status-overall");

        democlear.style.display = "inline";
        demostatuscurrent.style.display = "block";
        demostatusoverall.style.display = "block";
        offers_up.start();
      },
      /**
       * This one was directly in FancyUpload2 before, the event makes it
       * easier for you, to add your own response handling (you probably want
       * to send something else than JSON or different items).
       */
      onFileSuccess: function(file, response) {
        var json = new Hash(JSON.decode(response, true) || {});

        if (json.get('status') == '1') {
          file.element.addClass('file-success');
          file.info.set('html', '<span>' + '<?php echo $this->string()->escapeJavascript($this->translate('Upload complete.')) ?>' + '</span>');
          file.photo_id   = json.get('photo_id');
          var fileids = $('offers_fancyuploadfileids');
          if (fileids) {
            if (fileids.value.length)
              fileids.value += ' ';
            fileids.value += json.get('photo_id');
          }
        } else {
          file.element.addClass('file-failed');
          file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate('An error occurred:')) ?></span> ' + (json.get('error') ? (json.get('error')) : response));
        }
      },

      /**
       * onFail is called when the Flash movie got bashed by some browser plugin
       * like Adblock or Flashblock.
       */
      onFail: function(error) {
        switch (error) {
          case 'hidden': // works after enabling the movie and clicking refresh
            // alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).")) ?>');
            break;
          case 'blocked': // This no *full* fail, it works after the user clicks the button
            // alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).")) ?>');
            break;
          case 'empty': // Oh oh, wrong path
            // alert('<?php echo $this->string()->escapeJavascript($this->translate("A required file was not found, please be patient and we'll fix this.")) ?>');
            break;
          case 'flash': // no flash 9+
            // alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, install the latest Adobe Flash plugin.")) ?>');
            break;
        }
      }
    });
  });
</script>

<input type="hidden" name="file" id="offers_fancyuploadfileids" value ="" />
<fieldset id="offers-demo-fallback">
  <legend><?php echo $this->translate("OFFERS_pageoffers_File Upload") ?></legend>
  <p>
    <?php echo $this->translate('OFFERS_pageoffers_UPLOAD_DESCRIPTION') ?>
  </p>
  <label for="demo-offerslabel">
    <?php echo $this->translate('OFFERS_pageoffers_Upload Offers:') ?>
    <input type="file" name="Filedata" />

  </label>
</fieldset>

<div id="offers-demo-status" class="hide">
<!--  <div>-->
<!--    --><?php //echo $this->translate('OFFERS_pageoffers_upload_description') ?>
<!--  </div>-->
  <div>
    <a class="buttonlink icon_offers_image_new" href="javascript:void(0);" id="offers-demo-browse"><?php echo $this->translate('OFFERS_pageoffers_add_photo') ?></a>
    <a class="buttonlink icon_clearlist" style="display: none;" href="javascript:void(0);" id="offers-demo-clear"><?php echo $this->translate('OFFERS_pageoffers_clear_list') ?></a>
  </div>
  <div class="demo-status-overall" id="offers-demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress overall-progress" alt="" />
  </div>
  <div class="demo-status-current" id="offers-demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress current-progress" alt="" />
  </div>
  <div class="current-text"></div>
</div>

<ul id="offers-demo-list"></ul>