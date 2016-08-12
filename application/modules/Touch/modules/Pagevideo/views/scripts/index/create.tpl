<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<script type="text/javascript">
var current_code;

var ignoreValidation = function(){
  $('video_upload-wrapper').style.display = "block";
  $('video_validation').style.display = "none";
  $('video_code').value = current_code;
  $('video_ignore').value = true;
}
var updateTextFields = function()
{

  var video_element = document.getElementById("video_type");
  var url_element = document.getElementById("video_url-wrapper");
  var file_element = document.getElementById("file-wrapper");
  var submit_element = document.getElementById("video_upload-wrapper");

  // clear url if input field on change
  //$('video_code').value = "";
  $('video_upload-wrapper').style.display = "none";
  if ($('file')){
    $('file').value = '';
  }

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
    return;
  }

  // if there is video_id that means this form is returned from uploading because some other required field
  if ($('id').value)
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
      myElement.id = "video_validation";
      myElement.style.display = "none";
      url_element.appendChild(myElement);

      var body = $('video_url');
      var lastBody = '';
      var lastMatch = '';
      var video_element = $('video_type');

      if ($('file')){
        $('file').addEvent('change', function (){
          $('video_upload-wrapper').style.display = "block";
        });
      }


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
        var m = body.value.match(/http:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
        if( $type(m) && $type(m[0]) && lastMatch != m[0] )
        {
          if (video_element.value == 1){
            video.youtube(body.value);
          }
          else video.vimeo(body.value);
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

      if (youtube_code){
        (new Request.HTML({
          'format': 'html',
          'url' : '<?php echo $this->url(array('module' => 'pagevideo', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>',
          'data' : {
            'ajax' : true,
            'code' : youtube_code,
            'type' : 'youtube'
          },
          'onRequest' : function(){
            $('video_validation').style.display = "block";
            $('video_validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
            $('video_upload-wrapper').style.display = "none";
          },
          'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
          {
            if (valid){
              $('video_upload-wrapper').style.display = "block";
              $('video_validation').style.display = "none";
              $('video_code').value = youtube_code;
            }
            else{
              $('video_upload-wrapper').style.display = "none";
              current_code = youtube_code;
              <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here").'</a>'; ?>
                    $('video_validation').innerHTML = '<?php echo addslashes($this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link)); ?>';
            }
          }
        })).send();
      }
    },

    vimeo :function(url){
      var myURI = new URI(url);
      var vimeo_code = myURI.get('file');
      if (vimeo_code.length > 0){
        (new Request.HTML({
          'format': 'html',
          'url' : '<?php echo $this->url(array('module' => 'pagevideo', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>',
          'data' : {
            'ajax' : true,
            'code' : vimeo_code,
            'type' : 'vimeo'
          },
          'onRequest' : function(){
            $('video_validation').style.display = "block";
            $('video_validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...'));?>';
            $('video_upload-wrapper').style.display = "none";
          },
          'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
          {
            if (valid){
              $('video_upload-wrapper').style.display = "block";
              $('video_validation').style.display = "none";
              $('video_code').value = vimeo_code;
            }
            else{
              $('video_upload-wrapper').style.display = "none";
              current_code = vimeo_code;
              <?php $link = "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"; ?>
              $('video_validation').innerHTML = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $link); ?>";
            }
          }
        })).send();
      }
    }
}

en4.core.runonce.add(updateTextFields);
en4.core.runonce.add(video.attach);

en4.core.runonce.add(function (){
  if (Touch.isIPhone()){
    $$('#type option[value=3]').destroy();
  }
});

//en4.core.runonce.add($('url').value = '<?php echo $this->url?>');


</script>

<div id="navigation_content">

  <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have already uploaded the maximum number of videos allowed.');?>
        <?php echo $this->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'video_general'));?>
      </span>
    </div>
    <br/>
  <?php else:?>
    <div class="layout_content">
      <?php echo $this->form->setAttrib('class', 'global_form touchupload')->render($this);?>
    </div>
  <?php endif; ?>

</div>


