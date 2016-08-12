<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */

?>
<div>test
    <img src="application/modules/Store/externals/images2/nophoto_profile.jpg" alt=""/>
</div>
    <script type="text/javascript">
        store_music.view(1);
    </script>
<?php if ($this->playlists && $this->playlists->getTotalItemCount() > 0): ?>
    <div class="store-music-browse-container" id="page_music_browse_container">
        <?php foreach($this->playlists as $item): ?>
            <div class="store-music-browse-item">
                <div class="store-music-browse-item-cover playlist-cover">
                    <a href="<?php echo $item->getHref(); ?>" onclick="page_music.view(<?php echo $item->getIdentity(); ?>); return false;">
                        <?php
                        $photo_url = $item->photo_id ? $item->getPhotoUrl('thumb.profile') : "application/modules/storemusic/externals/images/nophoto_profile.jpg";
                        ?>
                        <span class="jewelcase" style="background-image: url(<?php echo $photo_url;?>)"></span>
                    </a>
                </div>
                <div class="store-music-browse-item-info">
                    <a href="<?php echo $item->getHref(); ?>" onclick="store_music.view(<?php echo $item->getIdentity(); ?>); return false;"><?php echo $item->getTitle(); ?></a>
                    <p class="label">
                        <?php echo $this->translate(array('storemusic_%s track', 'storemusic_%s tracks', $item->track_count), ($item->track_count)); ?>
                        <?php echo Engine_String::substr(Engine_String::strip_tags($item->getDescription()), 0, 100); ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="clr"></div>
    </div>

    <?php if( $this->playlists->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->playlists, null, array("pagination.tpl","storemusic"), array(
            'store' => $this->storeObject
        )); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="tip">
    <span>
      <?php echo $this->translate('storemusic_Nobody has created an playlist.');?>
      <?php if ($this->isAllowedPost): // @todo check if user is allowed to create an album ?>
          <?php echo $this->translate('storemusic_Be the first to %1$screate%2$s one!', '<a href="javascript:void(0)" onClick="store_music.create();">', '</a>'); ?>
      <?php endif; ?>
    </span>
    </div>
<?php endif; ?>