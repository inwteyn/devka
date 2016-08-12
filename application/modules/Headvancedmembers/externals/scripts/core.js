/**
  $Id: core.js 2015-10-13 01:44 bolot $
 */
var HeadvancedMembers = {};
HeadvancedMembers.init  = function(){
  var body_element = $$('body')[0];
  this.bg.inject(body_element);
  this.contaner.inject(body_element);
  this.rebiltButtons();
};
HeadvancedMembers.bg = new Element('div',{
  'class':'bg_form_headvuser',
  'id': 'bg_form_headvuser',
  'style':'position:fixed; top:0px; left:0px; width: 100%; height: 100%; background-color:rgba(0,0,0,0.4);display:none;z-index:999;'
});
HeadvancedMembers.contaner = new Element('div',{
  'class':'container_form_headvuser',
  'id': 'container_form_headvuser',
  'style':'position:absolute; top:100px; left:40%; width: 400px; min-height: 200px; background-color:#fff;display:none;z-index:1000;'
});

HeadvancedMembers.request  = function (url, data, callback) {
  if (typeof(data) == 'object') {
    data.format = 'json';
  } else if (typeof(data) == 'string') {
    data += '&format=json';
  }
  HeadvancedMembers.is_request = true;

  (new Request.JSON({
    secure: false,
    url: url,
    method: 'post',
    data: data,
    onSuccess: function (obj) {
      // callback
      if ($type(callback) == 'function') {
        callback(obj);
      }
    }
  })).send();

};
HeadvancedMembers.html  = function (url, data, callback) {
  if (typeof(data) == 'object') {

  } else if (typeof(data) == 'string') {
    data += '&format=json';
  }
  HeadvancedMembers.is_request = true;

  (new Request.HTML({
    secure: false,
    url: url,
    method: 'post',
    data: data,
    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

      // callback
      if ($type(callback) == 'function') {
        callback(responseHTML);
      }
    }
  })).send();

};
HeadvancedMembers.search = function(element){
  var self = this;
  var data  = element.value;
  var url = en4.core.baseUrl + 'headvancedmembers/index/search';
  if(data.length>2){
    $('hememberslist1').setStyle('display','block');
    $('hememberslist1').set('html','loading...');
    var post = {'search':data, 'type_view':type_view, 'format':'html'};
    self.html(url,post,function(ebg){
      var error = 0;
      var type = '';
      $('hememberslist').setStyle('display','none');
      $('hememberslist1').setStyle('display','block');
      $('hememberslist1').set('html',ebg);
    });
  }else{
    $('hememberslist').setStyle('display','block');
    $('hememberslist1').setStyle('display','none');
    $('hememberslist1').set('html','');
  }
};
HeadvancedMembers.rebiltButtons = function(){
    var self = this;
  $('search_headvanced_members').addEvent('keyup',function(){
    HeadvancedMembers.search(this);
  });
    $$('.headvuser_button').each(function(button){
      var element =  $(button);
      var href =  element.get('href');
      var url = href.replace('/members/','/headvancedmembers/');
      element.removeEvent('click');
      var loader = new HeadvancedMembers.loader();
      element.addEvent('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var parent = this.getParent();
        if(parent) {
          parent.set('html', '<img class="irc_mi" style="width:16px;height:16px;" src="' + en4.core.baseUrl + 'application/modules/Headvancedmembers/externals/images/loading.gif" width="16" height="16" title="loading ">');
        }
        self.request(url,'',function(ebg){
          var error = 0;
          var type = '';
          if(ebg.message){
            var message = ebg.message;
          }else{
            error = 1;
            var message = en4.core.language.translate('An error has occurred.');
          }
          if(error>0){
            he_show_message(message,type);
            type = 'error';
          }else{
          if(parent){
            parent.set('html',ebg.body);
            he_show_message(message,type);
            HeadvancedMembers.rebiltButton(parent);
          }
            if($('hememberslist') && (370*3) >=$('hememberslist').getSize().x){
              $$('#browsemembers_ul_normal_advhe li').each(function(element){
                element.setStyle('width','44%')
              })
            }
          }
        });
      })
      });

};
HeadvancedMembers.rebiltButton = function(parent_b){
    var self = this;
      var element =  parent_b.getChildren('a')[0];
      var href =  element.get('href');
      var url = href.replace('/members/','/headvancedmembers/');
      element.removeEvent('click');
      var loader = new HeadvancedMembers.loader();
      element.addEvent('click', function (e) {
        self.contaner.set('html','loading');
        e.stopPropagation();
        e.preventDefault();
        var parent = this.getParent();
        if(parent) {
          parent.set('html', '<img class="irc_mi" style="width:16px;height:16px;" src="' + en4.core.baseUrl + 'application/modules/Headvancedmembers/externals/images/loading.gif" width="16" height="16" title="loading ">');
        }
        self.request(url,'',function(ebg){
          var error = 0;
          var type = '';
          if(ebg.message){
            var message = ebg.message;
          }else{
            type = 'error'
            error = 1;
            var message = 'An error has occurred.';
          }
          if(error>0){
            he_show_message(message,type);
          }else{
          if(parent){
            parent.set('html',ebg.body);
            he_show_message(message,type);
            HeadvancedMembers.rebiltButton(parent);
          }

          }
        });
      })
      ;

};
HeadvancedMembers.loader =  new Class({

  bg: HeadvancedMembers.bg,
  element: HeadvancedMembers.contaner,


  hide: function () {
    if (this.element) {
      $(this.element).setStyle('display', 'none');
    }
    if (this.bg) {
      $(this.bg).setStyle('display', 'none');
    }
  },

  show: function () {
    var self = this;
    this.bg.addEvent('click',function(){
      self.bg.setStyle('display','none');
      self.element.setStyle('display','none');
      self.element.set('html','');
    });
    if (this.element) {
      $(this.element).setStyle('display', 'block');
    }
    if (this.bg) {
      $(this.bg).setStyle('display', 'block');
    }
  }

});
//========================================================================



var pages_map =
{
  pages_array: {},
  markers_array: {},
  map_bounds: {},
  current_data: {},
  zoom: 4,
  constructed: false,
  canvas_id: 'map_canvas',

  construct: function (pages_array, markers_array, zoom, map_bounds, edit_mode) {
    this.pages_array = pages_array;
    this.markers_array = markers_array;
    this.map_bounds = map_bounds;
    this.zoom = zoom;
    this.init();

    if (edit_mode == undefined && !edit_mode) {
      this.show_map();
    } else {
      this.show_edit_map();
    }

    this.constructed = true;
  },

  init: function () {
    window.map = new google.maps.Map(document.getElementById('map_canvas'), {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: false
    });
  },

  show_map: function () {
    var self = this;
    if (this.markers_array.length == 0) {
      return false;
    }

    //this.clearOverlays();

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    Object.each(this.markers_array, function(value, key){
      marker = value;
      point = new google.maps.LatLng(marker.lat, marker.lng);
      marker_obj = new google.maps.Marker({
        position: point,
        map: window.map
      });


      marker_obj.html1 =
          '<table style="display: block; width: 500px;">' +
          '<tr valign="top">' +
          '<td width="110px"><img src="' + marker.pages_photo + '" width="100"></td>' +
          '<td style="width: 300px; display: block; margin-left: 5px;">' +
          '<h3 style="margin-top:0; margin-bottom:6px;">' +
          '<a href="' + marker.url + '">' + marker.title + '</a>' +
          '</h3>' +marker.map_rate+ marker.desc + '</td><td width="60px">';

      marker_obj.html2 = '</td></tr>' +
      '</table>';
      marker_obj.page_id = key;

      if (!marker.desc) {
        marker.desc = '';
      }

      google.maps.event.addListener(marker_obj, 'mouseover', function () {
        var likeTemp = self.markers_array[this.page_id].map_like;
        if (typeof self.markers_array[this.page_id].map_like == 'string') {
          likeTemp = Elements.from(self.markers_array[this.page_id].map_like);
        }
        var tempElement = new Element('div');
        likeTemp.inject(tempElement);
        var content = this.html1 + tempElement.innerHTML + this.html2;
        infoWindow.setContent(content);
        infoWindow.open(window.map, this);
        list_like.init_list_like_buttons(key);
      });
    });


    window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
    window.map.setZoom(4);
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      window.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng));
      window.map.setZoom(4);
    } else {
      window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      window.map.setZoom(this.zoom);
    }
    if (bds) {
      window.map.fitBounds(bds);
    }
  },

  show_edit_map: function () {
    if (this.markers_array.length == 0) {
      return false;
    }
    var self = this;

    //this.clearOverlays();

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    for (var i = 0; i < this.markers_array.length; i++) {
      marker = this.markers_array[i];
      point = new google.maps.LatLng(marker.lat, marker.lng);
      marker_obj = new google.maps.Marker({
        position: point,
        map: window.map,
        draggable: true,
        identity: i
      });

      marker_obj.html = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.pages_photo + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;"><a href="' + marker.url + '">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';

      if (!marker.desc) {
        marker.desc = '';
      }

      google.maps.event.addListener(marker_obj, 'click', function () {
        infoWindow.setContent(this.html);
        infoWindow.open(window.map, this);
      });

      google.maps.event.addListener(marker_obj, 'dragend', (function (marker_obj) {
        return function () {
          self.trackChanges(marker_obj);
        };
      })(marker_obj));

    }
    window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));

    window.map.setZoom(4);
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      window.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng));
      window.map.setZoom(4);
    } else {
      window.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      window.map.setZoom(this.zoom);
    }
    if (bds) {
      window.map.fitBounds(bds);
    }

  },

  showEditMap: function (pages_array, markers_array, zoom, map_bounds, edit_mode) {
    $$('.page_edit_form_map_show').addClass('display_none');
    $$('.page_edit_form_map_hide').removeClass('display_none');
    $('map_canvas').removeClass('display_none');

    if (!this.constructed) {
      this.construct(pages_array, markers_array, zoom, map_bounds, edit_mode);
    }
  },

  hideEditMap: function () {
    $$('.page_edit_form_map_show').removeClass('display_none');
    $$('.page_edit_form_map_hide').addClass('display_none');
    $('map_canvas').addClass('display_none');
  },

  trackChanges: function (marker) {
    var coords = marker.getPosition();
    var marker_id = marker.identity;
    if(!marker_id){
      $('coordinates').value = coords.lat() + ';' + coords.lng();
    } else {
      $('coordinates_' + marker_id).value = coords.lat() + ';' + coords.lng();
    }

    geocoder = new google.maps.Geocoder();

    geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          var address = csvToArray(results[0].formatted_address, ',');
          var index = address.length - 1;

          if (index >= 0) {

            if(!marker_id){
              $('country').value = address[index].trim();
            } else {
              $('additional_country_'+marker_id).value = address[index].trim();
            }
            index--;
          }

          if (index >= 0) {
            var len = results[0].address_components.length, i;
            for (i = len - 1; i > 0; i--) {
              if (results[0].address_components[i].types[0] == 'country') {
                i--;
                if(!marker_id){
                  $('state').value = results[0].address_components[i].long_name;
                } else {
                  $('additional_state_'+marker_id).value = results[0].address_components[i].long_name;
                }
                break;
              }
            }
            index--;
          }


          if (index >= 0) {
            if(!marker_id){
              $('city').value = address[index].trim();
            } else {
              $('additional_city_'+marker_id).value = address[index].trim();
            }
            index--;
          }


          if (index >= 0) {
            if(!marker_id){
              $('street').value = address[index].trim();
            } else {
              $('additional_street_'+marker_id).value = address[index].trim();
            }
            index--;
          }
        }
      }
    });
  }
};

var my_location = {
  my_lat: '',
  my_lon: '',
  my_request: '',
  my_map: '',
  my_url: '',
  my_marker: '',
  my_location_page_num: 1,
  my_location_address: '',

  init: function (myurl) {
    var self = this;
    self.my_url = myurl;
    self.my_map = new google.maps.Map(document.getElementById("my_map_canvas"), {
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: false,
      center: new google.maps.LatLng(37.0902400, -95.7128910),
      zoom: 3
    });

    self.my_marker = new google.maps.Marker({
      map: self.my_map,
      animation: google.maps.Animation.BOUNCE,
      html: 'My Location!',
      title: 'My Location!'
    });
  },

  show_pages: function (markers, bounds) {
    var self = this;
    var marker;
    if (markers.length == 0) {
      return false;
    }

    //this.clearOverlays();

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    for (var i = 0; i < markers.length; i++) {
      marker = markers[i];
      var point = new google.maps.LatLng(marker.lat, marker.lng);
      var marker_obj = new google.maps.Marker({
        position: point,
        map: self.my_map
      });

      marker_obj.html = '<table width="500px"><tr valign="top"><td width="110px"><img src="' + marker.pages_photo + '" width="100"></td><td width="400px"><h3 style="margin-top:0; margin-bottom:6px;"><a href="' + marker.url + '">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';
      if (!marker.desc) {
        marker.desc = '';
      }

      google.maps.event.addListener(marker_obj, 'click', function () {
        infoWindow.setContent(this.html);
        infoWindow.open(self.my_map, this);
      });

    }

    self.my_map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
    self.my_map.setZoom(4);
    if (bounds && bounds.min_lat && bounds.max_lng && bounds.min_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds && bounds.map_center_lat && bounds.map_center_lng) {
      self.my_map.setCenter(new google.maps.LatLng(bounds.map_center_lat, bounds.map_center_lng));
      self.my_map.setZoom(4);
    } else {
      self.my_map.setCenter(new google.maps.LatLng(marker.lat, marker.lng));
      self.my_map.setZoom(this.zoom);
    }
    if (bds) {
      self.my_map.fitBounds(bds);
    }
  },
  set_my_marker: function (latitude, longitude) {
    var self = this;

    self.my_lat = latitude;
    self.my_lon = longitude;

    var point = new google.maps.LatLng(self.my_lat, self.my_lon);
    self.my_marker.setPosition(point);

    self.my_map.setCenter(point);

    var infoWindow = new google.maps.InfoWindow({
      content: ''
    });

    google.maps.event.addListener(self.my_marker, 'click', function () {
      infoWindow.setContent(this.html);
      infoWindow.open(self.my_map, this);
    });

  },

  get_geolocation: function () {
    var self = this;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        self.set_my_marker(position.coords.latitude, position.coords.longitude);
        self.get_pages();
      });
    }
    else {
      alert('Your browser does not support geolocation api');
    }
  },
  set_my_location_page: function (page_num) {
    this.my_location_page_num = page_num;
    this.get_pages();
  },
  set_my_location_address: function (address) {
    this.my_location_address = address;
    this.my_location_page_num = 1;
    this.get_pages();
  },
  get_pages: function () {
    var self = this;
    var loader_location_page = $('my_location_loading');
    loader_location_page.removeClass('hidden');
    my_request = new Request.HTML({
      url: self.my_url,
      evalScripts: false,
      data: {
        my_latitude: self.my_lat,
        my_longitude: self.my_lon,
        my_address: self.my_location_address,
        my_page_num: self.my_location_page_num
      },

      onComplete: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        if(loader_location_page) loader_location_page.addClass('hidden');
        var el = $$('.layout_page_my_location');
        var tElement = new Element('div', {'html': responseHTML});

        $('my_location_pages').innerHTML = tElement.getElement('.layout_page_my_location').innerHTML;
        if ($('my_location_pagination_select'))
          $('my_location_pagination_select').value = self.my_location_page_num;
      }
    }).post();
  }
};

/* function(){
  var body_element = $$('body')[0];

    console.log(this.bg);

};*/