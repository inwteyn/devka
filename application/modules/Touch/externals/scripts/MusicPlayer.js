/* $Id: MusicPlayer.js 2011-09-29 17:16 jungar $ */

// Music Player Core JavaScript
var MusicPlayer = new Class({
  audioEngine: false,
  playerContainer: null,
  monitor: null,
  timer: null,
  err_templ: 'Error: MusicPlayer.',
  playlist: null,
  loopPlaylist: true,
  shuffle: false,
  width: null,
  refreshInterval: 200,
  refreshIntervaler: null,
  tallied: [],
  controls: {
    playPauseBtn: null,
    popOutBtn: null,
    progressBarControl: null,
    pBContainer: null,
    pBKnob: null,
    pBKnobDragged: false,
    setNewPos: false,
    progressBar: null,
    volumeSlider: null,
    nowPlayingBar: null,
    sortControl: null,
    nextPrevControl: null,
    vcShowHide: null
  },

// Default Visual Effects
  visualEffects: {
    effects: {
    },

    effectStartPlaying: function(mplayer){
      this.effects.trackInfoSlide.hide();
    },

    effectPrev: function(mplayer){
    },

    effectNext: function(mplayer){
    },

    effectPlay: function(mplayer){
      this.effects.trackInfoSlide.slideIn();
    },

    effectPause: function(mplayer){
    },
    
    effectSort: function(mplayer){}
  },


  // Constructor
  initialize: function(playerElement, params, effects){
    // Player Container
    if($type(playerElement) == 'string')
      this.playerContainer = $(playerElement);
    else if($type(playerElement) == 'element')
      this.playerContainer = playerElement;

    // Initialize Effects
    this.initEffects(playerElement, effects);
    
    // Player width
    this.width = playerElement.getElement('.progressbar').getSize().x;
	
    // Initializing Audio Engine
    this.audioEngine = this.getAudioEngine();
    if($type(this.audioEngine) == 'string'){
      alert(this.audioEngine);
    } else {
      // Controls
      this.initializeControls();

      // Playlist
      this.playlist = this.playerContainer.getElement('.playlist');

      if(this.playlist){

/*
        // Default item playing
        if(this.playlist.getElement('.list_item_active'))
          this.startPlaying(this.playlist.getElement('.list_item_active'), {});
        else{
          var first = this.playlist.getElement('.list_item');
          first.addClass('list_item_active');
          this.startPlaying(first, {});
        }
*/


        // Binding Events to playlist's items on click it plays the current item
        var self = this;
        this.playlist.getElements('.list_item').addEvent('click', function(event){
          var target = $(event.target);
          var item = null;

          if(target.hasClass('list_item')) {
            item = target;
          } else
          if(target.hasClass('item_icon')) {
            return true;
          } else
          if(target.hasClass('audio_item_body')) {
            item = target.getParent();
          } else
          if(target.hasClass('item_title') || target.hasClass('item_description')|| target.hasClass('item_duration')) {
            item = target.getParent().getParent();
          } else{
            item = target.getParent().getParent().getParent();
          }

          self.clearRefreshIntervaler();
          self.startPlaying(item, {sid: (item.get('rel'))});
          return false;
        });
      } else {
        alert(self.err_templ + 'playlist is null');
      }
    }
  },

  // Set source of AudioElement with previous audio path from playlist and play if player is playing
  prev: function(){
    var prevTrack = this.playlist.getElement('.list_item_active').getPrevious('.list_item');

    if(!prevTrack && this.loopPlaylist){
      prevTrack = this.playlist.getElement('.list_item_active').getLast('.list_item');
    }
    else if(prevTrack)
      this.startPlaying(prevTrack, {sid: (prevTrack.get('rel'))});
    else
      this.pause();
  },

  // Set source of AudioElement with next audio path from playlist and play if player is playing
  next: function(){
    var nextTrack = this.playlist.getElement('.list_item_active').getNext('.list_item');
    if(!nextTrack && this.loopPlaylist){
      nextTrack = this.playlist.getFirst('.list_item');
      this.startPlaying(nextTrack, {sid: (nextTrack.get('rel'))});
    }
    else if(nextTrack)
      this.startPlaying(nextTrack, {sid: (nextTrack.get('rel'))});
    else
      this.pause();
  },

  /*
    If HTML5 audio supported by the browser and it can play mp3
    the function returns Html5AudioEngine otherwise FlashAudioEngine Class
  */
  getAudioEngine: function(){
    var engine;
    if(this.playerContainer){
        engine = new Html5AudioEngine(this.playerContainer.getElement('audio'));
      try{
      if(engine.canPlayMP3())
        return engine;
      else{
        return en4.core.language.translate('TOUCH_HTML5_AUDIO_DOESNT_SUPPORT_MP3');
      }

      } catch(e){
        return en4.core.language.translate('TOUCH_HTML5_AUDIO_DOESNT_SUPPORT');
      }
    }
    else{
      return this.err_templ + 'getAudioEngine() Before getting audioObject "player container" must be set.';
    }

  },

  // Initialize Controls
  initializeControls: function(){

    // Play pause button
    this.controls.playPauseBtn = this.playerContainer.getElement('.play_pause_btn');
    var self = this;
    this.controls.playPauseBtn.addEvent('click', function(event){
        self.playPause();
    });

    // ProgressBar blocks
    this.controls.pBContainer = this.playerContainer.getElement('.progressbar');
    this.controls.pBKnob = this.playerContainer.getElement('.slider');
    this.controls.pBKnob.addEvent('mousedown', function(){ self.controls.pBKnobDragged = true; self.controls.setNewPos = true;});
    this.controls.pBKnob.addEvent('mouseup', function(){ self.controls.pBKnobDragged = false;});
    this.controls.pBContainer.addEvent('mousedown', function(){ self.controls.pBKnobDragged = true; self.controls.setNewPos = true;});
    this.controls.pBContainer.addEvent('mouseup', function(){ self.controls.pBKnobDragged = false;});
    this.controls.progressBar = this.playerContainer.getElement('.progress_value_bar');
    this.controls.popOutBtn = this.playerContainer.getElement('.player_pop_in_out_btn');
    if($type(this.controls.popOutBtn))
      this.controls.popOutBtn.addEvent('click', function(){
        self.popInOut();
      });
    // Play Bar
    this.controls.nowPlayingBar = this.playerContainer.getElement('.now_playing');
    this.controls.nextPrevControl = this.playerContainer.getElement('.audio_info');
    this.controls.nextPrevControl.addEvent('swipe', function(event){
      if(event.direction=='left')
        self.next();
      else
        self.prev();
    });
    this.controls.vcShowHide = this.playerContainer.getElement('.volume');
    var cvcto = null;
    this.controls.volumeSlider =   new Slider(this.playerContainer.getElement('.volume_control_slider'), this.playerContainer.getElement('.volume_control_slider_knob'), {
        	range: [100, 0],
          wheel: true,
        	mode: 'vertical',
          onStart: function(step){
            clearTimeout(cvcto);
          },
        	onChange: function(step){
            self.audioEngine.setVolume(step/100);
        	},
          onComplete:function(step){
            clearTimeout(cvcto);
            self.audioEngine.setVolume(step/100);
            cvcto = window.setTimeout(
              function(){
                self.playerContainer.getElement('.volume_control').removeClass('active');
              }, 2000
            );
          }
        }).set(50);
    this.controls.vcShowHide.addEvent('click', function(e){
      var slider = self.playerContainer.getElement('.volume_control');
      if(slider.hasClass('active')){
        slider.removeClass('active');
      }else{
        slider.addClass('active');
      }
    });


/*
// Not on the current version

    this.controls.sortControl = new Sortables(this.playerContainer.getElement('div.playlist'),{
      clone: true,
      revert: true,
      opacity: 0
    }
    );
*/

  },

  initEffects: function(playerElement, effects) {
    if(playerElement)
      if(effects)
        this.visualEffects = effects;

      else { // If no custom effects apply default effects
        this.visualEffects.effects = {
          trackInfoSlide: new Fx.Slide(this.playerContainer.getElement('.audio_info'), {duration: 300})
        }
      }
  },
  // Toggle play and pause
  playPause: function(){
    if(this.controls.playPauseBtn.hasClass('paused')){
      try{
        if(!this.audioEngine.getMediaSource() || this.audioEngine.getMediaSource() == ''){
          var first = this.playlist.getElement('.list_item');
          first.addClass('list_item_active');
          this.startPlaying(first, {sid: (first.get('rel'))});
        } else
          this.play();
      } catch (e){
        throw e;
      }
    } else {
      try{
        this.pause();
      } catch (e) {
        throw e;
      }
    }
  },

  // Play or continue to play
  play: function(){
    this.audioEngine.play();
    if(!this.refreshIntervaler)
    var self = this;
    this.refreshIntervaler = setInterval(function (){if(!self){clearInterval();}self.refreshPlayingUI()}, this.refreshInterval);
    this.controls.playPauseBtn.removeClass('paused');
    if(!this.controls.playPauseBtn.hasClass('playing')){
      this.controls.playPauseBtn.addClass('playing');
    }

    // Apply the effect
    this.visualEffects.effectPlay(this);
    this.controls.playPauseBtn.removeClass('loading');
  },

  // Pause
  pause: function(){
    this.audioEngine.pause();
    this.clearRefreshIntervaler();
    this.controls.playPauseBtn.removeClass('playing');
    if(!this.controls.playPauseBtn.hasClass('paused')){
      this.controls.playPauseBtn.addClass('paused');
    }
  },

  // Refresh Player interface while playing (progressbar, time etc.) (invokes repeatedly only if audio is playing)
  refreshPlayingUI: function(){

    // Checking Ending of playing the audio item
    if(this.audioEngine.isEnded()){
      this.clearRefreshIntervaler();
      this.next();
    }
    // Getting current play time
    var currentPTNumber = this.audioEngine.getCurrentPlayTime();

    // Update ProgressBar Value;
    if(!this.controls.setNewPos)
      this.controls.progressBarControl.set(this.controls.progressBarControl.options.steps * currentPTNumber/this.audioEngine.getDuration())

    // Parsing seconds into Time string
    var currentTimeString = Math.floor(currentPTNumber/60) +':'+ (Math.floor(currentPTNumber%60)<10?'0'+Math.floor(currentPTNumber%60):Math.floor(currentPTNumber%60));

    // Update Time Bar
    this.controls.nowPlayingBar.getElement('.time').set('text', currentTimeString);
  },

  // Start playing audio at the beginning
  startPlaying: function(track, params){
    this.controls.playPauseBtn.addClass('loading');
    // Apply the effect
    this.visualEffects.effectStartPlaying(this);

    // Set playing track title

	  this.controls.nowPlayingBar.getElement('.audio_title').set('text', track.getElement('.item_title').innerHTML);

    track.getSiblings('.list_item').each(function(el){
      el.removeClass('list_item_active');
    });

    if(!track.hasClass('list_item_active')){
      track.addClass('list_item_active');
    }

    // Setting media source of the audio that will be played into engine
    this.audioEngine.setMediaSource(track, params);
    this.audioEngine.setCurrentPlayTime(0);

    // Waiting for load
    var self = this;
    var requestWait =0;
    var seeked = false;
    var seeking = setInterval(function(){
      if(requestWait>119){
        clearInterval(seeking);
        alert(en4.core.language.translate('TOUCH_Load time out'));
      }
      requestWait++;
      if(self.audioEngine.getDuration()>0){
        clearInterval(seeking);
        seeked = true;
        var steps = self.audioEngine.getDuration()*1000/self.refreshInterval;
        self.controls.progressBarControl = self.getProgressControl(self.audioEngine);
        self.play();
       self.incPlayCount(track);
      }
    }, 1000);
  },

  //
  getProgressControl: function(audioEngine){
    var steps = audioEngine.getDuration()*1000/this.refreshInterval;
    var self = this;
    return new Slider(
      this.controls.pBContainer,
      this.controls.pBKnob,
      {
        steps: steps,
        onChange:function(step){
          var value = (100*step/steps);
          self.controls.progressBar.setStyle('width', value+'%');
        },

        onComplete: function(step){
          if(self.controls.setNewPos){
            var value = step/steps;
            self.audioEngine.setCurrentPlayTime(value*self.audioEngine.getDuration());
            self.controls.setNewPos = false;
          }
        }
			}).set(0);
  },

  clearRefreshIntervaler: function(){
    if(this.refreshIntervaler)
      clearInterval(this.refreshIntervaler);
    this.refreshIntervaler = null;
  },
  incPlayCount: function(track, params){
    if(!$type(track.getElement('.item_duration b')))
      return;
    var song_id = parseInt(track.get('rel'));
    var playlist_id = parseInt(this.playlist.get('id'));

    // Tally song
    if( !this.tallied[song_id] ) {
      this.tallied[song_id] = true;
      new Request.JSON({
        url: $$('head base[href]')[0].get('href') + 'music/song/' + song_id + '/tally',
        noCache: true,
        data: {
          format: 'json',
          song_id: song_id,
          playlist_id: playlist_id
        },
        onSuccess: function(responseJSON) {
          if( responseJSON &&
              $type(responseJSON) == 'object' &&
              'song' in responseJSON &&
              'play_count' in responseJSON.song ) {
            track.getElement('.item_duration b')
              .set('text', responseJSON.play_count);
          }
        }.bind(this)
      }).send();
    }
  },
  popInOut: function(){
    var self = this;
    var btn = this.controls.popOutBtn;
    var global_header = $('global_header');
    var global_footer = $('global_footer');
    var comments = $('comments');
    var playlist_header = this.playerContainer.getElement('.playlist_header');
    var player_controls = this.playerContainer.getElement('.player_controls');

    if(!this.playerContainer.hasClass('player_pop_out')){
      this.playerContainer.addClass('player_pop_out_effect');
      global_header.setStyle('display', 'none');
      global_footer.setStyle('display', 'none');
      comments.setStyle('display', 'none');
      if($(document.body).getHeight() < this.playerContainer.getHeight()){
        playlist_header.addClass('header_fixed');
        player_controls.addClass('controls_fixed');
        this.playlist.addClass('playlist_add_margin');
      }
      this.playerContainer.addClass('player_pop_out');
      btn.removeClass('normal');
      btn.addClass('pop_out');
      setTimeout(function(){ self.playerContainer.removeClass('player_pop_out_effect');}, 200);
    } else {
      this.playerContainer.addClass('player_to_normal_effect');
      global_header.setStyle('display', 'block');
      global_footer.setStyle('display', 'block');
      comments.setStyle('display', 'block');
      this.playerContainer.removeClass('player_pop_out');
      btn.removeClass('pop_out');
      btn.addClass('normal');
      playlist_header.removeClass('header_fixed');
      player_controls.removeClass('controls_fixed');
      this.playlist.removeClass('playlist_add_margin');
      setTimeout(function(){ self.playerContainer.removeClass('player_to_normal_effect');}, 200);
    }
  }
});