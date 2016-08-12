<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: fancy_upload.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
    
  $this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
  ));
?>

<script type="text/javascript">
  var musicUploadCount = 0;
  var musicUploaderSwf = '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf' ?>';
  var music_up = {};
  en4.core.runonce.add(function(){
    music_up = new FancyUpload2($('music-demo-status'), $('music-demo-list'), {
      verbose: false,
      appendCookieData: true,
      url: $('form-upload-music').action + '?ul=1&user_id='+page_music.user_id+'&playlist_id='+page_music.playlist_id,
      path: musicUploaderSwf,
      typeFilter: {
        'Music (*.mp3,*.m4a,*.aac,*.mp4)': '*.mp3; *.m4a; *.aac; *.mp4'
      },
      target: 'music-demo-browse',
      onLoad: function() {
        $('music-demo-status').removeClass('hide'); // we show the actual UI
        $('music-demo-fallback').destroy(); // ... and hide the plain form

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
        if ($('music_submit-wrapper'))
          $('music_submit-wrapper').hide();
        $('music-demo-clear').addEvent('click', function() {
          music_up.remove(); // remove all files
          if ($('music_fancyuploadfileids'))
            $('music_fancyuploadfileids').value = '';
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
        var demostatuscurrent = document.getElementById("music-demo-status-current");
        var demostatusoverall = document.getElementById("music-demo-status-overall");
        var demosubmit = document.getElementById("music_submit-wrapper");
        var democlear = document.getElementById("music-demo-clear");

        demostatuscurrent.style.display = "none";
        demostatusoverall.style.display = "none";
        
        if (democlear)
          democlear.style.display = "inline";
          
        if (demosubmit)
          demosubmit.style.display = "block";
      },

      onFileStart: function() {
        musicUploadCount += 1;
      },
      onFileRemove: function(file) {
        musicUploadCount -= 1;
        file_id = file.song_id;
        request = new Request.JSON({
          'format' : 'json',
          'url' : '<?php echo $this->url(array('action' => 'remove-song'), 'page_music') ?>',
          'data': {
            'format': 'json',
            'song_id' : file_id
          },
          'onSuccess' : function(responseJSON) {
						$$($('form-upload-music').elements).each(function($element){
							$element.disabled = false;
						});
            return false;
          }
        });
				
				if (file_id) {
					$$($('form-upload-music').elements).each(function($element){
						$element.disabled = true;
					});
					request.send();
				}
				
        var fileids = $('music_fancyuploadfileids');

        if ($("music-demo-list").getChildren('li').length == 0)
        {
          var democlear  = document.getElementById("music-demo-clear");
          var demolist   = document.getElementById("music-demo-list");
          var demosubmit = document.getElementById("music_submit-wrapper");
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
        $('music-demo-list').style.display = 'block';
        var democlear = document.getElementById("music-demo-clear");
        var demostatuscurrent = document.getElementById("music-demo-status-current");
        var demostatusoverall = document.getElementById("music-demo-status-overall");

        democlear.style.display = "inline";
        demostatuscurrent.style.display = "block";
        demostatusoverall.style.display = "block";
        music_up.start();
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
          file.song_id   = json.get('song_id');
          var fileids = $('music_fancyuploadfileids');
          if (fileids) {
            if (fileids.value.length)
              fileids.value += ' ';
            fileids.value += json.get('song_id');
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

<input type="hidden" name="music_fancyuploadfileids" id="music_fancyuploadfileids" value ="" />
<fieldset id="music-demo-fallback">
  <legend><?php echo $this->translate("pagemusic_File Upload") ?></legend>
  <p>
    <?php echo $this->translate('PAGEMUSIC_UPLOAD_MUSIC_DESCRIPTION') ?>
  </p>
  <label for="demo-musiclabel">
    <?php echo $this->translate('pagemusic_Upload Music:') ?>
    <input id="<?php echo $this->element->getName() ?>"
           type="file"
           name="<?php echo $this->element->getName() ?>"
           value="<?php echo $this->element->getValue() ?>" />

  </label>
</fieldset>

<div id="music-demo-status" class="hide">
  <div>
    <?php echo $this->translate('_PAGEMUSIC_UPLOAD_MUSIC_DESCRIPTION') ?>
  </div>
  <div>
    <a class="buttonlink icon_music_new" href="javascript:void(0);" id="music-demo-browse"><?php echo $this->translate('pagemusic_Add Music') ?></a>
    <a class="buttonlink icon_clearlist" style="display: none;" href="javascript:void(0);" id="music-demo-clear"><?php echo $this->translate('pagemusic_Clear List') ?></a>
  </div>
  <div class="demo-status-overall" id="music-demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress overall-progress" alt="" />
  </div>
  <div class="demo-status-current" id="music-demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress current-progress" alt="" />
  </div>
  <div class="current-text"></div>
</div>

<ul id="music-demo-list"></ul>