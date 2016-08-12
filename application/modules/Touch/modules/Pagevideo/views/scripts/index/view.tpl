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
<h4>
  <?php echo $this->htmlLink($this->video->getOwner()->getHref(), $this->video->getOwner()->getTitle(), array('class' =>
              'touchajax')); echo $this->translate("'s");?> <?php echo $this->translate("Video"); echo $this->translate(":") ?> <?php echo $this->video->getTitle() ? $this->video->getTitle() : $this->translate('Untitled'); ?>
</h4>

<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
  <input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class='layout_middle video_view_container'>

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

    <br/>

        <div class='video_options'>
          <?php if ($this->viewer()->getIdentity()):?>
            <?php echo $this->htmlLink(array(
              'module'=> 'activity',
              'controller' => 'index',
              'action' => 'share',
              'route' => 'default',
              'type' => 'pagevideo',
              'id' => $this->video->getIdentity(),
            ), $this->translate("Share"), array(
              'class' => 'smoothbox'
            )); ?>
          <?php endif;?>
        </div>

    <?php echo $this->action("list", "comment", "core", array("type"=>"video", "id"=>$this->video->pagevideo_id)) ?>

  </div>

</div>

</div>
