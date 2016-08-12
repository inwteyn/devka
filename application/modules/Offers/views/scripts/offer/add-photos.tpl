<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: add.tpl 2012-07-19 17:52:12 ratbek $
 * @author     Ratbek
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('form-upload').style.clear = 'none';
  });
</script>
<div id="offer_edit">
  <div class="offers_add_photo_form_container">
    <div id="add_photo_offer">
      <?php echo $this->form->render(); ?>
    </div>
  </div>
  <div class="offers_navigation_editor tabs">
    <?php echo $this->navigation()->menu()->setContainer($this->navigation_edit)->setPartial(array('_navIcons.tpl', 'core'))->render(); ?>
  </div>
</div>
