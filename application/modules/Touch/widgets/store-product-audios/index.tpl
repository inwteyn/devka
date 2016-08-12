<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-09-14 17:07:11 taalay $
 * @author     Taalay
 */

?>
<?php
$this->headScript()
       ->appendFile($this->baseUrl().'/application/modules/Touch/externals/scripts/MusicPlayer.js');
?>

<script type="text/javascript">
    var playerCore;
    var loaded = setInterval(
      function(){
        if($('player<?php echo $this->product->getIdentity(); ?>')){
          clearInterval(loaded);
          playerCore = new MusicPlayer($('player<?php echo $this->product->getIdentity(); ?>'));
        }
      }, 500
    );
</script>

<div class="store-audio-view">
	<div class="store-audio-view-songs">
    <div id="player<?php echo $this->product->getIdentity(); ?>" class="player">

      <!-- Audio Element -->
      <audio>
      </audio>
      <div class="playlist_header">
        <div class="playlist_info">
          <span class="artist_name"><?php echo $this->product->getTitle(); ?></span>
          <span class="playlist_title"><?php echo $this->product->description ?></span>
        </div>
      </div>
      <div class="playlist" id='<?php echo $this->product->getIdentity(); ?>'>
      <!-- playlist items  -->
      <?php foreach( $this->audios as $song ){ if( !empty($song) ){ ?>
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
              <span class="audio_description"><?php echo $this->product->getTitle(); ?></span>
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

