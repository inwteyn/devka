
/* $Id: composer_tag.js 7244 2010-09-01 01:49:53Z john $ */

Wall.Composer.Plugin.Checkin = new Class({

  Extends : Wall.Composer.Plugin.Interface,
  name : 'checkin',

  checkin_enabled: true,
  edit_mode: false,
  navigator_shared: false,
  suggest: {},
  position: {},
  data: {},
  $checkin_cont: {},
  $share_btn: {},
  $share_info: {},
  choices: {}, 
  $loader: {},
  scroll: {},
  markerevent: false,
  select_location_btn: {},

  blur_state: false,

  initialize : function(options) {
    this.select_location_btn = new Element('button', {'class': 'select_location_btn', 'html': en4.core.language.translate('TOUCH_Select')});
    var self = this;
    this.params = new Hash(this.params);
    this.parent(options); 
    window.hehe = this;
    this.scroll = new Fx.Scroll(document.body, {
            wait: false,
            duration: 500,
            offset: {'x': 0, 'y': -50},
            transition: Fx.Transitions.Quad.easeInOut
          });
    this.select_location_btn.addEvent("click", function(){
      var choice = this.getParent().getParent();
      if(choice.hasClass('autocompleter-choices'));
        self.onChoiceSelect(choice);
    });
  },

  attach : function() {
    var self = this;
    this.$checkin_cont = this.getComposer().container.getElement('.checkinWallShareLocation');
    this.$share_btn = $(document.body).getElement('.checkinShareLoc');
    this.getComposer().container.getElement('.submitMenu').grab(this.$checkin_cont.getElement('.share_loc_btn'));
    this.$share_info = $(document.body).getElement('.checkinLocationInfo');
    this.$share_edit_info = $(document.body).getElement('.checkinEditLocation');
    this.$loader = $(document.body).getElement('.checkinLoader');
    // Submit
    this.getComposer().addEvent('editorSubmit', function (){
      self.linkAttached = false;
      if (self.checkin_enabled && self.isValidPosition()) {
        var checkin_hash = new Hash(self.position);
        self.getComposer().makeFormInputs({checkin: checkin_hash.toQueryString()});
      } else {
        self.getComposer().makeFormInputs({checkin: ''});
      }
    });

    this.$share_btn.addEvent('click', function() {
      this.blur();
      if (!self.navigator_shared) {
        $(document.body).getElement('.checkinWallShareLocation').removeClass('display_none');
        self.toggleLoader(true);
        if (!Browser.Platform.ipod) {
          self.enableLocation();
        } else {
          self.enableLocationIpod();
        }
        return;
      }

      if (self.isValidPosition() && !self.checkin_enabled) {
        self.toggleCheckin(true);
      } else {
        self.toggleCheckin(false);
      }
    });

    this.$share_info.addEvent('click', function() {
      self.edit_mode = true;
      self.editLocation();
    });
    var resp = true;
    this.$share_edit_info.removeEvents('blur');
    this.$share_edit_info.removeEvents('keypress');
    this.$share_edit_info.removeEvents('keydown');
    var time;
    var rto;
    this.$share_edit_info.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', function(e) {
      if (!['esc', 'tab', 'up', 'down', 'enter'].contains(e.key)) {
        this.addClass('checkinLoader');
        var edit_share = this;

    /*ulans Request*/
        var data = self.getLocation();
        data.keyword = this.get('value');
        var re = new Request.HTML({
          url: en4.core.baseUrl + 'checkin/index/suggest/format/json',
          noCache: true,
          data: data,
          evalScripts: false,
          onRequest: function(){
            resp = null;
          },
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            if( responseHTML) {
              resp = JSON.decode(responseHTML);
              for(var i = 0; i< resp.length; i++){
              }
              self.autoComplete(resp);
              edit_share.removeClass('checkinLoader');
            }
          }
        });
        var now = new Date().getTime();
        if(data.keyword.length >0){
          if(now - time >500){
            re.send();
            time = now;
          }
          else{
            clearTimeout(rto);
            rto = setTimeout(function(){
              if(now - time >= 500){
                re.send();
              }
            }, 500);
            time = now;
          }
        } else {
          this.removeClass('checkinLoader');
        }
    /*ulans Request*/

      }
    });
/*
    this.$share_edit_info.addEvent('blur', function(){
      self.suggestTimeout = window.setTimeout(function() {
        self.onBlur(self.$share_edit_info);
       self.toggleLoader(false);
      }, 500);
    });
*/
    this.choices = this.getComposer().container.getElement('.checkin_choice_cont_tpl').clone();
    this.choices.removeClass('checkin_choice_cont_tpl').addClass('checkin-autosuggest-cont');
    this.choices .inject(this.getComposer().container.getElement('form'));


    return this;
  },

  detach : function() {

    this.toggleCheckin(false);
    this.navigator_shared = false;
    this.$share_edit_info.getParent().addClass('display_none');
    this.$share_info.getParent().addClass('display_none');
    this.choices.setStyle('display', 'none');
    $(document.body).getElement('.checkinWallShareLocation').addClass('display_none');
    return this;
  },

  toggleCheckin: function(enable) {
    if (enable) {
      this.checkin_enabled = true;
      this.$share_btn.addClass('checkinShareLocAct');
      this.$share_info.removeClass('disabled');

    } else {
      this.checkin_enabled = false;
      this.$share_btn.removeClass('checkinShareLocAct');
      this.$share_info.addClass('disabled');
    }
  },

  enableLocation: function() {

      var data = {
        'accuracy': 0,
        'latitude': 0,
        'longitude': 0,
        'name': '',
        'vicinity': ''
      };

    //turn;
    this.setPosition(data);

    var self = this;
    var positionTimeLimit = 3000;
    this.navigator_shared = true;

    var positionTimeout = window.setTimeout(function() {
      try {
        navigator.geolocation.clearWatch(self.watchID);
        console.log('Watch Position Failed');
      } catch (e) {}
      self.toggleLoader(false);

      var data = {
        'accuracy': 0,
        'latitude': 0,
        'longitude': 0,
        'name': '',
        'vicinity': ''
      };

      self.setPosition(data);
    }, positionTimeLimit);

    try {
      console.log('Start Watch Position');
      self.watchID = navigator.geolocation.watchPosition(function(position) {
        window.clearTimeout(positionTimeout);
        console.log('Watch Position Success!');
        navigator.geolocation.clearWatch(self.watchID);
        self.toggleLoader(false);
        self.checkin_enabled = true;
        var delimiter = (position.address && position.address.street != '' && position.address && position.address.city != '') ? ', ' : '';
        if(position.address){
          var data = {
            'accuracy': position.coords.accuracy,
            'latitude': position.coords.latitude,
            'longitude': position.coords.longitude,
            'name': position.address.street + delimiter + position.address.city,
            'vicinity': position.address.street + delimiter + position.address.city
          };
          self.setPosition(data);
        }

      });
    } catch (e) {}
  },

    enableLocationIpod: function() {
    var self = this;
      self.toggleLoader(false);
      return;
    var positionTimeLimit = 6000;
    this.navigator_shared = true;

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position){
          var data = {
            'accuracy': position.coords.accuracy,
            'latitude': position.coords.latitude,
            'longitude': position.coords.longitude,
            'name': (position.address) ? (position.address.street + delimiter + position.address.city) : '',
            'vicinity': (position.address) ? (position.address.street + delimiter + position.address.city) : ''
          };

          if (data.name.length == 0 && data.latitude && data.longitude) {
            var latLong = new google.maps.LatLng( data.latitude, data.longitude);
            var map = new google.maps.Map(new Element('div'), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: latLong, zoom: 15});

            var request = {location: latLong, radius: 500};

            service = new google.maps.places.PlacesService(map);
            service.search(request, function(results, status) {
              self.toggleLoader(false);
              self.checkin_enabled = true;

              if (status == 'OK') {
                data.name = results[0].name;
                data.vicinity = results[0].vicinity;
              }

              self.setPosition(data);
            });

          } else {
            self.toggleLoader(false);
            self.checkin_enabled = true;
            self.setPosition(data);
          }

        },
        function(msg) {
          self.toggleLoader(false);

          var data = {
            'accuracy': 0,
            'latitude': 0,
            'longitude': 0,
            'name': '',
            'vicinity': ''
          };

          self.setPosition(data);
        }
      );
    }
  },

  setPosition: function(position) {
    this.edit_mode = false;
    this.position = position;
    if (this.isValidPosition(position)) {
      var checkin_hash = new Hash(position);
      this.checkin_enabled = true;
    } else {
      this.checkin_enabled = false;
    }


    this.$share_info.set('text', this.getLocationText());
    this.$share_btn.addClass('checkinShareLocAct');
    this.checkStatus();
  },

  editLocation: function() {
    var locationValue = this.getLocationText();
    locationValue = (locationValue == this.$share_info.get('text')) ? '' : locationValue;
    this.$share_edit_info.set('value', locationValue);
    this.$share_info.getParent().addClass('display_none');
    this.$share_edit_info.getParent().removeClass('display_none');

    this.$share_edit_info.focus();
  },

  toggleLoader: function(show) {
    show = (show != 'undefined' && show) ? show : false;
    if (show) {
      this.$share_info.getParent().addClass('display_none');
      this.$loader.getParent().removeClass('display_none');
    } else {
      this.$loader.getParent().addClass('display_none');
      this.$share_edit_info.getParent().addClass('display_none');
      this.$share_info.getParent().removeClass('display_none');
    }
  },

  getLocationText: function() {
    if (this.position.name) {
      var locationText = this.position.name;
    } else {
      var locationText = this.$share_info.get('text');
    }

    return locationText;
  },

  getLocation: function() {
    var location = {'latitude': 0, 'longitude': 0};

    if (this.isValidPosition(false, true)) {

      location.latitude = this.position.latitude;
      location.longitude = this.position.longitude;
    }
    return location;
  },

  checkStatus: function() {
    if (this.isValidPosition()) {
      this.$share_btn.addClass('checkinShareLocAct');
      this.$share_info.removeClass('disabled');
      return true;
    } else {
      this.$share_btn.removeClass('checkinShareLocAct');
      this.$share_info.addClass('disabled');
      return false;
    }
  },

  isValidPosition: function(position, check_coordinates) {
    var position = (position) ? position : this.position;
    var isValid = (check_coordinates)
      ? (position && position.latitude && this.position.longitude)
      : (position && position.name != undefined && position.name != '')

    return isValid;
  },

  showSelectedMarker: function(user_checkin, $choice) {
    var self = this;
    var $checkin_map = this.choices.getElement('.checkin-autosuggest-map');
    $choice.addClass('load');
    $choice.getElement('.autocompleter-choice').getElement('.checkin_choice_label').grab(this.select_location_btn, 'before');

    // effects
    if(user_checkin.latitude != undefined){
      if($choice.hasClass('collapse')){
        $checkin_map.addClass('display_none');
        $choice.removeClass('collapse');
        $choice.addClass('expand');
      } else {
        $checkin_map.removeClass('display_none');
        $choice.addClass('collapse');
        $choice.removeClass('expand');
      }

    }
    // effects

    if (user_checkin.latitude == undefined) {
      var map = new google.maps.Map(new Element('div'), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: new google.maps.LatLng(0, 0), zoom: 15});
      var service = new google.maps.places.PlacesService(map);
      var d= service.getDetails({'reference': user_checkin.reference}, function(place, status) {
        if (status == 'OK') {
          user_checkin.name = place.name;
          user_checkin.google_id = place.id;
          user_checkin.latitude = place.geometry.location.lat();
          user_checkin.longitude = place.geometry.location.lng();
          user_checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
          user_checkin.icon = place.icon;
          user_checkin.types = place.types.join(',');

          $choice.store('autocompleteChoice', user_checkin);
          self.showSelectedMarker(user_checkin, $choice);
        }
      }); 
      return;
    }

    // effects
    var choice_arr = this.choices.getElement('.checkin-autosuggest').getElements('.autocompleter-choices');
    var collapse = 'clear';
    if($choice.hasClass('collapse'))
      collapse = true;
    else
      collapse = false;

    choice_arr.removeClass('load');
    choice_arr.removeClass('active');
    choice_arr.removeClass('collapse');
    choice_arr.removeClass('expand');
    $choice.addClass('active');
    if(collapse != 'clear' && collapse)
      $choice.addClass('collapse');
    else if(collapse != 'clear')
      $choice.addClass('expand');
    $checkin_map.inject($choice, 'after');
    this.scroll.toElement($checkin_map);
    // effects

    var myLatlng = new google.maps.LatLng(user_checkin.latitude, user_checkin.longitude);
    var new_map = false;
    if (this.map == undefined || !$checkin_map.getFirst()) {
      new_map = true;
      this.map = new google.maps.Map($checkin_map, {mapTypeId: google.maps.MapTypeId.ROADMAP, center: myLatlng, zoom: 15});
    }
  var marker_options = {position: myLatlng, map: this.map};
    if (new_map) {
      this.marker = new google.maps.Marker(marker_options);
      this.map.setCenter(myLatlng);
    } else {
      this.marker = (this.marker == undefined) ? new google.maps.Marker(marker_options) : this.marker;
      this.marker.setPosition(myLatlng);
      this.map.panTo(myLatlng);
    }

    this.bindMapEvents();
  },
  bindMapEvents: function() {
    var self = this;
    if(!this.markerevent){
      google.maps.event.addListener(this.marker, 'click', function(){
        confirm('Are you here?');
      });
      this.markerevent = true;
    }
  },
  activate: $empty,
  deactivate : $empty,
  autoComplete : function(response) {
    var temp = this.choices.getElement('.checkin-autosuggest-map').addClass('display_none');
    this.choices.grab(temp);
    this.choices.getElement('.checkin-autosuggest').set('html', '');
    this.choices.setStyle('display', 'block');
    var token;
    for(var i = 0; i < response.length; i++)
    {
      token = response[i];
      this.injectChoice(token);
    }
  },
  injectChoice: function(token){
    var $choice = this.getComposer().container.getElement('.checkin_choice_tpl').clone();

    $choice.setProperty('id', token.id);
    $choice.removeClass('checkin_choice_tpl').addClass('autocompleter-choices');
    $choice.getElement('.checkin_choice_icon').setProperty('src', (token.icon) ? token.icon : en4.core.staticBaseUrl + 'application/modules/Touch/modules/Checkin/externals/images/map_icon.png');
    $choice.getElement('.checkin_choice_label').set('html',
    /*this.markQueryValue(*/
      token.name
    /*)*/
    );

    var $choice_list = this.choices.getElements('.checkin-autosuggest');
    if ($choice_list.length == 0) {
      this.choices.set('html', this.getComposer().container.getElement('.checkin_choice_cont_tpl').innerHTML);
    }

    this.choices.setStyle('width', this.getComposer().container.getCoordinates().width);
    this.choices.getElement('.checkin-autosuggest').setStyle('width', this.getComposer().container.getCoordinates().width);
    var self= this;
    $choice.addEvent('click', function(e){
      if(!$(e.target).hasClass('select_location_btn'))
        self.onSelect($choice);
    }); 
    $choice.inject(this.choices.getElement('.checkin-autosuggest'));
    $choice.store('autocompleteChoice', token);
    var count = this.choices.getElements('.checkin-autosuggest li').length;

  },

  onSelect: function(choice) {
    var user_checkin = choice.retrieve('autocompleteChoice');
    this.showSelectedMarker(user_checkin, choice);
  },
  onBlur: function(input){
    if (this.blur_state) {
      return;
    }

    this.blur_state = true;

    this.suggestTimeout = window.setTimeout(function() {
      if (input.value != '') {
        this.setPosition(this.position);
      } else {
        this.setPosition({'accuracy': 0, 'latitude': 0, 'longitude': 0, 'name': '', 'vicinity': ''});
      }

      input.removeClass('checkinLoader');
      this.suggest.toggleFocus(false);
      this.toggleLoader(false);
    }, 500);
  },
  onChoiceSelect: function($choice) {
    var self = this;
    var user_checkin = $choice.retrieve('autocompleteChoice');
    if (user_checkin.latitude == undefined) {
      var map = new google.maps.Map(new Element('div'), {mapTypeId: google.maps.MapTypeId.ROADMAP, center: new google.maps.LatLng(0, 0), zoom: 15});
      var service = new google.maps.places.PlacesService(map);
      service.getDetails({'reference': user_checkin.reference}, function(place, status) {
        if (status == 'OK') {
          user_checkin.name = place.name;
          user_checkin.google_id = place.id;
          user_checkin.latitude = place.geometry.location.lat();
          user_checkin.longitude = place.geometry.location.lng();
          user_checkin.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
          user_checkin.icon = place.icon;
          user_checkin.types = place.types.join(',');

          $choice.store('autocompleteChoice', user_checkin);
        }
      });
    }

    this.setPosition(user_checkin);
    this.toggleLoader(false);
   // this.navigator_shared = false;
    this.$share_edit_info.getParent().addClass('display_none');
//    this.$share_info.getParent().addClass('display_none');
    this.choices.setStyle('display', 'none');
    this.scroll.toElement(this.$checkin_cont);
  }
});