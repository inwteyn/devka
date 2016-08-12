<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _jsSwitch.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">

var topLevelId = '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>';
var topLevelValue = '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>';

function changeFields(element, force)
{
  // We can call this without an argument to start with the top level fields
  if( !$type(element) )
  {
    $$('.parent_'+topLevelId).each(function(element)
    {
      changeFields(element);
    });
    return;
  }

  // If this cannot have dependents, skip
  if( !$type(element) || !$type(element.onchange) )
  {
    return;
  }

  // Get the input and params
  var field_id = element.get('class').match(/field_([\d]+)/i)[1];
  var parent_field_id = element.get('class').match(/parent_([\d]+)/i)[1];
  var parent_option_id = element.get('class').match(/option_([\d]+)/i)[1];

  if( !field_id || !parent_option_id || !parent_field_id ) {
    return;
  }

  force = ( $type(force) ? force : false );

  // Now look and see
  // Check for multi values
  var option_id;
  if( element.name.indexOf('[]') > 0 ) {
    option_id = [];
    if( element.type == 'checkbox' ) { // MultiCheckbox
      $$('.field_' + field_id).each(function(multiEl) {
        if( multiEl.checked ) {
          option_id.push(multiEl.value);
        }
      });
    } else if( element.get('tag') == 'select' && element.multiple ) { // Multiselect
      element.getChildren().each(function(multiEl) {
        if( multiEl.selected ) {
          option_id.push(multiEl.value);
        }
      });
    }
  } else {
    option_id = [element.value];
  }

  // Iterate over children
  $$('.parent_'+field_id).each(function(childElement)
  {
    var childContainer = childElement.getParent('div.form-wrapper');
    if( !childContainer ) {
      childContainer = childElement.getParent('div.form-wrapper-heading');
    }
    var childLabel = childContainer.getElement('label');
    var childOptionId = childElement.get('class').match(/option_([\d]+)/i)[1];
    
    // Forcing hide
    var nextForce;
    if( force == 'hide' || force == 'show' )
    {
      //childElement.style.display = ( force == 'hide' ? 'none' : '' );
      childContainer.style.display =
          //childLabel.style.display =
          ( force == 'hide' ? 'none' : '' );
      nextForce = force;
    }

    // Hide fields not tied to the current option (but propogate hiding)
    else if( !$type(option_id) == 'array' || !option_id.contains(childOptionId) )
    {
      //childElement.style.display = 'none';
      childContainer.style.display =
          //childLabel.style.display =
          'none';
      nextForce = 'hide';
    }

    // Otherwise show field and propogate (nothing, show?)
    else
    {
      //childElement.style.display = '';
      childContainer.style.display =
          //childLabel.style.display =
          '';
      nextForce = undefined;
    }

    changeFields(childElement, nextForce);
  });
}

window.addEvent('load', function()
{
  changeFields();
});

</script>