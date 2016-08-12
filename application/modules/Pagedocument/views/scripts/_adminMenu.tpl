<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>
<div class="admin_home_middle" style="width:200px">
<h3><?php echo $this->translate("Document Settings") ?></h3>
<ul class="admin_home_dashboard_links">
  <li style="width:200px">
    <ul >
      <li class="hecore-menu-tab products page_document_settings <?php if ($this->menu == 'global'): ?>active-menu-tab<?php endif; ?>">
        <a href="<?php echo $this->url(array('module'=>'pagedocument', 'controller'=>'index', 'action'=>'index'),'admin_default', true); ?>" class="hecore-menu-link">
          <?php echo $this->translate('Global'); ?>
        </a>
      </li>

      <li class="hecore-menu-tab products page_document_settings <?php if ($this->menu == 'categories'): ?>active-menu-tab<?php endif; ?>">
        <a href="<?php echo $this->url(array('module'=>'pagedocument', 'controller'=>'index', 'action'=>'categories'),'admin_default', true); ?>" class="hecore-menu-link">
          <?php echo $this->translate('Categories'); ?>
        </a>
      </li>

    </ul>
  </li>
</ul>
</div>