<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-12-26 17:53 taalay $
 * @author     Taalay
 */
?>

<?php
  if( !$this->video || ($this->video->status != 1) ) {
    echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
    return; // Do no render the rest of the script in this mode
  }
?>

<?php if( $this->video->type == 3 ) : ?>
  <script type='text/javascript'>
    page_video.flashembed("pagevideo_embed", "<?php echo $this->video_location;?>", "<?php echo $this->video->duration ?>");
  </script>
<?php endif;?>

<script type="text/javascript">
  var video_id = <?php echo $this->video->getIdentity(); ?>;
  var viewer = <?php echo (int)$this->viewer_id; ?>;
</script>

<div class='layout_middle'>
  <div class="pagevideo_view">
		<div class="pagealbum_view_header">
			<span><?php echo $this->video->title;?></span>
			<?php if (!$this->isAllowedPost): ?>
        <div class="backlink_wrapper">
          <a class="backlink" href="javascript:page_video.all()"><?php echo $this->translate('Back To Videos'); ?></a>
        </div>
			<?php endif; ?>
		  <div class="clr"></div>
		</div>
    <div class="clr"></div>
    <?php if( $this->video->type == 3): ?>
      <div id="pagevideo_embed" class="pagevideo_embed"></div>
    <?php else: ?>
      <div class="pagevideo_embed">
        <?php echo $this->videoEmbedded;?>
      </div>
    <?php endif; ?>
		
		<div class="page-misc">
			<div class="page-misc-date">
			  <?php echo $this->translate("Posted %s", $this->timestamp($this->video->creation_date)); ?>
			</div>
			<?php if (count($this->videoTags)) : ?>
        <div class="page-tag">
          <div class="tags">
            <?php foreach ($this->videoTags as $tag): ?>
              <a href='javascript:void(0);' onclick="page_search.search_by_tag(<?php echo $tag->getTag()->tag_id; ?>);">#<?php echo $tag->getTag()->text ?></a>&nbsp;
            <?php endforeach; ?>
          </div>
          <div class="clr"></div>
        </div>
			<?php endif; ?>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		
    <?php echo $this->htmlLink(
        array(
          'module'=> 'activity',
          'controller' => 'index',
          'action' => 'share',
          'route' => 'default',
          'type' => 'pagevideo',
          'id' => $this->video->getIdentity(),
          'format' => 'smoothbox'
        ), $this->translate("Share"), array('class' => 'smoothbox')
      );
    ?> -
    <?php echo $this->htmlLink(
        array(
          'module'=> 'core',
          'controller' => 'report',
          'action' => 'create',
          'route' => 'default',
          'subject' =>  $this->video->getGuid(),
          'format' => 'smoothbox'
        ), $this->translate("Report"), array('class' => 'smoothbox')
      );
    ?>

    <br /><br />
    <div class="pagevideo_desc"><?php echo $this->viewMore($this->video->description); ?></div>
    <div class='pagevideo_options'>
      <?php if ($this->can_edit):?>
        <?php echo $this->htmlLink("javascript:page_video.edit({$this->video->getIdentity()})", $this->translate('Edit Video'), array('class' => 'buttonlink icon_pagevideo_edit')) ?>
      <?php endif;?>
      <?php if ($this->can_delete) : ?>
        <?php
          if ($this->video->status != 2) {
            echo $this->htmlLink("javascript:page_video.confirm({$this->video->getIdentity()})", $this->translate('Delete Video'), array('class' => 'buttonlink icon_pagevideo_delete'));
          }
        ?>
      <?php endif;?>
    </div>
    <?php if (Engine_Api::_()->getDbTable('modules' ,'hecore')->isModuleEnabled('wall')) : ?>
      <?php echo $this->wallComments($this->video, $this->viewer()); ?>
    <?php else: ?>
		  <div class="comments" id="pagevideo_comments"></div>
    <?php endif;?>
    
  </div>
</div>