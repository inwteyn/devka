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
var stickersUploadCount = 0;
var stickersUploaderSwf = '<?php echo $this->baseUrl() . '/externals/fancyupload/Swiff.Uploader.swf' ?>';
var stickers_up = {};
en4.core.runonce.add(function () {
    stickers_up = new FancyUpload2($('stickers-demo-status'), $('stickers-demo-list'), {
        verbose: false,
        appendCookieData: true,
        'url': '<?php echo $this->url(array('action' => 'upload-stickers'), 'heemoticon_admin_stickers') ?>?ul=1',
        path: stickersUploaderSwf,
        typeFilter: {
            'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
        },
        target: 'stickers-demo-browse',
        onLoad: function () {
            $('stickers-demo-status').removeClass('hide'); // we show the actual UI
            $('stickers-demo-fallback').destroy(); // ... and hide the plain form

            // We relay the interactions with the overlayed flash to the link
            this.target.addEvents({
                click: function () {
                    return false;
                },
                mouseenter: function () {
                    this.addClass('hover');
                },
                mouseleave: function () {
                    this.removeClass('hover');
                    this.blur();
                },
                mousedown: function () {
                    this.focus();
                }
            });

            // Interactions for the 2 other buttons
            if ($('stickers_submit-wrapper'))
                $('stickers_submit-wrapper').hide();
            $('stickers-demo-clear').addEvent('click', function () {
                stickers_up.remove(); // remove all files
                if ($('stickers_fancyuploadfileids'))
                    $('stickers_fancyuploadfileids').value = '';
                return false;
            });

        },

        /**
         * Is called when files were not added, "files" is an array of invalid File classes.
         *
         * This example creates a list of error elements directly in the file list, which
         * hide on click.
         */
        onSelectFail: function (files) {
            files.each(function (file) {
                new Element('li', {
                    'class': 'validation-error',
                    html: file.validationErrorMessage || file.validationError,
                    title: MooTools.lang.get('FancyUpload', 'removeTitle'),
                    events: {
                        click: function () {
                            this.destroy();
                        }
                    }
                }).inject(this.list, 'top');
            }, this);
        },

        onComplete: function hideProgress() {
            var demostatuscurrent = document.getElementById("stickers-demo-status-current");
            var demostatusoverall = document.getElementById("stickers-demo-status-overall");
            var demosubmit = document.getElementById("stickers_submit-wrapper");

            demostatuscurrent.style.display = "none";
            demostatusoverall.style.display = "none";

            if (demosubmit)
                demosubmit.style.display = "block";

            if (!$('cover').value) {
                $('uploaded-stickers-preview').getElements('.stickers-preview-img-cover')[0].click();
            }
        },

        onFileStart: function () {
            stickersUploadCount += 1;
        },
        onFileRemove: function (file) {
            stickersUploadCount -= 1;
            file_id = file.photo_id;
            request = new Request.JSON({
                'format': 'json',
                'url': '<?php echo $this->url(array('action' => 'remove-sticker'), 'heemoticon_admin_stickers') ?>?rp=1',
                'data': {
                    'format': 'json',
                    'photo_id': file_id
                },
                'onSuccess': function (responseJSON) {
                    return false;
                }
            });

            if (file_id) {
                request.send();
            }

            var fileids = $('stickers_fancyuploadfileids');

            if ($("stickers-demo-list").getChildren('li').length == 0) {
                var demolist = document.getElementById("stickers-demo-list");
                var demosubmit = document.getElementById("stickers_submit-wrapper");
                demolist.style.display = "none";
                if (demosubmit) {
                    demosubmit.style.display = "none";
                }
                if (demolist) {
                    demolist.style.display = "none";
                }
            }
            if (fileids)
                fileids.value = fileids.value.replace(file_id, "");
        },
        onSelectSuccess: function (file) {
            var demostatuscurrent = document.getElementById("stickers-demo-status-current");
            var demostatusoverall = document.getElementById("stickers-demo-status-overall");

            demostatuscurrent.style.display = "block";
            demostatusoverall.style.display = "block";
            stickers_up.start();
        },
        /**
         * This one was directly in FancyUpload2 before, the event makes it
         * easier for you, to add your own response handling (you probably want
         * to send something else than JSON or different items).
         */
        onFileSuccess: function (file, response) {
            var json = new Hash(JSON.decode(response, true) || {});
            if (json.get('status') == '1') {
                var preview_container = $('uploaded-stickers-preview');
                file.element.addClass('file-success');
                file.info.set('html', '<span>' + '<?php echo $this->string()->escapeJavascript($this->translate('Upload complete.')) ?>' + '</span>');
                file.photo_id = json.get('photo_id');

                sticker_preview_block = new Element('div', {
                    'id': 'sticker-block-' + json.get('photo_id'),
                    'class': 'uploaded-sticker-block'
                });
                sticker_preview_img = new Element('div', {
                    'class': 'stickers-preview-img'
                }).setStyle('background-image', 'url(' + json.get('url') + ')');
                sticker_preview_ico = new Element('i', {
                    'class': 'hei hei-times hei-lg stickers-preview-img-delete',
                    'onclick': 'delete_sticker_by_id(this) ',
                    'photo_id': json.get('photo_id'),
                    'file_name': file.name
                });
                sticker_preview_cover = new Element('i', {
                    'class': 'hei hei-check-circle hei-lg stickers-preview-img-cover',
                    'onclick': 'collection_cover_select(this) ',
                    'photo_id': json.get('photo_id')
                });
                sticker_preview_img.inject(sticker_preview_block);
                sticker_preview_ico.inject(sticker_preview_block);
                sticker_preview_cover.inject(sticker_preview_block);

                sticker_preview_block.inject(preview_container);
                var fileids = $('stickers_fancyuploadfileids');
                if (fileids) {
                    if (fileids.value.length)
                        fileids.value += ' ';
                    fileids.value += json.get('photo_id');
                }

                stickers_sortable();

            } else {
                file.element.addClass('file-failed');
                file.info.set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate('An error occurred:')) ?></span> ' + (json.get('error') ? (json.get('error')) : response));
            }
        },

        /**
         * onFail is called when the Flash movie got bashed by some browser plugin
         * like Adblock or Flashblock.
         */
        onFail: function (error) {
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

function delete_sticker_by_id(elem) {
    var sticker_id = elem.get('delete_id');
    if (!sticker_id)sticker_id = 0;
    var photo_id = elem.get('photo_id');
    $('sticker-block-' + photo_id).getElement('div').setStyle('background-image', 'url(application/modules/Heemoticon/externals/images/loading64.gif)');
    $('sticker-block-' + photo_id).getElement('div').setStyle('background-size', 'auto');
    request = new Request.JSON({
        'format': 'json',
        'url': '<?php echo $this->url(array('action' => 'remove-sticker'), 'heemoticon_admin_stickers') ?>?rp=1',
        'data': {
            'format': 'json',
            'photo_id': photo_id,
            'sticker_id': sticker_id
        },
        'onSuccess': function (responseJSON) {
            $('sticker-block-' + photo_id).remove();

            $$('.file-name').each(function (el) {
                if (el.get('html').trim() == elem.get('file_name')) {
                    el.getParent('li').getChildren('a')[0].click();
                }
            });

            if (elem.getNext().hasClass('collection_cover_select')) {
                if ($('uploaded-stickers-preview').getElements('.stickers-preview-img-cover')[0]) {
                    $('uploaded-stickers-preview').getElements('.stickers-preview-img-cover')[0].click();
                } else {
                    $('cover').value = '';
                }
            }
        }

    }).send();
}

function collection_cover_select(elem) {
    $('cover').value = elem.get('photo_id');
    $('uploaded-stickers-preview').getElements('.stickers-preview-img-cover').each(function (el) {
        el.removeClass('collection_cover_select');
    });
    elem.addClass('collection_cover_select');
}

function stickers_sortable() {
    new Sortables($('uploaded-stickers-preview'), {
        revert: {duration: 400, transition: 'cubic:out'},
        clone: true,
        onSort: function (element, clone) {
            element.setStyle('opacity', '0');
            clone.setStyle('z-index', '100');
          clone.setStyle('border', '1px solid rgba(0,0,0,0.3)');
          clone.setStyle('background-color', '#fff');
        },
        onComplete: function (element) {
            element.setStyle('opacity', '1');
        }
    });
}

</script>
<div class="form-wrapper">
    <div class="form-label">
        <label class="optional"
               for="uploaded-stickers-preview"><?php echo $this->translate('HE-Emoticon Sticker images'); ?></label>
    </div>

    <div class="form-element">
        <input type="hidden" name="file" id="stickers_fancyuploadfileids" value=""/>
        <fieldset id="stickers-demo-fallback">
            <label for="demo-stickerslabel">
                <input type="file" name="Filedata"/>
            </label>
        </fieldset>

        <div id="stickers-demo-status" class="hide">
            <div>
                <a class="buttonlink icon_offers_image_new" href="javascript:void(0);"
                   id="stickers-demo-browse"><?php echo $this->translate('HE-Emoticon Add Stickers') ?></a>
                <a class="buttonlink icon_clearlist" style="display: none;" href="javascript:void(0);"
                   id="stickers-demo-clear"><?php echo $this->translate('HE-Emoticon Clear list') ?></a>
            </div>
            <div class="demo-status-overall" id="stickers-demo-status-overall" style="display:none">
                <div class="overall-title"></div>
                <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
                     class="progress overall-progress" alt=""/>
            </div>
            <div class="demo-status-current" id="stickers-demo-status-current" style="display:none">
                <div class="current-title"></div>
                <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
                     class="progress current-progress" alt=""/>
            </div>
            <div class="current-text"></div>
        </div>

        <ul id="stickers-demo-list"></ul>

        <ul id="uploaded-stickers-preview"></ul>

    </div>
</div>