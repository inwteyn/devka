<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<form method="post" class="global_form_popup" action="<?php echo $this->url(array()) ?>">
  <div>
    <h3><?php echo $this->translate("HEBADGE_FORM_PAGEBADGE_DELETE_TITLE") ?></h3>
    <p>
      <?php echo $this->translate("HEBADGE_FORM_PAGEBADGE_DELETE_DESCRIPTION") ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
      <?php echo $this->translate("or") ?>
			<a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
				<?php echo $this->translate("cancel") ?>
			</a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
