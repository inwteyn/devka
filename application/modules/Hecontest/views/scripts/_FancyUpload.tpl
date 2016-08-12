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

<input type="hidden" name="file" id="fancyuploadfileids" value=""/>
<fieldset id="demo-fallback-hecontest">
    <legend><?php echo $this->translate('File Upload') ?></legend>
    <p>
        <?php echo $this->translate('Click "Browse..." to select the file you would like to upload.') ?>
    </p>
    <label for="demo-photoupload">
        <?php echo $this->translate('Upload a Photo:') ?>
        <input type="file" name="Filedata"/>
    </label>
</fieldset>
<div id="demo-position" >
    <div id="demo-status-hecontest" class="">
        <div>
            <!--      --><?php //echo $this->translate('hecontest_Select product photos') ?>
        </div>
        <div id="a-wrapper" style="position: relative;">
            <a class="buttonlink icon_photos_new" href="javascript:void(0);"
               id="demo-browse-hecontest"><?php echo $this->translate('HECONTEST_upload_photo') ?></a>
        </div>
        <div class="demo-status-hecontest-overall" id="demo-status-hecontest-overall" style="display:none">
            <div class="overall-title"></div>
            <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
                 class="progress overall-progress"/>
        </div>
        <div class="demo-status-hecontest-current" id="demo-status-hecontest-current" style="display:none">
            <div class="current-title"></div>
            <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
                 class="progress current-progress"/>
        </div>
        <div class="current-text"></div>
    </div>
    <ul id="demo-list-hecontest"></ul>
</div>