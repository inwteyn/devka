/**
 * Created by JetBrains PhpStorm.
 * User: TeaJay
 * Date: 01.12.11
 * Time: 17:13
 * To change this template use File | Settings | File Templates.
 */

var CheckinMap = new Class({
  checkin_array: {},
  markers_array: {},
  map_bounds: {},
  current_data: {},
  zoom: 4,
  constructed: false,
  map_canvas: null,

  initialize: function(checkin_array, markers_array, zoom, map_bounds, map_canvas){
    this.checkin_array = checkin_array;
    this.markers_array = markers_array;
    this.map_bounds = map_bounds;
    this.zoom = zoom;
    this.init(map_canvas);
    this.show_map();
    this.constructed = true;
  },

  init: function(map_canvas) {
    if($type(map_canvas) == 'string' && $type(document.getElementById(map_canvas)) == 'element'){
      this.map_canvas = document.getElementById(map_canvas);
    } else if($type(map_canvas) == 'element'){
      this.map_canvas = map_canvas;
    } else{
      this.map_canvas = document.getElementById('map_canvas');
    }

    this.map = new google.maps.Map(this.map_canvas, {mapTypeId: google.maps.MapTypeId.ROADMAP, center: new google.maps.LatLng(0, 0), zoom: 15});
  },

  show_map: function() {
    var self = this;
    if( this.markers_array.length==0 ) {return false;}

    var infowindow = new google.maps.InfoWindow();

    for( var i=0; i<this.markers_array.length; i++ )
    {
      var marker = this.markers_array[i];
      var marker_obj = new google.maps.Marker({
        map: this.map,
        position: new google.maps.LatLng(marker.lat, marker.lng)
      });

      this.setMarkerInfo(marker, infowindow, marker_obj);
      this.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng), 4);
    }

    this.setMapCenterZoom();
  },

  setMarkerInfo: function(marker, infowindow, marker_obj) {
    var self = this;
    google.maps.event.addListener(marker_obj, 'click', function() {
      if (marker.url) {
        var marker_content = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.pages_photo + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;"><a href="'+marker.url+'">' + marker.title + '</a></h3>' + marker.desc + '</td></tr></table>';
      } else {
        var marker_content = '<table width="500"><tr valign="top"><td width="110"><img src="' + marker.checkin_icon + '" width="100"></td><td width="400"><h3 style="margin-top:0; margin-bottom:6px;">' + marker.title + '</h3></td></tr></table>';
      }

      infowindow.setContent(marker_content);
      infowindow.open(self.map, this);
    });
  },

  setMapCenterZoom: function() {
    if (this.map_bounds && this.map_bounds.min_lat && this.map_bounds.max_lng && this.map_bounds.min_lat && this.map_bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(this.map_bounds.min_lat, this.map_bounds.min_lng), new google.maps.LatLng(this.map_bounds.max_lat, this.map_bounds.max_lng));
    }
    if (this.map_bounds && this.map_bounds.map_center_lat && this.map_bounds.map_center_lng) {
      this.map.setCenter(new google.maps.LatLng(this.map_bounds.map_center_lat, this.map_bounds.map_center_lng), 4);
    } else {
      this.map.setCenter(new google.maps.LatLng(marker.lat, marker.lng), this.zoom);
    }
    if (bds) {
      this.map.setCenter(bds.getCenter());
      this.map.fitBounds(bds);
    }
  },

  setView : function(view, el) {
    $$('.checkin-view-types').removeClass('active');
    if ($type(el) == 'element') {
      el.addClass('active');
    }

    if (view == 'map') {
      $('checkin_list_cont').setStyle('display', 'none');
      this.map_canvas.setStyle('position', 'relative');
      this.map_canvas.setStyle('top', '0px');
      google.maps.event.trigger(this.map, 'resize');
      this.setMapCenterZoom();
    } else if (view == 'list') {
      $('checkin_list_cont').setStyle('display', 'block');
      this.map_canvas.setStyle('position', 'absolute');
      this.map_canvas.setStyle('top', '10000px');
    }
  }
});