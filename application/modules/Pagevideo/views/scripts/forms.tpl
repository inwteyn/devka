<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: forms.tpl 2010-09-20 17:53 idris $
 * @author     Idris
 */
?>

<?php echo isset($this->videoEditForm) ? $this->videoEditForm->render($this) : ""; ?>
<?php echo isset($this->videoUploadForm) ? $this->videoUploadForm->render($this) : ""; ?>