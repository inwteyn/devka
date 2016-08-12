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
  <div class="page_sub_navigation">
    <ul class="touch_sub_navigation">
      <li>
        <a class="sub_nav_item" href="<?php echo $this->url(array('action'=>'index', 'page_id'=>$this->subject()->getIdentity()), 'page_music', true) ?>" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate('Browse Playlists'); ?>
        </a>
      </li>
      <li>
        <a class="sub_nav_item" href="<?php echo $this->url(array('action'=>'manage', 'page_id'=>$this->subject()->getIdentity()), 'page_music', true) ?>" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate('TOUCH_My Playlists'); ?>
        </a>
      </li>
    </ul>
  </div>
<div style="clear: both; height: 8px;"></div>
  <div id="sub_navigation_loading"  style="display: none;">
      <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
    </div>
    <div id="sub_navigation_content" >
  <!-- Filter Form there   -->
      <div id="filter_block">
        <ul class="items">
    <?php foreach($this->playlists as $item): ?>
          <li>
            <div class="item_photo">
              <a href="<?php echo $this->url(array('action' => 'view', 'playlist_id' => $item->getIdentity()), 'page_music', true)?>" onclick="Touch.navigation.subNavRequest($(this)); return false;">
                <?php
                  $photo_url = $item->photo_id ? $item->getPhotoUrl('thumb.icon') : "application/modules/Pagemusic/externals/images/nophoto_profile.jpg";
                ?>
                <img src="<?php echo $photo_url; ?>" class="pagemusic_art" alt="" />
              </a>
            </div>
            <div class="item_body">
              <div class="item_title">
                <a href="<?php echo $this->url(array('action' => 'view', 'page_id'=> $this->subject()->getIdentity(), 'playlist_id' => $item->getIdentity()), 'page_music', true)?>" onclick="Touch.navigation.subNavRequest($(this)); return false;">
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

    </div>
    <?php else: ?>
    <div id="sub_navigation_loading"  style="display: none;">
      <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
    </div>

    <div id="sub_navigation_content" >
      <div id="filter_block">
        <div class="tip">
          <span>
            <?php echo $this->translate('pagemusic_Nobody has created an playlist.');?>
          </span>
        </div>
      </div>
    </div>
    <?php endif; ?>
