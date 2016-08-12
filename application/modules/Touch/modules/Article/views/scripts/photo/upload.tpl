<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: upload.tpl 2011-08-05 13:43:04 ulan $
 * @author     Ulan
 */

?>

<script type="text/javascript">
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
      <?php if($this->created): ?>
      <h3><?php echo $this->translate('Add Photos');?> <?php echo $this->translate('or');?>
        <a href='<?php echo $this->article->getHref();?>'><?php echo $this->translate('continue to view this article');?></a></h3>
      <?php endif; ?>
        <?php echo $this->form->setAttrib('class', 'global_form touchupload touch-multi-upload')->setAttrib('id', 'form-upload')->render($this); ?>
    </div>
</div>
