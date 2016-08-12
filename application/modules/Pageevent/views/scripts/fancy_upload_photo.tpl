
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: fancy_upload_photo.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<script type="text/javascript">

  var event_photoUploaderSwf = '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf' ?>';

  var event_photoUploadCount = 0;
  var event_photo_up = {};

  en4.core.runonce.add(function()
  {
    event_photo_up = new FancyUpload2($('event_photo-demo-status'), $('event_photo-demo-list'), {

      verbose: false,
      multiple: false,
      appendCookieData: true,

      url: '<?php echo $this->url(array('action' => 'upload-photo', 'format'  => 'json'), 'page_event')?>',

      path: event_photoUploaderSwf,

      typeFilter: {
        'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
      },

      target: 'event_photo-demo-browse',

      onLoad: function() {
        $('event_photo-demo-status').removeClass('hide');
        $('event_photo-demo-fallback').destroy();

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

        if ($('event_photo_submit-wrapper'))
          $('event_photo_submit-wrapper').hide();
        $('event_photo-demo-clear').addEvent('click', function() {
          event_photo_up.remove(); // remove all files
          if ($('event_photo_fileid'))
            $('event_photo_fileid').value = '';
          return false;
        });

      },

      onSelectFail: function(files)
      {
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
        var demostatuscurrent = document.getElementById("event_photo-demo-status-current");
        var demostatusoverall = document.getElementById("event_photo-demo-status-overall");
        var demosubmit = document.getElementById("event_photo_submit-wrapper");
        var democlear = document.getElementById("event_photo-demo-clear");
        var demolist = document.getElementById("event_photo-demo-list");

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
        event_photoUploadCount += 1;
      },
      
      onFileRemove: function(file){
        event_photoUploadCount -= 1;
        var file_id = file.song_id;
        request = new Request.JSON({
          'format' : 'json',
          'url' : '<?php echo $this->url(array('action' => 'remove-photo', 'format'  => 'json'), 'page_event') ?>',
          'data': {
            'format': 'json',
            'photo_id' : file_id
          },
          'onSuccess' : function(responseJSON){
/*						$$($('pageevent-form-upload-photo').elements).each(function($element){
							$element.disabled = false;
						});*/
            return false;
          }
        });
        var $photo_id = $('event_photo_fileid');
        if (file_id && ($photo_id && $photo_id.value != 0)) {
/*					$$($('pageevent-form-upload-photo').elements).each(function($element){
						$element.disabled = true;
					});*/
          $photo_id.value = 0;
					request.send();
				}

        var democlear  = document.getElementById("event_photo-demo-clear");
        democlear.style.display  = "none";
        
        var demolist   = document.getElementById("event_photo-demo-list");
        demolist.style.display   = "none";

        $('event_photo-demo-status').setStyle('display', 'block');

        var fileids = $('event_photo_fileid');

        if (fileids)
          fileids.value = '';
      },
      
      onSelectSuccess: function(file){
        var democlear = document.getElementById("event_photo-demo-clear");
        var demostatuscurrent = document.getElementById("event_photo-demo-status-current");
        var demostatusoverall = document.getElementById("event_photo-demo-status-overall");
        var demolist = document.getElementById("event_photo-demo-list");
        
        $('event_photo-demo-status').setStyle('display', 'none');

        demolist.style.display   = "block";
        democlear.style.display = "inline";
        demostatuscurrent.style.display = "block";
        demostatusoverall.style.display = "block";
        
        event_photo_up.start();
      },

      onFileSuccess: function(file, response) {
        var json = new Hash(JSON.decode(response, true) || {});

        if (json.get('status') == '1') {
          file.element.addClass('file-success');
          // file.info.set('html', '<span>' + '<?php echo $this->string()->escapeJavascript($this->translate('Upload complete.')) ?>' + '</span>');
          file.info.set('html', '<img src="' + en4.core.baseUrl + json.photo.storage_path  + '" />');
          file.song_id   = json.get('photo_id');
          var fileids = $('event_photo_fileid');
          if (fileids) {
            fileids.value = json.get('photo_id');
          }
          var demolist = document.getElementById("event_photo-demo-list");
          demolist.style.display   = "block";
        } else {
          file.element.addClass('file-failed');
          file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate('An error occurred:')) ?></span> ' + (json.get('error') ? (json.get('error')) : response));
        }
      },

      onFail: function(error) {
      }
    });
  });
</script>

<input type="hidden" name="event_photo_fileid" id="event_photo_fileid" value="" />
<fieldset id="event_photo-demo-fallback"></fieldset>

<div id="event_photo-demo-status" class="hide">
  <div>
    <a class="buttonlink icon_pageevent_photo_new" href="javascript:void(0);" id="event_photo-demo-browse"><?php echo $this->translate('PAGEEVENT_FORM_PHOTO_TITLE') ?></a>
    <a class="buttonlink icon_clearlist hidden" style="display: none;" href="javascript:void(0);" id="event_photo-demo-clear"></a>
  </div>
  <p style="padding-top:5px; padding-bottom:5px;"><?php echo $this->translate('PAGEEVENT_FORM_PHOTO_DESCRIPTION') ?></p>
  <div class="demo-status-overall" id="event_photo-demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress overall-progress" alt="" />
  </div>
  <div class="demo-status-current" id="event_photo-demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>" class="progress current-progress" alt="" />
  </div>
  <div class="current-text"></div>
</div>

<ul id="event_photo-demo-list"></ul>