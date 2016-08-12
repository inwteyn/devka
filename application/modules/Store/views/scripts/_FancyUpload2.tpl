<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _FancyUpload.tpl 10250 2014-06-02 13:51:20Z lucas $
 * @author     Jung
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');
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
  en4.core.runonce.add(function () {

  });

</script>

<input type="hidden" name="<?php echo $this->name; ?>" id="fancyuploadfileids" value=""/>
<fieldset id="demo-fallback">
  <legend><?php echo $this->translate('File Upload'); ?></legend>
  <label for="demo-photoupload">
    <?php echo $this->translate('Upload a Video:'); ?>
    <input type="file" id="input1" name="Filedata" accept="video/*" onchange=""/>
  </label>
</fieldset>

<div id="demo-status" class="hide">
  <div>
    <?php echo $this->translate('Click "Add Video" to select a video from your computer. After you have selected video, click on Post Video at the bottom to begin uploading the file. Please wait while your video is being uploaded. When your upload is finished, your video will be processed - you will be notified when it is ready to be viewed.'); ?>
  </div>
  <div>
    <a class="buttonlink icon_video_new" href="javascript:void(0);"
       id="demo-browse"><?php echo $this->translate('Add Video'); ?></a>
  </div>
  <div class="demo-status-overall" id="demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
         class="progress overall-progress"/>
  </div>
  <div class="demo-status-current" id="demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
         class="progress current-progress"/>
  </div>
  <div class="current-text"></div>
</div>
<ul id="demo-list"></ul>

<div><br/>
  <a class="buttonlink" href="javascript://;" id="demo-upload"
     style='display:none; background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Video/externals/images/new.png);'><?php echo $this->translate('Post Video'); ?></a>
</div>
