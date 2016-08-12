/**
 * Author: Ulan
 * Date: 26.09.11
 * Time: 12:28
 * To change this template use File | Settings | File Templates.
 */
var Html5AudioEngine = new Class({

  audioObject: null,
  interval: 0.1,
  unmute_volume: null,
  err_templ: 'Error: Html5AudioEngine.',

	initialize: function(audio_element_id){
		this.audioObject = $(audio_element_id);
    this.audioObject.volume = .5;
	},

	play: function(){
    try{
      this.audioObject.play();
      return true;
    }catch(e){
      alert(this.err_templ + 'play() the media cannot be played')
      throw e;
      return false;
    }
  },

	pause: function(){
    this.audioObject.pause();
  },

	stop: function(){
    this.audioObject.pause();
    this.audioObject.currentTime=0;
  },

	volumeUp: function(){
    if(this.audioObject.volume <= 1 - this.interval)
      this.audioObject.volume += this.interval;
    else
      this.audioObject.volume = 1;
  },
  getVolume: function(){
       return this.audioObject.volume;
   },

  setVolume: function(volume){
    if(volume>=1)
       return this.audioObject.volume = 1;
    else if(volume<=0)
      return this.audioObject.volume = 0;
    return this.audioObject.volume = volume;
   },

	volumeDown: function(){
    if(this.audioObject.volume >= this.interval)
      this.audioObject.volume -= this.interval;
    else
      this.audioObject.volume = 0;
  },

	mute: function(ismute){
    if(ismute){
      this.unmute_volume = this.audioObject.volume;
      this.audioObject.volume = 0;
    }
    else if(this.audioObject.volume == 0){
      this.audioObject.volume = this.unmute_volume;
    }
  },

	setCurrentPlayTime: function(play_time){
    if(this.audioObject.duration > play_time && play_time > 0){
      this.audioObject.currentTime = play_time;
      return true;
    }
    else{
      //alert(this.err_templ + "setCurrentPlayTime(play_time) invalid parameter value "+play_time);
      return false;
    }
  },

	loop: function(isloop){
    try{
      if(isloop){
        this.audioObject.set('loop', 'true');
      } else {
        this.audioObject.erase('loop');
      }
      return true;
    } catch (e){
      alert(this.err_templ + "loop(isloop) invalid parameter value "+isloop);
      throw e;
      return false;
    }
  },

	getDuration: function(){
    return this.audioObject.duration;
  },

	getCurrentPlayTime: function(){
    return this.audioObject.currentTime;
  },

	getMediaType: function(){

  },

	isAutoBuffer: function(){},

	isAutoplay: function(){},

  getMediaSource: function(){
    return this.audioObject.src;
  },

  setMediaSource: function(new_source, params){
      if($type(new_source) == 'element')
        new_source= new_source.get('href');
    try{
      this.audioObject.src = new_source;
      if(!params){
        this.play();
      }
    } catch (e){
     // alert(this.err_templ + 'setMediaSource(new_source) source url is invalid.\n' + new_source);
      throw e;
      return false;
    }
  },

  getAudioElement: function(){
    if(!this.audioObject){
      alert(this.err_templ + 'Audio Element is null');
      return false;
    }
    return this.audioObject;
  },
  isEnded: function(){
    return this.audioObject.ended;
  },
  canPlayMP3: function(){
    var can = this.audioObject.canPlayType ? this.audioObject.canPlayType('audio/mpeg'): '';
    return can == '' || !can? false : true;
  }
});