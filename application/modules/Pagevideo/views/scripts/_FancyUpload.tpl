<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _FancyUpload.tpl 2010-09-20 17:53 idris $
 * @author     Idris
 */
?>

<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
?>

<script type="text/javascript">
var uploadCount2 = 0;
var up2;
en4.core.runonce.add(function(){
	// our uploader instance

	up2 = new FancyUpload2($('video-demo-status'), $('video-demo-list'), { // options object
		// we console.log infos, remove that in production!!
		verbose: false,
    multiple: false,
		appendCookieData: true,

                // url is read from the form, so you just have to change one place
		url: $('form-video-upload').action + '?ul=1&page_id=' + page_video.page_id,

		// path to the SWF file
		path: '<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.swf';?>',

		// remove that line to select all files, or edit it, add more items
                // 'Videos (*.4xm, *.IFF, *.MTV, *.RoQ, *.aac, *.ac3, *.aiff, *.alaw, *.amr, *.apc, *.ape, *.asf, *.asf_stream, *.au, *.avi, *.avs, *.bfi, *.c93, *.daud, *.dirac, *.dsicin, *.dts, *.dv, *.dv1394, *.dxa, *.ea, *.ea_cdata, *.eac3, *.f32be, *.f32le, *.f64be, *.f64le, *.ffm, *.film_cpk, *.flac, *.flic, *.flv, *.gsm, *.gxf, *.h261, *.h263, *.h264, *.idcin, *.image2, *.image2pipe, *.ingenient, *.ipmovie, *.lmlm4, *.m4v, *.matroska, *.mjpeg, *.mlp, *.mm, *.mmf, *.mov, *.mp4, *.m4a, *.3gp, *.3g2, *.mj2, *.mp3, *.mpc, *.mpc8, *.mpeg, *.mpegts, *.mpegtsraw, *.mpegvideo, *.msnwctcp, *.mulaw, *.mvi, *.mxf, *.nsv, *.nut, *.nuv, *.ogg, *.oma, *.oss, *.psxstr, *.pva, *.rawvideo, *.redir, *.rl2, *.rm, *.rpl, *.rtsp, *.s16be, *.s16le, *.s24be, *.s24le, *.s32be, *.s32le, *.s8, *.sdp, *.shn, *.siff, *.smk, *.sol, *.swf, *.thp, *.tiertexseq, *.tta, *.txd, *.u16be, *.u16le, *.u24be, *.u24le, *.u32be, *.u32le, *.u8, *.vc1, *.vc1test, *.video4linux, *.video4linux2, *.vmd, *.voc, *.wav, *.wc3movie, *.wsaud, *.wsvqa, *.wv, *.xa, *.yuv4mpegpipe)': '*.4xm; *.IFF; *.MTV; *.RoQ; *.aac; *.ac3; *.aiff; *.alaw; *.amr; *.apc; *.ape; *.asf; *.asf_stream; *.au; *.avi; *.avs; *.bfi; *.c93; *.daud; *.dirac; *.dsicin; *.dts; *.dv; *.dv1394; *.dxa; *.ea; *.ea_cdata; *.eac3; *.f32be; *.f32le; *.f64be; *.f64le; *.ffm; *.film_cpk; *.flac; *.flic; *.flv; *.gsm; *.gxf; *.h261; *.h263; *.h264; *.idcin; *.image2; *.image2pipe; *.ingenient; *.ipmovie; *.lmlm4; *.m4v; *.matroska; *.mjpeg; *.mlp; *.mm; *.mmf; *.mov; *.mp4; *.m4a; *.3gp; *.3g2; *.mj2; *.mp3; *.mpc; *.mpc8; *.mpeg; *.mpegts; *.mpegtsraw; *.mpegvideo; *.msnwctcp; *.mulaw; *.mvi; *.mxf; *.nsv; *.nut; *.nuv; *.ogg; *.oma; *.oss; *.psxstr; *.pva; *.rawvideo; *.redir; *.rl2; *.rm; *.rpl; *.rtsp; *.s16be; *.s16le; *.s24be; *.s24le; *.s32be; *.s32le; *.s8; *.sdp; *.shn; *.siff; *.smk; *.sol; *.swf; *.thp; *.tiertexseq; *.tta; *.txd; *.u16be; *.u16le; *.u24be; *.u24le; *.u32be; *.u32le; *.u8; *.vc1; *.vc1test; *.video4linux; *.video4linux2; *.vmd; *.voc; *.wav; *.wc3movie; *.wsaud; *.wsvqa; *.wv; *.xa; *.yuv4mpegpipe'

		typeFilter: {
			// 'Videos (*.flv, *.4xm, *.IFF, *.MTV, *.RoQ, *.aac, *.ac3, *.aiff, *.alaw, *.amr, *.apc, *.ape, *.asf, *.asf_stream, *.au, *.avi, *.avs, *.bfi, *.c93, *.daud, *.dirac, *.dsicin, *.dts, *.dv, *.dv1394, *.dxa, *.ea, *.ea_cdata, *.eac3, *.f32be, *.f32le, *.f64be, *.f64le, *.ffm, *.film_cpk, *.flac, *.flic, *.flv, *.gsm, *.gxf, *.h261, *.h263, *.h264, *.idcin, *.image2, *.image2pipe, *.ingenient, *.ipmovie, *.lmlm4, *.m4v, *.matroska, *.mjpeg, *.mlp, *.mm, *.mmf, *.mov, *.mp4, *.m4a, *.3gp, *.3g2, *.mj2, *.mp3, *.mpc, *.mpc8, *.mpeg, *.mpegts, *.mpegtsraw, *.mpegvideo, *.msnwctcp, *.mulaw, *.mvi, *.mxf, *.nsv, *.nut, *.nuv, *.ogg, *.oma, *.oss, *.psxstr, *.pva, *.rawvideo, *.redir, *.rl2, *.rm, *.rpl, *.rtsp, *.s16be, *.s16le, *.s24be, *.s24le, *.s32be, *.s32le, *.s8, *.sdp, *.shn, *.siff, *.smk, *.sol, *.swf, *.thp, *.tiertexseq, *.tta, *.txd, *.u16be, *.u16le, *.u24be, *.u24le, *.u32be, *.u32le, *.u8, *.vc1, *.vc1test, *.video4linux, *.video4linux2, *.vmd, *.voc, *.wav, *.wc3movie, *.wsaud, *.wsvqa, *.wv, *.xa, *.yuv4mpegpipe)': '*.4xm; *.IFF; *.MTV; *.RoQ; *.aac; *.ac3; *.aiff; *.alaw; *.amr; *.apc; *.ape; *.asf; *.asf_stream; *.au; *.avi; *.avs; *.bfi; *.c93; *.daud; *.dirac; *.dsicin; *.dts; *.dv; *.dv1394; *.dxa; *.ea; *.ea_cdata; *.eac3; *.f32be; *.f32le; *.f64be; *.f64le; *.ffm; *.film_cpk; *.flac; *.flic; *.flv; *.gsm; *.gxf; *.h261; *.h263; *.h264; *.idcin; *.image2; *.image2pipe; *.ingenient; *.ipmovie; *.lmlm4; *.m4v; *.matroska; *.mjpeg; *.mlp; *.mm; *.mmf; *.mov; *.mp4; *.m4a; *.3gp; *.3g2; *.mj2; *.mp3; *.mpc; *.mpc8; *.mpeg; *.mpegts; *.mpegtsraw; *.mpegvideo; *.msnwctcp; *.mulaw; *.mvi; *.mxf; *.nsv; *.nut; *.nuv; *.ogg; *.oma; *.oss; *.psxstr; *.pva; *.rawvideo; *.redir; *.rl2; *.rm; *.rpl; *.rtsp; *.s16be; *.s16le; *.s24be; *.s24le; *.s32be; *.s32le; *.s8; *.sdp; *.shn; *.siff; *.smk; *.sol; *.swf; *.thp; *.tiertexseq; *.tta; *.txd; *.u16be; *.u16le; *.u24be; *.u24le; *.u32be; *.u32le; *.u8; *.vc1; *.vc1test; *.video4linux; *.video4linux2; *.vmd; *.voc; *.wav; *.wc3movie; *.wsaud; *.wsvqa; *.wv; *.xa; *.flv, *.yuv4mpegpipe'
		},

		// this is our browse button, *target* is overlayed with the Flash movie
		target: 'video-demo-browse',

		// graceful degradation, onLoad is only called if all went well with Flash
		onLoad: function() {
			$('video-demo-status').removeClass('hide'); // we show the actual UI
			$('video-demo-fallback').destroy(); // ... and hide the plain form

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

		},

		// Edit the following lines, it is your custom event handling

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
		  var demostatuscurrent = document.getElementById("video-demo-status-current");
		  var demostatusoverall = document.getElementById("video-demo-status-overall");
		  var demosubmit = document.getElementById("video_upload-wrapper");
      
		  demostatusoverall.style.display = "none";
		},

		onFileStart: function() {
		  uploadCount2 += 1;
		},
		onFileRemove: function(file) {
		  uploadCount2 -= 1;
		  file_id = file.photo_id;
      var fileids = document.getElementById('video_fancyuploadfileids');

      var demobrowse = document.getElementById("video-demo-browse");
      var demoupload = document.getElementById("video-demo-upload");
      var demolist = document.getElementById("video-demo-list");
      var demosubmit = document.getElementById("video_upload-wrapper");

      demolist.style.display = "none";
      demosubmit.style.display = "none";
      demobrowse.style.display = "block"
      demoupload.style.display = "none";

		  var demostatusoverall = document.getElementById("video-demo-status-overall");
		  demostatusoverall.style.display = "none";
		  fileids.value = fileids.value.replace(file_id, "");
		},
		onSelectSuccess: function(file){
      $('video-demo-list').style.display = 'block';
		  var demoupload = document.getElementById("video-demo-upload");
		  var demobrowse = document.getElementById("video-demo-browse");
      var demostatuscurrent = document.getElementById("video-demo-status-current");
		  var demostatusoverall = document.getElementById("video-demo-status-overall");
      demoupload.style.display = "inline";
      demobrowse.style.display = "none";
		  demostatusoverall.style.display = "block";
		},
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
				file.info.set('html', '<span>Upload complete.</span>');
        $('video_code').value=json.get('code');
        $('video_id').value=json.get('video_id');
        $('video_upload-wrapper').setStyle('display', 'block');
			} else {
				file.element.addClass('file-failed');
				file.info.set('html', '<br/><b>Upload has failed: </b> The video you tried to upload exceeds the maximum file size. <br/>' + (json.get('error') ? (json.get('error')) : response));
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
			}
		}

	});

});

function startUploadVideo(){
  $('video_type-wrapper').style.display = "none";
  $('video-demo-upload').style.display = "none";
  up2.start();
}
</script>

<input type="hidden" name="<?php echo $this->name;?>" id="video_fancyuploadfileids" value ="" />
<fieldset id="video-demo-fallback">
  <legend><?php echo $this->translate('File Upload');?></legend>
  <p>
    <?php echo $this->translate('PAGEVIDEO_UPLOAD_DESCRIPTION');?>
  </p>
  <label for="demo-photoupload">
    <?php echo $this->translate('Upload a Video:');?>
    <input type="file" name="Filedata" />
  </label>
</fieldset>

<div id="video-demo-status" class="hide">
  <div>
    <?php echo $this->translate('PAGEVIDEO_UPLOAD_DESCRIPTION');?>
  </div>
  <div>
    <a class="buttonlink icon_video_new" href="javascript:void(0);" id="video-demo-browse"><?php echo $this->translate('Add Video');?></a>
  </div>
  <div class="video-demo-status-overall" id="video-demo-status-overall" style="display:none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress overall-progress" />
  </div>
  <div class="video-demo-status-current" id="video-demo-status-current" style="display:none">
    <div class="current-title"></div>
    <img src="<?php echo $this->baseUrl() . '/externals/fancyupload/assets/progress-bar/bar.gif';?>" class="progress current-progress" />
  </div>
  <div class="current-text"></div>
</div>
<ul id="video-demo-list"></ul>

<div><br/>
  <a class="buttonlink" href="javascript:startUploadVideo();" id="video-demo-upload" style='display:none; background-image: url(application/modules/Pagevideo/externals/images/new.png);'><?php echo $this->translate('Post Video');?></a>
</div>
