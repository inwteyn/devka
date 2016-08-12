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
<?php if ($this->playlists && $this->playlists->getTotalItemCount() > 0): ?>
  <!-- Filter Form there   -->
      <div id="filter_block">
        <ul class="items">
    <?php foreach($this->playlists as $item): ?>
          <li>
            <div class="item_photo">
              <a href="<?php echo $this->url(array('action' => 'view', 'playlist_id' => $item->getIdentity()), 'page_music', true)?>" onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                <?php
                  $photo_url = $item->photo_id ? $item->getPhotoUrl('thumb.icon') : "application/modules/Pagemusic/externals/images/nophoto_profile.jpg";
                ?>
                <img src="<?php echo $photo_url; ?>" alt="" />
              </a>
            </div>
            <div class="item_body">
              <div class="item_title">
                <a href="<?php echo $this->url(array('action' => 'view', 'page_id'=> $this->page_id, 'playlist_id' => $item->getIdentity()), 'page_music', true)?>" onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                  <?php echo $item->getTitle(); ?>
                </a>
              </div>
              <div class="item_date">
                <p class="stats">
          				<?php echo $this->translate(array('pagemusic_%s play', 'pagemusic_%s plays', $item->play_count), ($item->play_count)); ?>
          				(<?php echo $this->translate(array('pagemusic_%s listener', 'pagemusic_%s listeners', $item->listener_count), ($item->listener_count)); ?>)
          			</p>
                <p class="label">
                  <?php echo Engine_String::substr(Engine_String::strip_tags($item->getDescription()), 0, 100); ?><br /><br />
                  <?php echo $this->translate(array('pagemusic_%s track', 'pagemusic_%s tracks', $item->track_count), ($item->track_count)); ?>
                </p>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

    <?php else: ?>
      <div id="filter_block">
        <div class="tip">
          <span>
            <?php echo $this->translate('pagemusic_Nobody has created an playlist.');?>
          </span>
        </div>
      </div>
    <?php endif; ?>
