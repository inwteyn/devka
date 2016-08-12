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
<?php
$this->headScript()
       ->appendFile($this->baseUrl().'/application/modules/Touch/externals/scripts/MusicPlayer.js');
?>

<?php if (count($this->songs) > 0){?>
<script type="text/javascript">
  var playerCore;
  var loaded = setInterval(
    function(){
      if($('player<?php echo $this->playlist->getIdentity(); ?>')){
        clearInterval(loaded);
        playerCore = new MusicPlayer($('player<?php echo $this->playlist->getIdentity(); ?>'));
      }
    }, 500
  );
</script>
<div class="store-audio-view">
	<div class="store-audio-view-songs">
    <div id="player<?php echo $this->playlist->getIdentity(); ?>" class="player">

      <!-- Audio Element -->
      <audio>
      </audio>
      <div class="playlist_header">
        <div class="album_art">
          <?php
            $photo_url = $this->playlist->photo_id ? $this->playlist->getPhotoUrl('thumb.icon') : "application/modules/Pagemusic/externals/images/nophoto_profile.jpg";
          ?>
          <img src="<?php echo $photo_url; ?>" class="pagemusic_art" alt="" />
        </div>
        <div class="playlist_info">
          <span class="artist_name"><?php echo $this->playlist->getTitle(); ?></span>
          <span class="playlist_title"><?php echo Engine_String::substr(Engine_String::strip_tags($this->playlist->getDescription()), 0, 100); ?></span>
        </div>
      </div>
      <div class="playlist" id='<?php echo $this->playlist->getIdentity(); ?>'>
      <!-- playlist items  -->
      <?php foreach( $this->songs as $song ){ if( !empty($song) ){ ?>
        <a class="list_item" href="<?php echo $this->storage->get($song->file_id)->map(); ?>" rel="<?php echo $song->getIdentity(); ?>" type = 'audio' onclick="return false;">
          <span class="item_icon"></span>
          <span class="audio_item_body">
            <span class="item_title"><?php echo $this->string()->truncate($song->getTitle(), 50); ?></span>
            <span class="item_description"></span>
          </span>
        </a>
    <?php }} ?>
      </div>
      <div class="player_controls">
        <div class="contol_main">
          <div class="btn_bar">
            <div class="play_pause_btn paused">
            </div>
          </div>
          <div class="now_playing">
            <div class="duration_time">
              <span class="time">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="audio_info">
              <span class="audio_title"></span>
              <span class="audio_description"><?php echo $this->playlist->getTitle(); ?></span>
            </div>
          </div>
        </div>
        <div class="progressbar_container">
          <div class="progressbar">
          <div class="progress_value_bar">
          </div>
          <div class="slider">
          </div>
          </div>
          <div class="shuffle_on_off"></div>
        </div>
      </div>
      <span class="html5_audio_doesnt_support_mp3" style="display: none;"><?php echo $this->translate('TOUCH_HTML5_AUDIO_DOESNT_SUPPORT_MP3'); ?></span>
    </div>
    <div id = 'fix'>

	</div>
</div>
  <?php }else{ ?>
 		<br />
 		<div class="tip"><span><?php echo $this->translate('pagemusic_NO_SONGS_TEXT'); ?></span></div>
 	<?php } ?>
<?php echo $this->touchAction("list", "comment", "core", array("type"=>"playlist", "id"=>$this->playlist->getIdentity(),
'viewAllLikes'=>true)) ?>
