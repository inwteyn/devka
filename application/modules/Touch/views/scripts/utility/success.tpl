<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: success.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<script type="text/javascript">
(function(){
	en4.core.runonce.add(function(){

		<?php if (count($this->messages) > 0): ?>
			if (!Smoothbox.box.retrieve('opened', false) && $('utility-success') == null)
			{
				<?php foreach( $this->messages as $message ): // Show messages ?>
						Touch.message('<?php echo $message; ?>', '<?php echo ($this->status)?"success":"error"; ?>', '1000');
				<?php endforeach; ?>
			}
		<?php endif; ?>
	});


	<?php //Smoothbox Operations; ?>
	<?php if( $this->smoothboxClose ): ?>
		setTimeout(function()
		{
			Smoothbox.close();
		}, <?php echo ( empty($this->smoothboxCloseTime) ? 1000 : $this->smoothboxCloseTime ); ?>);
  <?php endif; ?>

  <?php if( $this->redirect ): ?>
      setTimeout(function()
      {
				var $a = new Element('a', {'href':'<?php echo $this->redirect ?>'});
        Smoothbox.open($a);
      }, <?php echo ( empty($this->redirectTime) ? 500 : $this->redirectTime  ); ?>);
  <?php endif; ?>


	<?php //Ajax Operations; ?>
  <?php if( $this->parentRefresh ): ?>
		setTimeout(function()
		{
			Touch.refresh();
		}, <?php echo ( empty($this->parentRefreshTime) ? 1000 : $this->parentRefreshTime ); ?>);
  <?php endif; ?>

  <?php if( $this->parentRedirect ): // Refresh parent window (for smoothboxes) ?>
		setTimeout(function()
		{
			Touch.goto('<?php echo $this->parentRedirect ?>');
		}, <?php echo ( empty($this->parentRedirectTime) ? 1000 : $this->parentRedirectTime ); ?>);
  <?php endif; ?>



	<?php //Location Operations; ?>
  <?php if( $this->locationHref ): ?>
		setTimeout(function()
		{
			location.hash = '';
			location.href = '<?php echo $this->locationHref; ?>';
		}, <?php echo ( empty($this->locationHrefTime) ? 1000 : $this->locationHrefTime ); ?>);
  <?php endif; ?>

  <?php if( $this->locationReload ): ?>
		setTimeout(function()
		{
			location.reload();
		}, <?php echo ( empty($this->locationReloadTime) ? 1000 : $this->locationReloadTime ); ?>);
  <?php endif; ?>
})();
</script>

<div id="utility-success">
  <?php foreach( $this->messages as $message ): // Show messages ?>
    <div class="global_form_popup_message">
      <?php echo $message ?>
    </div>
  <?php endforeach; ?>
</div>