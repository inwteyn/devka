<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2010-07-02 18:31 ermek $
 * @author     Ermek
 */
?>

<h2><?php echo $this->translate('Delete Survey:')?> <?php echo $this->survey->__toString(); ?></h2>

<?php echo $this->form->render($this) ?>