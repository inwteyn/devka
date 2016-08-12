<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit-video.tpl 2011-09-07 16:37:11 taalay $
 * @author     Taalay
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');


if ($this->video && $this->video->type == 3 && $this->video_extension == 'mp4') {
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');
}
?>

<?php if ($this->video && $this->video->type == 3 && $this->video_extension == 'flv'):

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $coreItem = $modulesTbl->getModule('core')->toArray();
    if(version_compare($coreItem['version'], '4.8.10')>=0){
        $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js');

    }else{
        $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
    }

  ?>
  <script type='text/javascript'>
    en4.core.runonce.add(function () {
      flashembed("videoFrame", {
        src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.2.18.swf",
        width: 480,
        height: 386,
        wmode: 'transparent'
      }, {
        config: {
          clip: {
            url: "<?php echo $this->video_location;?>",
            autoPlay: false,
            duration: "<?php echo $this->video->duration ?>",
            autoBuffering: true
          },
          plugins: {
            controls: {
              background: '#000000',
              bufferColor: '#333333',
              progressColor: '#444444',
              buttonColor: '#444444',
              buttonOverColor: '#666666'
            }
          },
          canvas: {
            backgroundColor: '#000000'
          }
        }
      });
    });

  </script>
<?php endif ?>

<script type="text/javascript">
function showLoader() {
  $('video-loader').setStyle('display', 'block');
}
function hideLoader() {
  $('video-loader').setStyle('display', 'none');
}
var videoExist = false;
var videoType = 0;
en4.core.runonce.add(function () {
  videoExist = false;
  videoType = 0;
  <?php if($this->video) : ?>
    videoExist = true;
    videoType = <?php echo $this->video->type; ?>;
  <?php endif; ?>

  updateVideoFields();

  up = new FancyUpload2($('demo-status'), $('demo-list'), { // options object
    verbose: false,
    multiple: false,
    appendCookieData: true,
    timeLimit: 0,

    policyFile: '<?php echo (_ENGINE_SSL ? 'https://' : 'http://')
          . $_SERVER['HTTP_HOST'] . $this->url(array(
            'controller' => 'cross-domain'),
            'default', true) ?>',

    url: "<?php echo $this->url(array('module'=>'store', 'controller'=>'videos', 'action'=>'edit-video'), 'admin_default', 1) . '?ul=1'; ?>",

    path: '<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.swf'; ?>',
    data: {
      product_id: '<?php echo $this->product->getIdentity(); ?>',
      type: 3,
      format: 'json'
    },
    // remove that line to select all files, or edit it, add more items
    // 'Videos (*.4xm, *.IFF, *.MTV, *.RoQ, *.aac, *.ac3, *.aiff, *.alaw, *.amr, *.apc, *.ape, *.asf, *.asf_stream, *.au, *.avi, *.avs, *.bfi, *.c93, *.daud, *.dirac, *.dsicin, *.dts, *.dv, *.dv1394, *.dxa, *.ea, *.ea_cdata, *.eac3, *.f32be, *.f32le, *.f64be, *.f64le, *.ffm, *.film_cpk, *.flac, *.flic, *.flv, *.gsm, *.gxf, *.h261, *.h263, *.h264, *.idcin, *.image2, *.image2pipe, *.ingenient, *.ipmovie, *.lmlm4, *.m4v, *.matroska, *.mjpeg, *.mlp, *.mm, *.mmf, *.mov, *.mp4, *.m4a, *.3gp, *.3g2, *.mj2, *.mp3, *.mpc, *.mpc8, *.mpeg, *.mpegts, *.mpegtsraw, *.mpegvideo, *.msnwctcp, *.mulaw, *.mvi, *.mxf, *.nsv, *.nut, *.nuv, *.ogg, *.oma, *.oss, *.psxstr, *.pva, *.rawvideo, *.redir, *.rl2, *.rm, *.rpl, *.rtsp, *.s16be, *.s16le, *.s24be, *.s24le, *.s32be, *.s32le, *.s8, *.sdp, *.shn, *.siff, *.smk, *.sol, *.swf, *.thp, *.tiertexseq, *.tta, *.txd, *.u16be, *.u16le, *.u24be, *.u24le, *.u32be, *.u32le, *.u8, *.vc1, *.vc1test, *.video4linux, *.video4linux2, *.vmd, *.voc, *.wav, *.wc3movie, *.wsaud, *.wsvqa, *.wv, *.xa, *.yuv4mpegpipe)': '*.4xm; *.IFF; *.MTV; *.RoQ; *.aac; *.ac3; *.aiff; *.alaw; *.amr; *.apc; *.ape; *.asf; *.asf_stream; *.au; *.avi; *.avs; *.bfi; *.c93; *.daud; *.dirac; *.dsicin; *.dts; *.dv; *.dv1394; *.dxa; *.ea; *.ea_cdata; *.eac3; *.f32be; *.f32le; *.f64be; *.f64le; *.ffm; *.film_cpk; *.flac; *.flic; *.flv; *.gsm; *.gxf; *.h261; *.h263; *.h264; *.idcin; *.image2; *.image2pipe; *.ingenient; *.ipmovie; *.lmlm4; *.m4v; *.matroska; *.mjpeg; *.mlp; *.mm; *.mmf; *.mov; *.mp4; *.m4a; *.3gp; *.3g2; *.mj2; *.mp3; *.mpc; *.mpc8; *.mpeg; *.mpegts; *.mpegtsraw; *.mpegvideo; *.msnwctcp; *.mulaw; *.mvi; *.mxf; *.nsv; *.nut; *.nuv; *.ogg; *.oma; *.oss; *.psxstr; *.pva; *.rawvideo; *.redir; *.rl2; *.rm; *.rpl; *.rtsp; *.s16be; *.s16le; *.s24be; *.s24le; *.s32be; *.s32le; *.s8; *.sdp; *.shn; *.siff; *.smk; *.sol; *.swf; *.thp; *.tiertexseq; *.tta; *.txd; *.u16be; *.u16le; *.u24be; *.u24le; *.u32be; *.u32le; *.u8; *.vc1; *.vc1test; *.video4linux; *.video4linux2; *.vmd; *.voc; *.wav; *.wc3movie; *.wsaud; *.wsvqa; *.wv; *.xa; *.yuv4mpegpipe'

    typeFilter: {
      //'Videos (*.wmv, *.4xm, *.IFF, *.MTV, *.RoQ, *.aac, *.ac3, *.aiff, *.alaw, *.amr, *.apc, *.ape, *.asf, *.asf_stream, *.au, *.avi, *.avs, *.bfi, *.c93, *.daud, *.dirac, *.dsicin, *.dts, *.dv, *.dv1394, *.dxa, *.ea, *.ea_cdata, *.eac3, *.f32be, *.f32le, *.f64be, *.f64le, *.ffm, *.film_cpk, *.flac, *.flic, *.flv, *.gsm, *.gxf, *.h261, *.h263, *.h264, *.idcin, *.image2, *.image2pipe, *.ingenient, *.ipmovie, *.lmlm4, *.m4v, *.matroska, *.mjpeg, *.mlp, *.mm, *.mmf, *.mov, *.mp4, *.m4a, *.3gp, *.3g2, *.mj2, *.mp3, *.mpc, *.mpc8, *.mpeg, *.mpegts, *.mpegtsraw, *.mpegvideo, *.msnwctcp, *.mulaw, *.mvi, *.mxf, *.nsv, *.nut, *.nuv, *.ogg, *.oma, *.oss, *.psxstr, *.pva, *.rawvideo, *.redir, *.rl2, *.rm, *.rpl, *.rtsp, *.s16be, *.s16le, *.s24be, *.s24le, *.s32be, *.s32le, *.s8, *.sdp, *.shn, *.siff, *.smk, *.sol, *.swf, *.thp, *.tiertexseq, *.tta, *.txd, *.u16be, *.u16le, *.u24be, *.u24le, *.u32be, *.u32le, *.u8, *.vc1, *.vc1test, *.video4linux, *.video4linux2, *.vmd, *.voc, *.wav, *.wc3movie, *.wsaud, *.wsvqa, *.wv, *.xa, *.yuv4mpegpipe)': '*.4xm; *.IFF; *.MTV; *.RoQ; *.aac; *.ac3; *.aiff; *.alaw; *.amr; *.apc; *.ape; *.asf; *.asf_stream; *.au; *.avi; *.avs; *.bfi; *.c93; *.daud; *.dirac; *.dsicin; *.dts; *.dv; *.dv1394; *.dxa; *.ea; *.ea_cdata; *.eac3; *.f32be; *.f32le; *.f64be; *.f64le; *.ffm; *.film_cpk; *.flac; *.flic; *.flv; *.gsm; *.gxf; *.h261; *.h263; *.h264; *.idcin; *.image2; *.image2pipe; *.ingenient; *.ipmovie; *.lmlm4; *.m4v; *.matroska; *.mjpeg; *.mlp; *.mm; *.mmf; *.mov; *.mp4; *.m4a; *.3gp; *.3g2; *.mj2; *.mp3; *.mpc; *.mpc8; *.mpeg; *.mpegts; *.mpegtsraw; *.mpegvideo; *.msnwctcp; *.mulaw; *.mvi; *.mxf; *.nsv; *.nut; *.nuv; *.ogg; *.oma; *.oss; *.psxstr; *.pva; *.rawvideo; *.redir; *.rl2; *.rm; *.rpl; *.rtsp; *.s16be; *.s16le; *.s24be; *.s24le; *.s32be; *.s32le; *.s8; *.sdp; *.shn; *.siff; *.smk; *.sol; *.swf; *.thp; *.tiertexseq; *.tta; *.txd; *.u16be; *.u16le; *.u24be; *.u24le; *.u32be; *.u32le; *.u8; *.vc1; *.vc1test; *.video4linux; *.video4linux2; *.vmd; *.voc; *.wav; *.wc3movie; *.wsaud; *.wsvqa; *.wv; *.xa; *.yuv4mpegpipe'
    },

    target: 'demo-browse',

    onLoad: function () {
      $('demo-status').removeClass('hide'); // we show the actual UI
      $('demo-fallback').destroy(); // ... and hide the plain form

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
    },
    onSelectFail: function (files) {
    },
    onComplete: function hideProgress() {
    },
    onFileStart: function () {
      //uploadCount += 1;
    },
    onFileRemove: function (file) {
    },
    onSelectSuccess: function (file) {
      $('video-loader').setStyle('display', 'block');
      up.start();
    },
    onFileSuccess: function (file, response) {
      $('video-loader').setStyle('display', 'none');
      var json = new Hash(JSON.decode(response, true) || {});
      if (json && json.status) {
        $('store-video-preview').set('html', json.preview);
        /*$('remove').setStyle('display', 'block');
        $('upload').setStyle('display', 'block');
        $('file-wrapper').setStyle('display', 'none');*/
        videoExist = true;

        updateVideoFields();
      }
    }
  });

  $('type-1').addEvent('click', function () {
    if(videoExist) return;
    $('url-wrapper').setStyle('display', 'block');
    $('file-wrapper').setStyle('display', 'none');
  });
  $('type-3').addEvent('click', function () {
    if(videoExist) return;
    $('url-wrapper').setStyle('display', 'none');
    if (!videoExist) {
      $('file-wrapper').setStyle('display', 'block');
    }
  });

  $('remove').addEvent('click', function () {
    showLoader();
    $('store-video-preview').setStyle('display', 'none');
    new Request.JSON({
      method: 'post',
      url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'videos', 'action'=>'delete'), 'admin_default', 1); ?>',
      data: {
        product_id: '<?php echo $this->product->getIdentity(); ?>',
        format: 'json'
      },
      onSuccess: function (response) {
        if (response.status) {
          $('store-video-preview').set('html', '');
          $('url').value = '';
          $('code').value = '';
          $('description').value = '';
          /*if($('type-2').checked)
            $('file-wrapper').setStyle('display', 'block');
          $('remove').setStyle('display', 'none');*/
          videoExist = false;
          updateVideoFields();
        }
        hideLoader();
        $('store-video-preview').setStyle('display', 'block');
        //$('upload').setStyle('display', 'none');
      },
      onError: function (text, code) {
        hideLoader();
        $('store-video-preview').setStyle('display', 'block');
      },
      onFailure: function (obj) {
        hideLoader();
        $('store-video-preview').setStyle('display', 'block');
      }
    }).send();
  });

  $('upload').addEvent('click', function () {
    var title = $('title').value;
    var descr = $('description').value;
    var data = {};
    if (getVideoType() == 3) {
      data = {
        product_id: '<?php echo $this->product->getIdentity(); ?>',
        title: title,
        description: descr,
        type: getVideoType(),
        format: 'json'
      };
    } else {
      data = {
        product_id: '<?php echo $this->product->getIdentity(); ?>',
        title: title,
        description: descr,
        url: $('url').value,
        type: getVideoType(),
        code: $('code').value,
        format: 'json'
      };
    }

    showLoader();
    new Request.JSON({
      method: 'post',
      url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'videos', 'action'=>'edit-video'), 'admin_default', 1); ?>',
      data: data,
      onSuccess: function (response) {
        if (response.status && response.preview) {
          $('store-video-preview').set('html', response.preview);
          videoExist = true;
          updateVideoFields();
        }
        hideLoader();
      },
      onError: function (text, code) {
        hideLoader();
      },
      onFailure: function (obj) {
        hideLoader();
      }
    }).send();
  });

  updateVideoFields();
});
function removeVideo() {

}
var current_code;

function getVideoType() {
  if ($('type-1').checked) {
    var url = $('url').value;
    if(url.indexOf('youtube') + 1) {
      return 1;
    }
    if(url.indexOf('vimeo') + 1) {
      return 2;
    }
    return 0;
  }
  if ($('type-3').checked) return 3;
  return 0;
}

var ignoreValidation = function () {
  $('upload').style.display = "block";
  $('validation').style.display = "none";
  $('code').value = current_code;
  $('ignore').value = true;
}

 function updateVideoFields() {
  var url_element = $("url-wrapper");
  var file_element = $("file-wrapper");
  var submit_element = $("upload");
  var remove_element = $("remove");

  if(!videoExist) {
    submit_element.style.display = 'none';
    remove_element.style.display = 'none';
  } else {
    submit_element.style.display = 'block';
    remove_element.style.display = 'block';
  }

  //submit_element.style.display = 'none';
  // If video source is empty
  if (getVideoType() == 0) {
    $('url').value = "";
    url_element.style.display = "none";
    file_element.style.display = "none";
    return;
  }

  if (getVideoType() == 3) {
    url_element.style.display = "none";
    if (!videoExist) {
      file_element.style.display = "block";
    } else {
      file_element.style.display = "none";
    }
    return;
  }

  // If video source is youtube or vimeo
  if (getVideoType() == 1 || getVideoType() == 2) {
    if(videoExist) {
      url_element.style.display = "none";
    } else {
      url_element.style.display = "block";
    }
    file_element.style.display = "none";
  }

   if ($('code').value && $('url').value) {
     submit_element.style.display = "block";
     file_element.style.display = 'none';
   }

  // if there is video_id that means this form is returned from uploading because some other required field
  /*if ($('id').value) {
    $('upload').style.display = "block";
    return;
  }*/
}

var video = {
  active: false,

  debug: false,

  currentUrl: null,

  currentTitle: null,

  currentDescription: null,

  currentImage: 0,

  currentImageSrc: null,

  imagesLoading: 0,

  images: [],

  maxAspect: (10 / 3), //(5 / 2), //3.1,

  minAspect: (3 / 10), //(2 / 5), //(1 / 3.1),

  minSize: 50,

  maxPixels: 500000,

  monitorInterval: null,

  monitorLastActivity: false,

  monitorDelay: 500,

  maxImageLoading: 5000,

  attach: function () {
    if($('type-3').checked) return;
    var bind = this;
    $('url').addEvent('keyup', function () {
      bind.monitorLastActivity = (new Date).valueOf();
    });

    var url_element = document.getElementById("url-element");
    var myElement = new Element("p");
    myElement.innerHTML = "test";
    myElement.addClass("description");
    myElement.id = "validation";
    myElement.style.display = "none";
    url_element.appendChild(myElement);

    var body = $('url');
    var lastBody = '';
    var lastMatch = '';
    (function () {
      // Ignore if no change or url matches
      if (body.value == lastBody || bind.currentUrl) {
        return;
      }

      // Ignore if delay not met yet
      if ((new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay) {
        return;
      }
      /*var pattern = "^(([^:/\\?#]+):)?(//(([^:/\\?#]*)(?::([^/\\?#]*))?))?([^\\?#]*)(\\?([^#]*))?(#(.*))?$";
       var m = body.value.match(pattern);*/
      var m = body.value.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
      if ($type(m) && $type(m[0]) && lastMatch != m[0]) {
        if (video.isYoutube(m)) {
          video.youtube(body.value);
        } else  {
          video.vimeo(body.value);
        }
      }

      lastBody = body.value;
    }).periodical(250);
  },

  isYoutube:function(parts) {
    if(parts.length > 2) {
      return ( parts[1].indexOf('youtube') + 1);
    }
    return false;
  },

  youtube: function (url) {
    if($('type-3').checked) return;
    // extract v from url
    var myURI = new URI(url);
    var youtube_code = myURI.get('data')['v'];
    if (youtube_code === undefined) {
      youtube_code = myURI.get('file');
    }
    if (youtube_code) {
      (new Request.HTML({
        'format': 'html',
        'url': '<?php echo $this->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'validation', 'product_id' => $this->product->getIdentity()), 'admin_default', true) ?>',
        'data': {
          'ajax': true,
          'code': youtube_code,
          'type': 'youtube'
        },
        'onRequest': function () {
          $('validation').style.display = "block";
          $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
          //$('upload').style.display = "none";
        },
        'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
          if (valid) {
            $('validation').style.display = "none";
            $('code').value = youtube_code;
          }
          else {
            $('upload').style.display = "none";
            current_code = youtube_code;
            <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here").'</a>'; ?>
            $('validation').innerHTML = '<?php echo addslashes($this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link)); ?>';
          }
          updateVideoFields();
        }
      })).send();
    }
  },

  vimeo: function (url) {
    if($('type-3').checked) return;
    var myURI = new URI(url);
    var vimeo_code = myURI.get('file');
    if (vimeo_code.length > 0) {
      (new Request.HTML({
        'format': 'html',
        'url': '<?php echo $this->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'validation', 'product_id' => $this->product->getIdentity()), 'admin_default', true) ?>',
        'data': {
          'ajax': true,
          'code': vimeo_code,
          'type': 'vimeo'
        },
        'onRequest': function () {
          $('validation').style.display = "block";
          $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
          //$('upload').style.display = "none";
        },
        'onSuccess': function (responseTree, responseElements, responseHTML, responseJavaScript) {
          if (valid) {
            $('validation').style.display = "none";
            $('code').value = vimeo_code;
          }
          else {
            current_code = vimeo_code;
            <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"; ?>
            $('validation').innerHTML = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link); ?>";
          }
          updateVideoFields();
        }
      })).send();
    }
  }
};

en4.core.runonce.add(video.attach);

</script>



<?php echo $this->render('admin/_productHeader.tpl'); ?>

<div>

  <div style="width: 75%; float: left;">
    <?php echo $this->getGatewayState(0); ?>

    <div class="settings admin_home_middle" style="clear: none;">
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
  <div style="float: right;">
    <?php echo $this->render('admin/_productsMenu.tpl'); ?>
  </div>

</div>
