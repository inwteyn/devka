<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>


<?php if( !$this->video || $this->video->status !=1 ):
  echo '<div class="tip"><span><b>'.$this->translate('The video you are looking for does not exist or has not been processed yet.').'</b></span></div>';
  return; // Do no render the rest of the script in this mode
endif; ?>


<?php if( $this->video->type == 3 ):?>
  <script type='text/javascript'>

    en4.core.runonce.add(function (){

      if (Touch.isFlash()){

        flashembed("video_embed",
        {
          src: "<?php echo $this->baseUrl()?>/externals/flowplayer/flowplayer-3.1.5.swf",
          width: 480,
          height: 386,
          wmode: 'transparent'
        },
        {
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
              backgroundColor:'#000000'
            }
          }
        });

        } else {

          $('video_embed').set('html', '<div class="video_not_supported"><?php echo $this->translate('TOUCH_NOT_SUPPORTED_FLASH')?></div>');

        }

      });


  </script>
<?php endif;?>

<script type="text/javascript">
  var pre_rate = <?php echo $this->video->rating;?>;
  var rated = '<?php echo $this->rated;?>';
  var video_id = <?php echo $this->video->video_id;?>;
  var total_votes = <?php echo $this->rating_count;?>;
  var viewer = <?php echo $this->viewer_id;?>;

  function rating_over(rating) {
    if (rated == 1){
      $('rating_text').innerHTML = "<?php echo $this->translate('you already rated');?>";
      //set_rating();
    }
    else if (viewer == 0){
      $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate');?>";
    }
    else{
      $('rating_text').innerHTML = "<?php echo $this->translate('click to rate');?>";
      for(var x=1; x<=5; x++) {
        if(x <= rating) {
          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
        } else {
          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }
  }
  function rating_out() {
    $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    if (pre_rate != 0){
      set_rating();
    }
    else {
      for(var x=1; x<=5; x++) {
        $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function set_rating() {
    var rating = pre_rate;
    $('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    for(var x=1; x<=parseInt(rating); x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
    }

    for(var x=parseInt(rating)+1; x<=5; x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
    }

    var remainder = Math.round(rating)-rating;
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
    }
  }

  function rate(rating) {
    $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!');?>";
    for(var x=1; x<=5; x++) {
      $('rate_'+x).set('onclick', '');
    }
    (new Request.JSON({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
      'data' : {
        'format' : 'json',
        'rating' : rating,
        'video_id': video_id
      },
      'onRequest' : function(){
        rated = 1;
        total_votes = total_votes+1;
        pre_rate = (pre_rate+rating)/total_votes;
        set_rating();
      },
      'onSuccess' : function(responseJSON, responseText)
      {
        $('rating_text').innerHTML = responseJSON[0].total+" ratings";
      }
    })).send();

  }

  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }

  en4.core.runonce.add(set_rating);
</script>

<h4>
  <?php echo $this->video->getTitle() ?>
</h4>

<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
  <input type="hidden" id="tag" name="tag" value=""/>
</form>

<?php /* */ ?>
<div class="layout_main">

<?php /*
<div class="layout_right">
  <?php echo $this->content()->renderWidget('video.show-same-tags') ?>
  <?php echo $this->content()->renderWidget('video.show-also-liked') ?>
  <?php echo $this->content()->renderWidget('video.show-same-poster') ?>
</div>
*/ ?>

<div class='layout_middle video_view_container'>
<?php /* */ ?>

  <div class="video_view video_view_container">
    <div class="video_desc">
      <?php echo $this->video->description;?>
    </div>
    <?php if( $this->video->type == 3 ): ?>
    <div id="video_embed" class="video_embed">
    </div>
    <?php else: ?>
    <div class="video_embed">
      <?php echo $this->videoEmbedded ?>
    </div>
    <?php endif; ?>
    <div class="description">
      <?php echo $this->translate('Posted by') ?>
      <?php echo $this->htmlLink($this->video->getParent(), $this->video->getParent()->getTitle(), array('class' => 'tounchajax')) ?>
      - <?php echo $this->timestamp($this->video->creation_date) ?>
      <?php if( $this->category ): ?>
        - <?php echo $this->translate('Filed in') ?>
        <?php echo $this->translate($this->category->category_name) ?>
      <?php endif; ?>
      <?php if (count($this->videoTags )):?>
      -
        <?php foreach ($this->videoTags as $tag): ?>
          #<?php echo $tag->getTag()->text?>&nbsp;
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div id="video_rating" class="rating" onmouseout="rating_out();">
      <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
      <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
      <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
      <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
      <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
      <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
    </div>

    <br/>
    
        <div class='video_options'>
          <?php if( $this->can_edit ): ?>
            <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'video',
              'controller' => 'index',
              'action' => 'edit',
              'video_id' => $this->video->video_id
            ), $this->translate('Edit Video'), array(
              'class' => 'icon_video_edit touchajax'
            )) ?>
            &nbsp;|&nbsp;
          <?php endif;?>
          <?php if( $this->can_delete && $this->video->status != 2 ): ?>
            <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'video',
              'controller' => 'index',
              'action' => 'delete',
              'video_id' => $this->video->video_id,
            ), $this->translate('Delete Video'), array(
              'class' => 'smoothbox'
            )) ?>
            &nbsp;|&nbsp;
          <?php endif;?>
          <?php if ($this->viewer()->getIdentity()):?>
            <?php echo $this->htmlLink(array(
              'module'=> 'activity',
              'controller' => 'index',
              'action' => 'share',
              'route' => 'default',
              'type' => 'video',
              'id' => $this->video->getIdentity(),
            ), $this->translate("Share"), array(
              'class' => 'smoothbox'
            )); ?>
          <?php endif;?>
        </div>
    <?php echo $this->touchAction("list", "comment", "core", array("type"=>"video", "id"=>$this->video->video_id)) ?>
  </div>

<?php /* */ ?>
</div>

</div>
<?php /* */ ?>
