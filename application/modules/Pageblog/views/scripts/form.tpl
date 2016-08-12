<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: form.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->isAllowedPost): ?>
	<?php echo $this->blogForm->render($this); ?>
<?php endif; ?>