<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: fancy_upload_document_2.tpl 2011-09-01 13:17:53 kirill $
 * @author     Kirill
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
var upCreate = {};

function returnFancyUploadCreate() {
	  return new FancyUpload2($('demo-status-pagedocument'), $('demo-list-pagedocument'), {
		    verbose: false,
        multiple: false,
		    appendCookieData: true,
        url: '<?php echo $this->url(array('action' => 'upload-document', 'format'  => 'json'), 'page_document')?>',
		    path: '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf'; ?>',
		    
        typeFilter: {
        'Documents (*.pdf, *.txt, *.ps, *.rtf, *.epub, *.odt, *.odp, *.ods, *.odg, *.sxw, *.sxc, *.sxi, *.sxd, *.doc, *.ppt, *.pps, *.xls, *.docx, *.pptx, *.ppsx, *.xlsx, *.tif, *.tiff)':
        '*.pdf; *.txt; *.ps; *.rtf; *.epub; *.odt; *.odp; *.ods; *.odg; *.sxw; *.sxc; *.sxi; *.sxd; *.doc; *.ppt; *.pps; *.xls; *.docx; *.pptx; *.ppsx; *.xlsx; *.tif; *.tiff)'
      },
		    target: 'demo-browse-pagedocument',
		    data: extraData,
           
		    onLoad: function()
        {
		      $('demo-status-pagedocument').removeClass('hide');
          if ($('demo-fallback-pagedocument') != undefined){
		        $('demo-fallback-pagedocument').destroy();
          }
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
		      var demostatuscurrent = document.getElementById("demo-status-pagedocument-current");
		      var demostatusoverall = document.getElementById("demo-status-pagedocument-overall");
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
		         'url' : '<?php echo $this->url(array('action' => 'remove-document', 'format'  => 'json'), 'page_document') ?>',
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
		        var demolist = document.getElementById("demo-list-pagedocument");
		        var demosubmit = document.getElementById("submit-wrapper");
		        demolist.style.display = "none";
		        demosubmit.style.display = "none";
		      }
          if($('demo-browse-pagedocument'))
              $('demo-browse-pagedocument').show();
          var file_size = document.getElementById('file_size');
          var file_path = document.getElementById('file_path');
          var file_id = document.getElementById('file_id');
          file_size.value = '';
          file_path.value = '';
          file_id.value = '';
		      fileids.value = fileids.value.replace(file_id, "");
		    },

		    onSelectSuccess: function(file) {
		                  $('demo-list-pagedocument').style.display = 'block';
		      var demostatuscurrent = document.getElementById("demo-status-pagedocument-current");
		      var demostatusoverall = document.getElementById("demo-status-pagedocument-overall");
		      
		      demostatuscurrent.style.display = "block";
		      demostatusoverall.style.display = "block";
		      upCreate.start();
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

            var file_size = document.getElementById('file_size');
            var file_path = document.getElementById('file_path');
            var file_id = document.getElementById('file_id');
            file_size.value = json.get('file_size');
            file_path.value = json.get('file_path');
            file_id.value = json.get('file_id');
		        fileids.value = fileids.value + json.get('photo_id') + " ";
		        file.photo_id = json.get('photo_id');
		        if($('demo-browse-pagedocument'))
              $('demo-browse-pagedocument').hide();
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
}

en4.core.runonce.add(function(){
  try{
    upCreate = returnFancyUploadCreate();
  }catch(e){
  }
});
</script>

<input type="hidden" name="file" id="fancyuploadfileids" value ="" />
<fieldset id="demo-fallback-pagedocument">
  <legend><?php echo $this->translate('File Upload') ?></legend>
  <p>
    <?php echo $this->translate('Click "Browse..." to select the file you would like to upload.') ?>
  </p>
  <label for="demo-photoupload">
    <?php echo $this->translate('Upload a Photo:') ?>
    <input type="file" name="Filedata" />
  </label>
</fieldset>
<div id="demo-position">
  <div id="demo-status-pagedocument" class="">
    <div>
<!--      --><?php //echo $this->translate('pagedocument_Select product photos') ?>
    </div>
    <div>
      <a class="buttonlink icon_photos_new" href="javascript:void(0);" id="demo-browse-pagedocument"><?php echo $this->translate('pagedocument_Upload') ?></a>
    </div>
    <div class="demo-status-pagedocument-overall" id="demo-status-pagedocument-overall" style="display:none">
      <div class="overall-title"></div>
      <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress overall-progress" />
    </div>
    <div class="demo-status-pagedocument-current" id="demo-status-pagedocument-current" style="display:none">
      <div class="current-title"></div>
      <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress current-progress" />
    </div>
    <div class="current-text"></div>
  </div>
  <ul id="demo-list-pagedocument"></ul>
</div>