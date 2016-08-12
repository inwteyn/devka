<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: create.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
?>
<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-07-22 11:18:13 ulan $
 * @author     Ulan
 */

?>
<script type="text/javascript">
  var interv = setInterval(function(){
    if($('form-upload')){
      $('demo-status').dispose();
      $('demo-fallback').getElement('legend').dispose();
      $('search-element').setStyle('position', 'absolute');
      $('search-element').setStyle('left', '-1000px');
      clearInterval(interv);
    }
  }, 100);
    (function()
    {
        window.multiSelect = new MultiSelector();
        en4.core.runonce.add(function() {
            multiSelect.bind('file-wrapper', 5);
            multiSelect.addElement($('file'));
        });
    })();

</script>

<?php if( count($this->navigation) > 0 ): ?>
<?php
		// Render the menu
		echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>

<div id="navigation_content">
  <div class="layout_content">
    <?php echo $this->form->setAttrib('class', 'global_form touchupload')->setAttrib('id', 'form-upload')->render($this) ?>
  </div>
</div>

