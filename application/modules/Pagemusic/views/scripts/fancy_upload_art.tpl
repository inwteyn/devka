<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: fancy_upload_art.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">
  var music_artUploaderSwf = '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf' ?>';
  var music_artUploadCount = 0;
  var music_art_up = {};
  en4.core.runonce.add(function(){
    music_art_up = new FancyUpload2($('music_art-demo-status'), $('music_art-demo-list'), { // options object
      // we console.log infos, remove that in production!!
      verbose: false,
      multiple: false,
      appendCookieData: true,

      // url is read from the form, so you just have to change one place
      url: $('form-upload-music').action + '?ua=1&user_id='+page_music.user_id+'&playlist_id='+page_music.playlist_id,

      // path to the SWF file
      path: music_artUploaderSwf,

      // remove that line to select all files, or edit it, add more items
      typeFilter: {
        'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
      },

      // this is our browse button, *target* is overlayed with the Flash movie
      target: 'music_art-demo-browse',

      // graceful degradation, onLoad is only called if all went well with Flash
      onLoad: function() {
        $('music_art-demo-status').removeClass('hide'); // we show the actual UI
        $('music_art-demo-fallback').destroy(); // ... and hide the plain form

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
        if ($('music_art_submit-wrapper'))
          $('music_art_submit-wrapper').hide();
        $('music_art-demo-clear').addEvent('click', function() {
          music_art_up.remove(); // remove all files
          if ($('music_art_fileid'))
            $('music_art_fileid').value = '';
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

      onComplete: function hideProgress(){
        var demostatuscurrent = document.getElementById("music_art-demo-status-current");
        var demostatusoverall = document.getElementById("music_art-demo-status-overall");
        var demosubmit = document.getElementById("music_art_submit-wrapper");
        var democlear = document.getElementById("music_art-demo-clear");
        var demolist = document.getElementById("music_art-demo-list");

        demostatuscurrent.style.display = "none";
        demostatusoverall.style.display = "none";
        
        if (democlear)
          democlear.style.display = "inline";

        if (demolist)
          demolist.style.display = "block";

        if (demosubmit)
          demosubmit.style.display = "block";
      },

      onFileStart: function(){
        music_artUploadCount += 1;
      },
      
      onFileRemove: function(file){
        music_artUploadCount -= 1;
        var file_id = file.song_id;
        request = new Request.JSON({
          'format' : 'json',
          'url' : '<?php echo $this->url(array('action' => 'remove-art'), 'page_music') ?>',
          'data': {
            'format': 'json',
            'photo_id' : file_id
          },
          'onSuccess' : function(responseJSON){
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

        var democlear  = document.getElementById("music_art-demo-clear");
        democlear.style.display  = "none";
        
        var demolist   = document.getElementById("music_art-demo-list");
        demolist.style.display   = "none";

        $('music_art-demo-status').setStyle('display', 'block');

        var fileids = $('music_art_fileid');

        if (fileids)
          fileids.value = '';
      },
      
      onSelectSuccess: function(file){
        var democlear = document.getElementById("music_art-demo-clear");
        var demostatuscurrent = document.getElementById("music_art-demo-status-current");
        var demostatusoverall = document.getElementById("music_art-demo-status-overall");
        var demolist = document.getElementById("music_art-demo-list");
        //$('music_art-demo-status').setStyle('display', 'none');

        demolist.style.display   = "block";
        democlear.style.display = "inline";
        demostatuscurrent.style.display = "block";
        demostatusoverall.style.display = "block";

        music_art_up.start();
      },
      /**
       * This one was directly in FancyUpload2 before, the event makes it
       * easier for you, to add your own response handling (you probably want
       * to send something else than JSON or different items).
       */
      onFileSuccess: function(file, response) {
        $('music_art-demo-status').setStyle('display', 'none');
        var json = new Hash(JSON.decode(response, true) || {});
        
        if (json.get('status') == '1') {
          file.element.addClass('file-success');
          // file.info.set('html', '<span>' + '<?php echo $this->string()->escapeJavascript($this->translate('Upload complete.')) ?>' + '</span>');
          file.info.set('html', '<img src="' + en4.core.baseUrl + json.photo.storage_path  + '" />');
          file.song_id   = json.get('photo_id');
          var fileids = $('music_art_fileid');
          if (fileids) {
            fileids.value = json.get('photo_id');
          }
          var demolist = document.getElementById("music_art-demo-list");
          demolist.style.display   = "block";
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

<input type="hidden" name="music_art_fileid" id="music_art_fileid" value ="" />
<fieldset id="music_art-demo-fallback">
  <legend><?php echo $this->translate("pagemusic_File Upload") ?></legend>
  <p>
    <?php echo $this->translate('PAGEMUSIC_UPLOAD_ARTWORK_DESCRIPTION') ?>
  </p>
  <label for="demo-music_artlabel">
    <?php echo $this->translate('pagemusic_Upload Playlist Artwork:') ?>
    <input id="<?php echo $this->element->getName(); ?>" type="file" name="<?php echo $this->element->getName() ?>" value="<?php echo $this->element->getValue(); ?>" />
  </label>
</fieldset>

<div id="music_art-demo-status" class="hide">
  <div>
    <?php echo $this->translate('_PAGEMUSIC_UPLOAD_ARTWORK_DESCRIPTION') ?>
  </div>
  <div>
    <a class="buttonlink icon_music_art_new" href="javascript:void(0);" id="music_art-demo-browse"><?php echo $this->translate('pagemusic_Select Playlist Artwork') ?></a>
    <a class="buttonlink icon_clearlist" style="display: none;" href="javascript:void(0);" id="music_art-demo-clear"><?php echo $this->translate('pagemusic_Clear List') ?></a>
  </div>
  <div class="demo-status-overall" id="music_art-demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress overall-progress" alt="" />
  </div>
  <div class="demo-status-current" id="music_art-demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress current-progress" alt="" />
  </div>
  <div class="current-text"></div>
</div>

<ul id="music_art-demo-list"></ul>