<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<div class="hebadge_require_loader_description">
  <?php echo $this->translate('HEBADGE_LOADER_DESCRIPTION', $this->complete.'%');?>
</div>

<div class="hebadge_require_loader">
  <div class="hebadge_require_loader_line" style="width: <?php echo $this->complete?>%"></div>
</div>

<div style="padding-bottom: 10px;"></div>
