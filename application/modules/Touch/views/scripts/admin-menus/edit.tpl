<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>
<script type="text/javascript">
  window.addEvent('domready', function()
  {
    var touch_pages = $$('#uri_pages-wrapper');
    var outer_page = $$('#uri-wrapper');

    if( ($$('input[id=uri_type-1]:checked').length) )
    {

      outer_page.setStyle('display','block');
      touch_pages.setStyle('display','none');
    }
    else
    {
      outer_page.setStyle('display','none');
      touch_pages.setStyle('display','block');
    }

  $$('input[name=uri_type]').addEvent('change', function()
  {
    if( !this.checked ) return;
    if( this.value == 1 )
    {
      outer_page.setStyle('display','block');
      touch_pages.setStyle('display','none');
    }
    else
    {
      outer_page.setStyle('display','none');
      touch_pages.setStyle('display','block');
    }
  });

  $$('#uri_pages').addEvent('change', function()
  {
    var t = this.value;
    $$('#uri').set('value', t);
  });

  });

</script>
<?php elseif( $this->status ): ?>

  <div><?php echo $this->translate("Changes saved!") ?></div>

  <script type="text/javascript">
    var name = '<?php echo $this->name ?>';
    var label = '<?php echo $this->escape($this->menuItem->label) ?>';
    setTimeout(function() {
      parent.$('admin_menus_item_' + name).getElement('.item_label').set('html', label);
      parent.Smoothbox.close();
    }, 500);
  </script>

<?php endif; ?>