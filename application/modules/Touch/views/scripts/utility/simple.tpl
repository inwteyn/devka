<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: simple.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<script type="text/javascript">
(function(){
	<?php //Smoothbox Operations; ?>
	<?php if( $this->smoothboxClose ): ?>
		setTimeout(function()
		{
			Smoothbox.close();
		}, <?php echo ( empty($this->smoothboxCloseTime) ? 0 : $this->smoothboxCloseTime ); ?>);
  <?php endif; ?>

  <?php if( $this->redirect ): ?>
      setTimeout(function()
      {
				var $a = new Element('a', {'href':'<?php echo $this->redirect ?>'});
        Smoothbox.open($a);
      }, <?php echo ( empty($this->redirectTime) ? 0 : $this->redirectTime  ); ?>);
  <?php endif; ?>


	<?php //Ajax Operations; ?>
  <?php if( $this->parentRefresh ): ?>
		setTimeout(function()
		{
			Touch.refresh();
		}, <?php echo ( empty($this->parentRefreshTime) ? 0 : $this->parentRefreshTime ); ?>);
  <?php endif; ?>

  <?php if( $this->parentRedirect ): // Refresh parent window (for smoothboxes) ?>
		setTimeout(function()
		{
			Touch.goto('<?php echo $this->parentRedirect ?>');
		}, <?php echo ( empty($this->parentRedirectTime) ? 0 : $this->parentRedirectTime ); ?>);
  <?php endif; ?>



	<?php //Location Operations; ?>
  <?php if( $this->locationHref ): ?>
		setTimeout(function()
		{
			location.hash = '';
			location.href = '<?php echo $this->locationHref; ?>';
		}, <?php echo ( empty($this->locationHrefTime) ? 0 : $this->locationHrefTime ); ?>);
  <?php endif; ?>

  <?php if( $this->locationReload ): ?>
		setTimeout(function()
		{
			location.hash = '';
			location.reload();
		}, <?php echo ( empty($this->locationReloadTime) ? 0 : $this->locationReloadTime ); ?>);
  <?php endif; ?>
})();
</script>