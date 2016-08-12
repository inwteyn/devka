<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.js.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/create.js')
?>
<script type="text/javascript">
window.addEvent('domready', function (e) {
  var coverImgEl = $('heevent-create-cover');
  var catPhotos = <?php echo Zend_Json::encode(Engine_Api::_()->heevent()->getCategoryCovers());?>;
  var catSelect = $('category_id');
  var photo_id = $('photo_id');
  var prevNextBtns = $$('.heevent-create-cover-nav');
  $('cover_params-element').addEvent('click', function (e) {
    var coverImg = $('heevent-create-cover');
    var pos = ['left', 'center', 'right'];
    if (e.target.tagName == 'LABEL') {
      var label = $(e.target);
      var input = $(label.get('for'));
      if (input.get('type') == 'radio') {
        coverImg.setStyle('background-position', pos[input.value]);
      }
    } else if ($(e.target).get('id') == 'cover_repeat') {
      var cbx = $(e.target);
//      console.log((cbx.get('checked')));
//      console.log((cbx.get('checked')) ? 'repeat' : 'no-repeat');
      coverImg.setStyle('background-repeat', (cbx.get('checked')) ? 'repeat' : 'no-repeat');
    }
  });

  _initUploader();

  $('description').autogrow(); // Description text area auto grow

  $('heevent-create-upload-cover').addEvent('mousedown', function (e) {
//    console.log(e);
    _hem.fireEvent($$('#photo')[0], 'click');
  });

  $('heevent-create-delete-cover').addEvent('mousedown', deletePhoto);

  var input = /** @type {HTMLInputElement} */(document.getElementById('location'));
  var autocomplete = new google.maps.places.Autocomplete(input);
  google.maps.event.addListener(autocomplete, 'place_changed', function () {
    _hem.fireEvent(input, 'change');
  });
  if(input.value)_hem.fireEvent(input, 'change');
  var catMouseOutCB = function (e) {
    if (coverImgEl.getAttribute('selected')) return;
    var defaultBg;
    if (photo_id.value)
      defaultBg = coverImgEl.get('selected-bg');
    else
      defaultBg = coverImgEl.get('default-bg');
    if (defaultBg)
      coverImgEl.setStyle('background-image', defaultBg);
  };
  var catMouseOverCB = function (e) {
    if (!coverImgEl.getAttribute('selected') && e.target.tagName == 'OPTION') {
      var option = e.target;
      var cat_id = parseInt(option.value);
      var cat_photo = catPhotos[cat_id];
      if (cat_photo) {
        setPhoto(cat_photo);
      }
    }
  };
  var catMouseChangeCB = function (e) {
    var option;
    if (!coverImgEl.getAttribute('selected') && (option = this.getElement('option:selected'))) {
      var cat_id = parseInt(option.value);
      if (!cat_id) {
        var piSrc = photo_id.getProperty('src');
        if (piSrc && photo_id.value) {
          setPhoto(piSrc);
          coverImgEl.setAttribute('selected', true);
          $('heevent-create-delete-cover').show();
          //            coverImgEl.set('selected-bg', ['url("', piSrc, '")'].join(''));
          photo_id.setAttribute('src', '');
        } else {
          photo_id.value = '';
          coverImgEl.set('selected-bg', '');
          coverImgEl.setStyle('background-image', coverImgEl.get('default-bg'));
          if(photo_id.oldValue)
            delete photo_id.oldValue;
          if(photo_id.photos)
            delete coverImgEl.photos;
          if(photo_id.photo_index)
            delete coverImgEl.photo_index;
        }
        prevNextBtns.hide();
        return;
      }
      var url = '<?php echo $this->url(array('module' => 'heevent', 'controller' => 'index', 'action' => 'get-covers'), 'default', true) ?>';
//      console.log(cat_id);
//      console.log(url);
      var photosArr = option.photos;
      if (!photo_id.value)
        photo_id.value = true;
      if (photosArr) {
        prevNextBtns.show();
        coverImgEl.photos = photosArr;
        photo_id.value = photosArr[0].photo_id;
        setPhoto(photosArr[0].src);
        coverImgEl.set('selected-bg', ['url("', photosArr[0].src, '")'].join(''));
        coverImgEl.photo_index = 0;
      } else if (catPhotos[cat_id]) {
        (function (option, url, cat_id) {
          new Request.JSON({
            'url':url,
            'method':'post',
            'data':{
              category:cat_id,
              format:'json'
            },
            'onSuccess':function (response) {
//              console.log(response);
              var selectedBg;
              if (photo_id.get('src')) {
                selectedBg = photo_id.get('src');
              } else {
                selectedBg = response.covers[0].src;
                photo_id.value = response.covers[0].photo_id;
              }
              setPhoto(selectedBg);
              coverImgEl.photos = response.covers;
              if(response.covers)
                prevNextBtns.show();
              coverImgEl.set('selected-bg', ['url("', selectedBg, '")'].join(''));
              coverImgEl.photo_index = 0;
              option.photos = response.covers;
            }
          }).send();
        })(option, url, cat_id);
      } else {
        photo_id.value = '';
        coverImgEl.set('selected-bg', '');
        coverImgEl.setStyle('background-image', coverImgEl.get('default-bg'));
        if(photo_id.oldValue)
          delete photo_id.oldValue;
        if(photo_id.photos)
          delete coverImgEl.photos;
        if(photo_id.photo_index)
          delete coverImgEl.photo_index;
        prevNextBtns.hide();
      }
    }
  };
  if (navigator.userAgent.indexOf('MSIE ') > 0) { // Identify IE
    catSelect.addEvent('mouseout', catMouseOutCB);

    catSelect.addEvent('mouseover', function(){
      if (!coverImgEl.get('default-bg'))
        coverImgEl.set('default-bg', coverImgEl.getStyle('background-image'));
    });

    catSelect.addEvent('change', catMouseChangeCB);
  } else {
    catSelect.addEvent('mouseout', catMouseOutCB);

    catSelect.addEvent('mouseover', catMouseOverCB);

    catSelect.addEvent('change', catMouseChangeCB);
  }
  if (photo_id.get('src')) {
    setPhoto(photo_id.get('src'));
    checkPhotoRatio(photo_id.get('src'));
    _hem.fireEvent(catSelect.getElement('option:selected'), 'click');
  }

  prevNextBtns.addEvent('mousedown', function (e) {
    var len = coverImgEl.photos.length;
    if (this.get('name') == 'prev') {
      if (coverImgEl.photo_index == 0) return;
      coverImgEl.photo_index--;
    } else {
      if (coverImgEl.photo_index == (len - 1)) return;
      coverImgEl.photo_index++;
    }
    var cover = coverImgEl.photos[coverImgEl.photo_index];
    if (photo_id.get('src')) {
      photo_id.set('src', '');
    }
    photo_id.value = cover.photo_id;
    coverImgEl.set('selected-bg', ['url("', cover.src, '")'].join(''));
    setPhoto(cover.src);
  });
});
function setPhoto(src) {
  var imgEl = $('heevent-create-cover');
  if (!imgEl.get('default-bg'))
    imgEl.setProperty('default-bg', imgEl.getStyle('background-image'));
  imgEl.setStyle('background-image', ['url("', src, '")'].join(''));
}
</script>