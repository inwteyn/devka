<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: requireauth.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">

  <h2><?php echo $this->translate('Private Page') ?></h2>

  <p>
    <?php echo $this->translate('You do not have permission to view this private page.') ?>
  </p>

  <br />

  <a class='buttonlink icon_back' href='javascript:void(0);' onClick='history.go(-1);'>
    <?php echo $this->translate('Go Back') ?>
  </a>

</div>