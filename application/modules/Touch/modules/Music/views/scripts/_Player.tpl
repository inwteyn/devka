<?php
$this->headScript()
       ->appendFile($this->baseUrl().'/application/modules/Touch/externals/scripts/MusicPlayer.js');
?>
<?php
  $playlist = $this->playlist;
  $songs    = (isset($this->songs) && !empty($this->songs))
            ? $this->songs
            : $playlist->getSongs();

  // this forces every playlist to have a unique ID, so that a playlist can be displayed twice on the same page
  $random   = '';
  for ($i=0; $i<6; $i++) {
    $d=rand(1,30)%2;
    $random .= ($d?chr(rand(65,90)):chr(rand(48,57)));
  }

?>
<script type="text/javascript">
    var playerCore;
    var loaded = setInterval(
      function(){
        if($('player<?php echo $random; ?>')){
          clearInterval(loaded);
          playerCore = new MusicPlayer($('player<?php echo $random; ?>'));
        }
      }, 500
    );
</script>
<?php if (!$playlist->isViewable() && $this->message_view): ?>
  <div class="tip">
    <?php echo $this->translate('This playlist is private.') ?>
  </div>
<?php return; elseif (empty($songs) || empty($songs[0])): ?>
    <br />
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no songs uploaded yet.') ?>
        <?php if( $playlist->isEditable() ): ?>
          <?php echo $this->htmlLink($playlist->getHref(array(
            'route' => 'music_playlist_specific',
            'action' => 'edit',
          )), $this->translate('Why don\'t you add some?')) ?>
        <?php endif; ?>
      </span>
    </div>
    <br />
<?php return; endif; ?>
    

<div id="player<?php echo $random; ?>" class="player">

  <!-- Audio Element -->
  <audio>
  </audio>

  <div class="playlist_header">
    <div class="album_art">
    <?php if($this->itemPhoto($playlist, null)){ ?>
      <?php echo $this->itemPhoto($playlist,  'thumb.icon', $playlist->getTitle()) ?>
      <?php } else {?>
      <img src="<?php echo $this->baseUrl() . '/application/modules/Touch/modules/Music/externals/styles/images/player/default_art.png' ?>" /> >
     <?php } ?>
    </div>
    <div class="player_pop_in_out_btn normal"></div>
    <div class="playlist_info">
      <span class="artist_name"><?php echo $playlist->getTitle() ?></span>
      <span class="playlist_title"><?php echo $this->translate('Created %1$s by %2$s', $this->timestamp($playlist->creation_date), $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle())) ?></span>
      
    </div>

  </div>
  <div class="playlist" id='<?php echo $this->playlist->playlist_id; ?>'>
  <!-- playlist items  -->
  <?php foreach( $songs as $song ){ if( !empty($song) ){ ?>
    <a class="list_item" target="_blank" href="<?php echo $song->getFilePath(); ?>" rel="<?php echo $song->song_id; ?>" type = 'audio' >
      <span class="item_icon" title="<?php echo $this->translate('download') ?>"></span>
      <span class="audio_item_body">
        <span class="item_duration"><b><?php echo vsprintf(Zend_Registry::get('Zend_Translate')->_(array('%s <br> play', '%s <br> plays', $song->play_count)),
                    Zend_Locale_Format::toNumber($song->play_count)
                    ); ?></b></span>
        <span class="item_title"><?php echo $this->string()->truncate($song->getTitle(), 50); ?></span>
        <span class="item_description"><?php echo $playlist->getTitle() ?> - <?php echo $playlist->description ?></span>
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
          <div class="volume_control">
            <div class="volume_control_slider">
              <div class="volume_control_decoration1"></div>
              <div class="volume_control_slider_knob"></div>
            </div>
          </div>
          <span class="volume"><div></div></span>
          <span class="time">&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <div class="audio_info">
          <span class="audio_title"></span>
          <span class="audio_description"><?php echo $playlist->getTitle() ?> - <?php echo $playlist->description ?></span>
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
