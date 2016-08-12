<div class="headline">
  <h2>
    <?php echo $this->translate('Videos');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
</div>
<script type="text/javascript">
  var maxRecipients = 10;
  
  function removeFromToValue(id)
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('height', '0');
    }

    $('auth_view_group_field').disabled = false;
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
      //var tokens = <?php echo $this->friends ?>;
      new Autocompleter.Request.JSON('auth_view_group_field', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){
          if(token.type == 'user'){
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          else {
            var choice = new Element('li', {'class': 'autocompleter-choices friendlist', 'id':token.label});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
            
        },
        onPush : function(){
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('auth_view_group_field').disabled = true;
          }
        }
      });
    });


  en4.core.runonce.add(function(){
    new OverText($('auth_view_group_field'), {
      'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
      'element' : 'label',
      'positionOptions' : {
        position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        offset: {
          x: ( en4.orientation == 'rtl' ? -4 : 4 ),
          y: 2
        }
      }
    });
  });
</script>

<?php
    $this->headScript()
      ->appendFile($this->baseUrl() . '/externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->baseUrl().'/application/modules/Core/externals/scripts/composer.js');
?>

<script type="text/javascript">
  var composeInstance;
  en4.core.runonce.add(function() {
    var tel = new Element('div', {
      'id' : 'compose-tray',
      'styles' : {
        'display' : 'none'
      }
    }).inject($('submit'), 'before');

    var mel = new Element('div', {
      'id' : 'compose-menu'
    }).inject($('submit'), 'after');
    // @todo integrate this into the composer
    if( !Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad() ) {
      composeInstance = new Composer('body', {
        overText : false,
        menuElement : mel,
        trayElement: tel,
        baseHref : '<?php echo $this->baseUrl() ?>',
        hideSubmitOnBlur : false,
        allowEmptyWithAttachment : false,
        submitElement: 'submit',
        type: 'message'
      });
    }
  });
</script>

<?php echo $this->form->render($this);?>

<script type="text/javascript">
//<![CDATA[
var tags;

window.addEvent('load', function()
{
      tags = avp.tag.register(avpGetById('tags_visible'), avpGetById('tags_container'), avpGetById('tags'), avpGetById('form-upload'));
});
//]]>
</script>