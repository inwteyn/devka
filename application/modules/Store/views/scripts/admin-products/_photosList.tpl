<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');
$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');
$this->headTranslate(array(
  'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
  'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
  'Remove', 'Click to remove this entry.', 'Upload failed',
  '{name} already added.',
  '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
  '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
  '{name} could not be added, amount of {fileListMax} files exceeded.',
  '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
  'Server returned HTTP-Status <code>#{code}</code>',
  'Security error occurred ({text})',
  'Error caused a send or load operation to fail ({text})',
));
?>

<div id="demo-status" class="hide">
  <div>
    <?php echo $this->translate('STORE_UPLOAD_A_PHOTO_DESCRIPTION'); ?>
  </div>
  <div class="demo-status-overall" id="demo-status-overall" style="display: none">
    <div class="overall-title"></div>
    <img src="<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
         class="progress overall-progress"/>
  </div>
  <div class="demo-status-current" id="demo-status-current" style="display: none">
    <div class="current-title"></div>
    <img src="<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/assets/progress-bar/bar.gif'; ?>"
         class="progress current-progress"/>
  </div>
  <div class="current-text"></div>
</div>
<ul id="demo-list"></ul>

<script type="text/javascript">
var current_cover_id = new Number('<?php echo $this->product->photo_id; ?>');
function showLoader(id) {
  try {
    var loader = $('photo-item-loader-' + id);
    loader.setStyle('display', 'block');
  } catch (e) {
  }
}
function hideLoader(id) {
  try {
    var loader = $('photo-item-loader-' + id);
    loader.setStyle('display', 'none');
  } catch (e) {
  }
}

function showReOrder() {
  $('photos-save-positions').setStyle('display', '');
}
function hideReOrder() {
  $('photos-save-positions').setStyle('display', 'none');
}

function reOrder() {
  var list = $$('.photo-item');
  var ids = '';
  list.each(function (el) {
    ids += $(el).get('data-id') + ',';
  });
  showLoader('-2');
  new Request.JSON({
    url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'re-order'), 'admin_default', 1); ?>',
    data: {
      ids: ids,
      format: 'json'
    },
    onSuccess: function (response) {
      hideReOrder();
      hideLoader('-2');
    },
    onError: function (text, error) {
      hideLoader('-2');
    },
    onFailure: function (xhr) {
      hideLoader('-2');
    }
  }).send();
}

function addPhoto(id, url, title) {
  var li = new Element('li', {
    'class': 'photo-item',
    'data-id': id,
    'id': 'store_photo_' + id
  });
  li.setStyle('background-image', "url('" + url + "')");
  li.inject($('sortable-list'));

  var div = new Element('div', {'class': 'photo-item-wrapper'});
  div.inject(li);

  var i = new Element('i', {'class': 'remove-icon hei-trash-o'});
  var loader = new Element('div', {
    'class': 'photo-item-loader',
    'id': 'photo-item-loader-' + id
  });
  new Element('div', {'class': 'he-loader-animation photo-item-loader-item'}).inject(loader);

  i.inject(div);
  loader.inject(li);

  var cl = (id == current_cover_id) ? 'photo-item-cover photo-item-cover-active' : 'photo-item-cover';

  var a = new Element('a', {
    'class':cl,
    'href':'javascript:void(0)'
  });
  a.set('html', '<?php echo $this->translate('STORE_Product Cover'); ?>');
  a.inject(li);

  initEvents();
}
function changeCover(el, src, id) {
  $('product_preview').getElement('img').set('src', src);
  current_cover_id = id;

  $$('.photo-item-cover').removeClass('photo-item-cover-active');

  if(!$(el).hasClass('photo-item-cover-active')) {
    $(el).addClass('photo-item-cover-active');
  }
}
function initEvents() {

  $$('.photo-item-cover').removeEvent('click').addEvent('click', function() {
    var self = this;
    var parent = $(self).getParent();
    var id = $(parent).get('data-id');
    if(id == current_cover_id) {
      return;
    }
    showLoader(id);
    new Request.JSON({
      url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'set-cover'), 'admin_default', 1); ?>',
      data: {
        product_id:'<?php echo $this->product->getIdentity(); ?>',
        photo_id: id,
        format: 'json'
      },
      onSuccess: function (response) {
        if(response.status) {
          changeCover(self, response.photo, response.photo_id);
        }
        hideLoader(id);
      },
      onError: function (text, error) {
        hideLoader(id);
      },
      onFailure: function (xhr) {
        hideLoader(id);
      }
    }).send();
  });

  $('photos-save-positions').removeEvent('click').addEvent('click', function () {
    reOrder();
  });

  $$('.remove-icon').removeEvent('click').addEvent('click', function () {
    var self = this;
    var parent = $(self).getParent().getParent();

    var id = $(parent).get('data-id');
    showLoader(id);
    new Request.JSON({
      url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'removephoto'), 'admin_default', 1); ?>',
      data: {
        product_id: '<?php echo $this->product->getIdentity(); ?>',
        photo_id: id,
        format: 'json'
      },
      onSuccess: function (response) {
        if(id == current_cover_id && response.photo && response.photo_id) {
          var a = $('store_photo_'+response.photo_id).getElement('a');
          changeCover(a, response.photo, response.photo_id);
        }
        $('product_preview').getElement('img').set('src', response.photo);
        parent.remove();
        hideLoader(id);
      },
      onError: function (text, error) {
        hideLoader(id);
      },
      onFailure: function (xhr) {
        hideLoader(id);
      }
    }).send();
  });

  var list = $('sortable-list');
  new Sortables(list, {
    constrain: true,
    clone: true,
    revert: true,
    onComplete: function (el, clone) {
      showReOrder();
    }
  });
}

function loadMore() {
  if (!$('store-photos-page')) {
    return;
  }

  var wrapper = $('storestore-admin-photos-viewmore');
  var page = Number($('store-photos-page').value);
  if (isNaN(page) || page <= 0) {
    return
  }

  showLoader('-3');
  wrapper.getElement('a').setStyle('display', 'none');


  new Request.JSON({
    url: "<?php echo $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'editphotos'), 'admin_default', 1); ?>",
    data: {
      product_id: '<?php echo $this->product->getIdentity(); ?>',
      page: page,
      format: 'json'
    }, onSuccess: function (response) {
      if (response.nextPage) {
        $('store-photos-page').value = response.nextPage;

        hideLoader('-3');
        wrapper.getElement('a').setStyle('display', '');
      } else {
        wrapper.parentNode.removeChild(wrapper);
      }

      if (response.items) {
        for (var i = 0; i < response.items.length; i++) {
          var item = response.items[i];
          addPhoto(item.photo_id, item.path, item.title);
        }
      }
    },
    onError: function (text, error) {
      hideLoader('-3');
      wrapper.getElement('a').setStyle('display', '');
    },
    onFailure: function (xhr) {
      hideLoader('-3');
      wrapper.getElement('a').setStyle('display', '');
    }
  }).send();

}

window.addEvent('domready', function () {
  initEvents();

  window.addEvent('scroll', function () {
    var link = $('storestore-admin-photos-viewmore');
    if (!link) {
      return;
    }
    if (window.getScrollTop() + 5 >= window.getScrollSize().y - window.getSize().y) {
      loadMore();
    }
  });

  try {
    var up = new FancyUpload2($('demo-status'), $('demo-list'), { // options object
      verbose: false,
      appendCookieData: true,
      timeLimit: 0,
      // set cross-domain policy file
      policyFile: '<?php echo (_ENGINE_SSL ? 'https://' : 'http://')
      . $_SERVER['HTTP_HOST'] . $this->url(array(
        'controller' => 'cross-domain'),
        'default', true) ?>',

      // url is read from the form, so you just have to change one place
      url: '<?php echo $this->url(array('module'=>'store','controller'=>'products', 'action'=>'uploadphotos'), 'admin_default', 1);?>' + '?ul=1',

      // path to the SWF file
      path: '<?php echo $this->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.swf';?>',

      // remove that line to select all files, or edit it, add more items
      typeFilter: {
        'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
      },
      filesCount: 0,
      // this is our browse button, *target* is overlayed with the Flash movie
      target: 'product-images-uploader-button',
      container: 'product-images-uploader-button',
      data: {
        collection_id: '<?php echo $this->product->getIdentity(); ?>'
      },

      // graceful degradation, onLoad is only called if all went well with Flash
      onLoad: function () {

        // We relay the interactions with the overlayed flash to the link
        this.target.addEvents({
          click: function () {
            return false;
          },
          mouseenter: function () {
            this.addClass('hover');
          },
          mouseleave: function () {
            this.removeClass('hover');
            this.blur();
          },
          mousedown: function () {
            this.focus();
          }
        });
      },
      /**
       * Is called when files were not added, "files" is an array of invalid File classes.
       *
       * This example creates a list of error elements directly in the file list, which
       * hide on click.
       */
      onSelectFail: function (files) {

      },

      onComplete: function hideProgress() {

      },

      onFileStart: function () {

      },

      onFileRemove: function (file) {

      },

      onSelectSuccess: function (file) {
        this.filesCount = file.length;
        showLoader('-1');
        up.start();
      },
      /**
       * This one was directly in FancyUpload2 before, the event makes it
       * easier for you, to add your own response handling (you probably want
       * to send something else than JSON or different items).
       */
      onFileSuccess: function (file, response) {
        this.filesCount--;
        var json = new Hash(JSON.decode(response, true) || {});
        if(json.isCover) {
          $('product_preview').getElement('img').set('src', json.photo);
          current_cover_id = json.photo_id;
        }
        addPhoto(json.photo_id, json.path, '');

        if (this.filesCount == 0) {
          hideLoader('-1');
        }
      },

      /**
       * onFail is called when the Flash movie got bashed by some browser plugin
       * like Adblock or Flashblock.
       */
      onFail: function (error) {
        switch (error) {
          case 'hidden': // works after enabling the movie and clicking refresh
          case 'hidden': // works after enabling the movie and clicking refresh
            alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).")) ?>');
            break;
          case 'blocked': // This no *full* fail, it works after the user clicks the button
            alert('<?php echo $this->string()->escapeJavascript($this->translate("To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).")) ?>');
            break;
          case 'empty': // Oh oh, wrong path
            alert('<?php echo $this->string()->escapeJavascript($this->translate("A required file was not found, please be patient and we'll fix this.")) ?>');
            break;
          case 'flash': // no flash 9+
            alert('<?php echo $this->translate("To enable the embedded uploader, install the latest Adobe Flash plugin.") ?>');
        }
      }

    });

  } catch (e) {
    console.log(e);
  }

});
</script>

<div class="admin-photos-list">

  <div class="admin-photos-controls-wrapper" style="height: 50px;">
    <a disabled="true" class="store-admin-upload-button" style="display: block; float: left;"
       id="product-images-uploader-button"><?php echo $this->translate('Store_Add Photo'); ?></a>

    <div style="float: left; display: none;" id="photo-item-loader--1">
      <div class="he-loader-animation photo-item-loader-item"></div>
    </div>

    <a class="store-admin-upload-button" href="javascript:void(0)" style="display: none; float: right;"
       id="photos-save-positions">
      <?php echo $this->translate('STORE_Save positions?'); ?>
    </a>

    <div style="float: right; display: none;" id="photo-item-loader--2">
      <div class="he-loader-animation photo-item-loader-item"></div>
    </div>
    <div style="clear: both"></div>
  </div>

  <ul id="sortable-list">
    <?php foreach ($this->paginator as $photo): ?>
      <li id='store_photo_<?php echo $photo->getIdentity(); ?>' class="photo-item" data-id="<?php echo $photo->getIdentity(); ?>"
          style="background-image: url('<?php echo $photo->getPhotoUrl(); ?>')">
        <div class="photo-item-wrapper">
          <i class="remove-icon hei-trash-o"></i>
        </div>

        <div class="photo-item-loader" id="photo-item-loader-<?php echo $photo->getIdentity(); ?>">
          <div class="he-loader-animation photo-item-loader-item"></div>
        </div>

        <a class="photo-item-cover <?php if($this->product->photo_id == $photo->getIdentity()) echo 'photo-item-cover-active'; ?>"
          style="display: inline-block"
          href="javascript:void(0)"><?php echo $this->translate('STORE_Product Cover'); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <div id="storestore-admin-photos-viewmore" style="text-align: center; margin-top: 10px;">
    <?php if ($this->nextPage): ?>
      <input style="display:none;" type="text" id="store-photos-page" value="<?php echo $this->nextPage; ?>">
      <a onclick="loadMore();" href="javascript:void(0);>" class="store-admin-upload-button">Load More</a>
      <div id="photo-item-loader--3" style="display: none;">
        <div class="he-loader-animation photo-item-loader-item"></div>
      </div>
    <?php endif; ?>
  </div>
</div>
