<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create-file.tpl  21.09.11 16:47 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->render('admin/_productHeader.tpl'); ?>

<div>

  <div style="width: 75%; float: left;">
<?php echo $this->getGatewayState(0); ?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('link-wrapper').setStyle('display', 'none');
    $('file-wrapper').setStyle('display', 'none');

    var file = $$('.description')[0];
    var link = $$('.description')[1];
    file.addEvent('click', function(){
      link.removeClass('active');
      file.addClass('active');
      $('file-wrapper').setStyle('display', 'block');
      $('link-wrapper').setStyle('display', 'none');
      $('submit-wrapper').setStyle('display', 'none');
    });
    link.addEvent('click', function(){
      file.removeClass('active');
      link.addClass('active');
      $('link-wrapper').setStyle('display', 'block');
      $('file-wrapper').setStyle('display', 'none');
      $('submit-wrapper').setStyle('display', 'block');
    });
  });
</script>

<div class='settings' style="clear: none">
  <?php echo $this->form->render($this) ?>
</div>

  </div>
  <div style="float: right;">
    <?php echo $this->render('admin/_productsMenu.tpl'); ?>
  </div>

</div>
