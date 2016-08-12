<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php
$this->headLink()->appendStylesheet($this->baseUrl() . 'application/modules/Advancedsearch/externals/styles/admin/main.css');
?>
<div class="clear">
  <div class="settings">
    <h3><?php echo $this->translate('AS_Advanced Search Configuration')?></h3>
    <form method="post">
      <div>
        <div>
          <p>
            <?php echo $this->translate('AS_Add items which will be included in search');?>
          </p>
          <?php if ($this->formSaved):?>
            <ul class="form-notices">
              <li><?php echo $this->translate($this->formSaved)?></li>
            </ul>
          <?php endif;?>

          <div style="margin: 10px 5px">
            <input type="checkbox" id="all_types_select"><span class="item_type_select" style="cursor: pointer"><?php echo $this->translate('Select/Deselect All')?></span>
            <br><br>

            <!-------------ADMIN_MENU_ITEMS---------------------->
            <ul class="admin_menus_items" id='menu_list'>
              <?php foreach( $this->types as $type ): ?>
                <li class="admin_menus_item<?php if( isset($type) && !$type ) echo ' disabled' ?>" id="admin_menus_item_<?php echo $type ?>">
                  <span class="item_wrapper">
                    <span class="item_label">
                      <?php echo $this->translate('ITEM_TYPE_'.strtoupper($type));?>
                    </span>
                    <span style="float: right; margin-right: 1px; margin-top: -22px;">
                      <input class="as_types_list" <?php if (in_array($type, $this->viewList)):?>checked="checked" <?php endif;?>  name="types[]" type="checkbox" value="<?php echo $type;?>">
                    </span>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
            <!-------------END_ADMIN_MENU_ITEMS------------------------>

          </div>
          <button class="admin_button" type="submit"><?php echo $this->translate('AS_Save changes');?></button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('domready', function() {
    $$('.item_label').addEvents({
      mouseover: showPreview,
      mouseout: showPreview
    });
  });

  var showPreview = function(event) {
    try {
      element = $(event.target);
      element = element.getParent('.admin_menus_item').getElement('.item_url');
      if( event.type == 'mouseover' ) {
        element.setStyle('display', 'block');
      } else if( event.type == 'mouseout' ) {
        element.setStyle('display', 'none');
      }
    } catch( e ) {
    }
  }


  window.addEvent('load', function() {
    SortablesInstance = new Sortables('menu_list', {
      clone: true,
      constrain: false,
      handle: '.item_label',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

 var reorder = function(e) {
     var menuitems = e.parentNode.childNodes;

     var ordering = {};
     var i = 0;
     for (var menuitem in menuitems)
     {
       var child_id = menuitems[menuitem].id;
       if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin'))
       {
         ordering[child_id.substr(17, child_id.length)] = i;
         i++;
       }
     }
    // Send request
    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
    var menu = {'menu':ordering}
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'format':'json',
      'data' : menu,
      onSuccess : function(responseJSON) {
      }
    });
    request.send();
  }

  function ignoreDrag()
  {
    event.stopPropagation();
    return false;
  }

</script>

<script>

  $$('.item_type_select').addEvent('click', function(){
    if ($(this).getPrevious().get('checked')) {
      $(this).getPrevious().set('checked', '')
      $(this).getPrevious().fireEvent('change');
    } else {
      $(this).getPrevious().set('checked', 'checked')
      $(this).getPrevious().fireEvent('change');
    }
  });
  $('all_types_select').addEvent('change', function(){
    if ($(this).get('checked')) {
      $$('.as_types_list').set('checked', 'checked');

    } else {
      $$('.as_types_list').set('checked', '');
    }
  });
</script>