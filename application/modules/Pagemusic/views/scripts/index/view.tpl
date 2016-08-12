<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-10-21 17:53 idris $
 * @author     Idris
 */


?>
<div style="margin-bottom: 10px;">
  <a class="title" href="<?php echo $this->playlist->getHref(); ?>"
     onclick="page_music.view(<?php echo $this->playlist->getIdentity(); ?>); return false;"><?php echo $this->playlist->getTitle(); ?></a>

  <div style="float: right;">
    <?php if ($this->pageObject->isTeamMember()): ?>
      <p class="options">
        <a class="edit" href="javascript:page_music.edit(<?php echo $this->playlist->getIdentity(); ?>)"></a>
        <a class="delete" href="javascript:page_music.confirm_delete(<?php echo $this->playlist->getIdentity(); ?>)"></a>
      </p>
    <?php endif; ?>
  </div>
</div>

<div class="pagemusic-playlist-item">
  <table>
    <tr>
      <td><a href="<?php echo $this->playlist->getHref(); ?>"
             onclick="page_music.view(<?php echo $this->playlist->getIdentity(); ?>); return false;">
          <?php $photo_url = $this->playlist->photo_id ? $this->playlist->getPhotoUrl() : "application/modules/Pagemusic/externals/images/nophoto.jpg"; ?>
          <img src="<?php echo $photo_url; ?>">
        </a></td>
    </tr>
    <tr>
      <td>
  <span class="label">
    <?php echo $this->translate(array('pagemusic_%s track', 'pagemusic_%s tracks', $this->playlist->track_count), ($this->playlist->track_count)); ?>
    <?php //echo $this->playlist->getDescription(); ?>
  </span>
      </td>
    </tr>
    <tr>
      <td>
        <div class="playlist_share_wrapper">
          <?php echo $this->htmlLink($this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $this->playlist->getType(), 'id' => $this->playlist->getIdentity(), 'format' => 'smoothbox'), 'default', true), $this->translate('Share Playlist'), array('class' => 'smoothbox')); ?>
        </div>
      </td>
    </tr>
  </table>




  <?php if (!$this->isAllowedPost): ?>
    <div class="backlink_wrapper">
      <a class="backlink"
         href="javascript:page_music.index()"><?php echo $this->translate('pagemusic_Back To Playlists'); ?></a>
    </div>
  <?php endif; ?>
</div>
<div style="float: left;">
  <?php
  echo $this->partial(
    '_Player.tpl',
    array('playlist' => $this->playlist, 'popout' => true)
  )
  ?>
  <?php if (!$this->isAllowedPost): ?>
    <div class="backlink_wrapper">
      <a class="backlink"
         href="javascript:page_music.index()"><?php echo $this->translate('pagemusic_Back To Playlists'); ?></a>
    </div>
  <?php endif; ?>
  <div class="clr"></div>

  <?php if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall')): ?>
    <?php echo $this->wallComments($this->playlist, $this->viewer(), array('class' => 'page-music-view-comments')); ?>
  <?php else: ?>
    <div class="comments page-music-view-comments" id="playlist_comments"></div>
  <?php endif; ?>
</div>
<div style="clear: both"></div>