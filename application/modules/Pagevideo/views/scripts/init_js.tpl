<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: init_js.tpl 2010-09-20 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">
page_video.url.page = "<?php echo $this->subject->getHref(); ?>";
page_video.url.index = "<?php echo $this->url(array(), 'page_video'); ?>";
page_video.url.create = "<?php echo $this->url(array('action' => 'create'), 'page_video'); ?>";
page_video.url.view = "<?php echo $this->url(array('action' => 'view'), 'page_video'); ?>";
page_video.url.manage = "<?php echo $this->url(array('action' => 'manage'), 'page_video'); ?>";
page_video.url.delete_url = "<?php echo $this->url(array('action' => 'delete'), 'page_video'); ?>";
page_video.url.edit = "<?php echo $this->url(array('action' => 'edit'), 'page_video'); ?>";
page_video.url.save = "<?php echo $this->url(array('action' => 'save'), 'page_video'); ?>";
page_video.page_id = <?php echo (int)$this->subject()->getIdentity(); ?>;
page_video.files = <?php echo Zend_Json_Encoder::encode($this->files); ?>;
page_video.allowed_post = <?php echo (int)$this->isAllowedPost; ?>;
page_video.allowed_comment = <?php echo (int)$this->isAllowedComment; ?>;

en4.core.runonce.add(function(){
  page_video.init();
  <?php echo $this->init_js_str; ?>
});

var animate_thumbs = function() {
  $$('.pagevideo_frame').addEvent('mouseover', function() {
    this.src = "<?php echo $this->baseUrl() . "/application/modules/Pagevideo/externals/images/videoframe_{$this->theme_class}_hover.png"; ?>";
  });
  $$('.pagevideo_frame').addEvent('mouseout', function() {
    this.src = "<?php echo $this->baseUrl() . "/application/modules/Pagevideo/externals/images/videoframe_{$this->theme_class}.png"; ?>";
  });
}

var current_code;

var ignoreValidation = function(){
  $('video_upload-wrapper').style.display = "block";
  $('validation').style.display = "none";
  $('video_code').value = current_code;
  $('video_ignore').value = true;
}

var updateVideoFields = function()
{
  var video_element = document.getElementById("video_type");
  var url_element = document.getElementById("video_url-wrapper");
  var file_element = document.getElementById("video_file-wrapper");
  var submit_element = document.getElementById("video_upload-wrapper");

  // clear url if input field on change
  //$('code').value = "";
  $('video_upload-wrapper').style.display = "none";

  // If video source is empty
  if (video_element.value == 0)
  {
    $('video_url').value = "";
    file_element.style.display = "none";
    url_element.style.display = "none";
    return;
  }

  if ($('video_code').value && $('video_url').value)
  {
    $('video_type-wrapper').style.display = "none";
    file_element.style.display = "none";
    $('video_upload-wrapper').style.display = "block";
    return;
  }

  // If video source is youtube or vimeo
  if (video_element.value == 1 || video_element.value == 2)
  {
    $('video_url').value = "";
    $('video_code').value = "";
    file_element.style.display = "none";
    url_element.style.display = "block";
    return;
  }

  // If video source is from computer
  if (video_element.value == 3)
  {
    $('video_url').value = "";
    $('video_code').value = "";
    file_element.style.display = "block";
    url_element.style.display = "none";
    $('video-demo-browse').style.display = "block";
    return;
  }

  // if there is video_id that means this form is returned from uploading because some other required field
  if ($('video_id').value)
  {
    $('video_type-wrapper').style.display = "none";
    file_element.style.display = "none";
    $('video_upload-wrapper').style.display = "block";
    return;
  }

}

var video = {
    active : false,

    debug : false,

    currentUrl : null,

    currentTitle : null,

    currentDescription : null,

    currentImage : 0,

    currentImageSrc : null,

    imagesLoading : 0,

    images : [],

    maxAspect : (10 / 3), //(5 / 2), //3.1,

    minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),

    minSize : 50,

    maxPixels : 500000,

    monitorInterval: null,

    monitorLastActivity : false,

    monitorDelay : 500,

    maxImageLoading : 5000,
    
    attach : function()
    {
      var bind = this;
      $('video_url').addEvent('keyup', function()
      {
        bind.monitorLastActivity = (new Date).valueOf();
      });

      var url_element = document.getElementById("video_url-element");
      var myElement = new Element("p");
      myElement.innerHTML = "test";
      myElement.addClass("description");
      myElement.id = "validation";
      myElement.style.display = "none";
      url_element.appendChild(myElement);

      var body = $('video_url');
      var lastBody = '';
      var lastMatch = '';
      var video_element = $('video_type');
      (function()
      {
        // Ignore if no change or url matches
        if( body.value == lastBody || bind.currentUrl )
        {
          return;
        }

        // Ignore if delay not met yet
        if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay )
        {
          return;
        }

        // Check for link
        var m = body.value;
        if(m)
        {
          if (video_element.value == 1){
            video.youtube(body.value);
          }
          else
            video.vimeo(body.value);
        }
        else{
         
        }

        lastBody = body.value;
      }).periodical(250);
    },

    youtube : function(url){
      // extract v from url
      var myURI = new URI(url);
      var youtube_code = myURI.get('data')['v'];
      if( youtube_code === undefined ) {
        youtube_code = myURI.get('file');
      }

      if (youtube_code){
        (new Request.HTML({
          'format': 'html',
          'url' : '<?php echo $this->url(array('action' => 'validation'), 'page_video', true) ?>',
          'data' : {
            'ajax' : true,
            'code' : youtube_code,
            'type' : 'youtube'
          },
          'onRequest' : function(){
            $('validation').style.display = "block";
            $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
            $('video_upload-wrapper').style.display = "none";
          },
          'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
          {
            if (valid){
              $('video_upload-wrapper').style.display = "block";
              $('validation').style.display = "none";
              $('video_code').value = youtube_code;
            }
            else{
              $('video_upload-wrapper').style.display = "none";
              current_code = youtube_code;
              <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here").'</a>'; ?>
                    $('validation').innerHTML = '<?php echo addslashes($this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link)); ?>';
            }
          }
        })).send();
      }
    },

    vimeo: function(url){
      var myURI = new URI(url);
      var vimeo_code = myURI.get('file');
      if (vimeo_code.length > 0){
        (new Request.HTML({
          'format': 'html',
          'url' : '<?php echo $this->url(array('action' => 'validation'), 'page_video', true) ?>',
          'data' : {
            'ajax' : true,
            'code' : vimeo_code,
            'type' : 'vimeo'
          },
          'onRequest' : function(){
            $('validation').style.display = "block";
            $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
            $('video_upload-wrapper').style.display = "none";
          },
          'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
          {
            if (valid){
              $('video_upload-wrapper').style.display = "block";
              $('validation').style.display = "none";
              $('video_code').value = vimeo_code;
            }
            else{
              $('video_upload-wrapper').style.display = "none";
              current_code = vimeo_code;
              <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"; ?>
              $('validation').innerHTML = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link); ?>";
            }
          }
        })).send();
      }
    }
}

en4.core.runonce.add(updateVideoFields);
en4.core.runonce.add(video.attach);
window.addEvent('domready', function(){
    var url_element = document.getElementById("video_url-element");

    var myElement = new Element("p");

    myElement.innerHTML = "test";

    myElement.addClass("description");

    myElement.id = "validation";

    myElement.style.display = "none";

    url_element.appendChild(myElement);

    if( $('video_url'))
    {
        $('video_url').addEvent('keyup', function(){
                var video_urls = $('video_url');
                var video_elements  =  $('video_type');
                if (video_elements.value == 1){
                    video.youtube(video_urls.value);

                }

                else{ video.vimeo(video_urls.value);}

            }
        )
    }

});
</script>