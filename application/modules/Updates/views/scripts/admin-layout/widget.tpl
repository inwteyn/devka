<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<style type="text/css">
.form-label, .form-element, .form-wrapper  {
float: none !important;
}
.form-label {
padding: 0px 5px 0 2px;
}

</style>
<div class="global_form_popup">

  <?php if( $this->form ): ?>

    <script type="text/javascript">
      window.addEvent('domready', function() {
        var params = parent.pullWidgetParams();
        var info = parent.pullWidgetTypeInfo();
        $H(params).each(function(value, key) {
          if( $(key) ) {
            $(key).value = value;
          }
        });
        $$('.form-description').set('html', '');
      })
    </script>

    <?php echo $this->form->render($this) ?>

  <?php elseif( $this->values ):?>

    <script type="text/javascript">
      parent.setWidgetParams(<?php echo Zend_Json::encode($this->values) ?>);
      parent.Smoothbox.close();
    </script>

  <?php else: ?>

    <?php echo $this->translate("UPDATES_Error: no values") ?>
    
  <?php endif; ?>

</div>