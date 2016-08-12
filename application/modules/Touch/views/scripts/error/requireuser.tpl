<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: requireuser.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php
  if( $this->form ):
    echo $this->form->render($this);
  else:
    echo $this->translate('Please sign in to continue.');
  endif;
?>