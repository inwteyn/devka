<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>

<div class="layout_right">
  <?php
    echo $this->form->render($this);
  ?>
</div>

<div class="layout_middle">
<?php if ($this->paginator && $this->paginator->getTotalItemCount() > 0): ?>
<div class="page-music-browse-container" id="page_music_browse_container">
  <?php foreach($this->paginator as $item): ?>
  <?php if( $item['type'] == 'music') : ?>
    <?php $music = Engine_Api::_()->getItem('music_playlist', $item['playlist_id']);?>
    <?php else: ?>
    <?php $music = Engine_Api::_()->getItem('playlist', $item['playlist_id']);?>
    <?php endif; ?>
  <div class="page-music-browse-item">
    <div class="page-music-browse-item-cover playlist-cover">
      <a href="<?php echo $music->getHref(); ?>">
        <?php
        $photo_url = $music->photo_id ? $music->getPhotoUrl('thumb.profile') : "application/modules/Pagemusic/externals/images/nophoto_profile.jpg";
        ?>

        <span class="jewelcase" style="background-image: url(<?php echo $photo_url;?>)"></span>
      </a>
    </div>

    <div class="page-music-browse-item-info">
      <?php echo $this->htmlLink($music->getHref(), $music->getTitle()); ?>
      <p class="stats">
        <?php echo $this->translate(array('pagemusic_%s play', 'pagemusic_%s plays', $music->play_count), ($music->play_count)); ?>
        <?php if( $item['type'] != 'music' ) : ?>
        (<?php echo $this->translate(array('pagemusic_%s listener', 'pagemusic_%s listeners', $music->listener_count), ($music->listener_count)); ?>)
        <?php endif; ?>
      </p>
      <p class="label">
        <?php echo Engine_String::substr(Engine_String::strip_tags($music->getDescription()), 0, 50); ?>
        <br/>
        <?php echo $this->translate('By'); ?>
        <?php echo $this->htmlLink($music->getOwner()->getHref(), $music->getOwner()->getTitle()); ?>
        <?php if($item['type'] == 'page') : ?>
        <br/>
        <?php echo $this->translate('On page ')?>
        <?php echo $this->htmlLink($music->getPage()->getHref(), $music->getPage()->getTitle()); ?>
        <?php endif; ?>

        <br /><br />
        <?php if($item['type'] != 'music') : ?>
        <?php echo $this->translate(array('pagemusic_%s track', 'pagemusic_%s tracks', $music->track_count), ($music->track_count)); ?>
        <?php endif;?>
      </p>
      <div class="options">
        <?php if( $item['type'] == 'page') : ?>
          <?php echo $this->htmlLink($music->getHref(), '', array('class' => 'edit')); ?>
          <?php echo $this->htmlLink(array('route' => 'page_musics', 'action' => 'delete', 'playlist_id' => $music->getIdentity(), 'format' => 'smoothbox'), '', array('class' => 'smoothbox delete')); ?>
        <?php else : ?>
          <?php echo $this->htmlLink($music->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit')), '', array('class' => 'edit')); ?>
          <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'music', 'controller' => 'playlist', 'action' => 'delete', 'playlist_id' => $music->getIdentity(), 'format' => 'smoothbox'), '', array('class' => 'smoothbox delete')); ?>
        <?php endif; ?>
        <div class="clr"></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="clr"></div>
</div>

<?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>
<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate('There is no playlist.');?>
    </span>
</div>
<?php endif; ?>
</div>