<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _adminMenuTabs.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>


<?php
  $menu_active = '';
  $request = Zend_Controller_Front::getInstance()->getRequest();
  if ($request->getModuleName() == 'hebadge'){
    if ($request->getControllerName() == 'admin-badges'){
      if ($request->getActionName() == 'index'){
        $menu_active = 'badges_index';
      }
      if ($request->getActionName() == 'create'){
        $menu_active = 'badges_create';
      }
      if ($request->getActionName() == 'members'){
        $menu_active = 'badges_members';
      }
    }

    if ($request->getControllerName() == 'admin-levelbadges'){
      if ($request->getActionName() == 'index'){
        $menu_active = 'levelsbadges_index';
      }
      if ($request->getActionName() == 'create'){
        $menu_active = 'levelsbadges_create';
      }
    }

    if ($request->getControllerName() == 'admin-pagebadges'){
      if ($request->getActionName() == 'requests'){
        $menu_active = 'pagebadges_requests';
      }
      if ($request->getActionName() == 'index'){
        $menu_active = 'pagebadges_index';
      }
      if ($request->getActionName() == 'create'){
        $menu_active = 'pagebadges_create';
      }
      if ($request->getActionName() == 'members'){
        $menu_active = 'pagebadges_members';
      }
    }
    if ($request->getControllerName() == 'admin-creditbadges'){
      if ($request->getActionName() == 'index'){
        $menu_active = 'creditbadges_index';
      }
      if ($request->getActionName() == 'create'){
        $menu_active = 'creditbadges_create';
      }
      if ($request->getActionName() == 'members'){
        $menu_active = 'creditbadges_members';
      }
    }
  }
?>

<div class="hebadge_admin_tabs">
  <ul>
    <li>
      <span class="category_title hebadge_admin_tabg_category_badge"><?php echo $this->translate('HEBADGE_ADMIN_MENU_CATEGORY_BADGE')?></span>
      <ul>
        <li><a <?php if ($menu_active == 'badges_index'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'index'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_BADGE_MANAGE')?></a></li>
        <li><a <?php if ($menu_active == 'badges_create'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'create'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_BADGE_CREATE')?></a></li>
        <li><a <?php if ($menu_active == 'badges_members'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'members'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_BADGE_MEMBERS')?></a></li>
      </ul>
    </li>

    <li>
      <span class="category_title hebadge_admin_tabg_category_memberlevelbadge"><?php echo $this->translate("HEBADGE_ADMIN_MENU_MEMBER_LEVELBADGE")?></span>
      <ul>
        <li><a <?php if ($menu_active == 'levelsbadges_index'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'index'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_BADGE_MANAGE')?></a></li>
        <li><a <?php if ($menu_active == 'levelsbadges_create'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'create'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_BADGE_CREATE')?></a></li>
      </ul>
    </li>

    <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')):?>
    <li>
      <span class="category_title hebadge_admin_tabg_category_pagebadge"><?php echo $this->translate('HEBADGE_ADMIN_MENU_CATEGORY_PAGEBADGE')?></span>
      <ul>
        <li><a <?php if ($menu_active == 'pagebadges_requests'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'requests'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_PAGEBADGE_REQUESTS')?> (<?php echo Engine_Api::_()->getDbTable('pagemembers', 'hebadge')->getRequestPaginator()->getTotalItemCount()?>)</a></li>
        <li><a <?php if ($menu_active == 'pagebadges_index'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'index'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_PAGEBADGE_MANAGE')?></a></li>
        <li><a <?php if ($menu_active == 'pagebadges_create'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'create'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_PAGEBADGE_CREATE')?></a></li>
        <li><a <?php if ($menu_active == 'pagebadges_members'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'members'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_PAGEBADGE_MEMBERS')?></a></li>
      </ul>
    </li>
    <?php endif;?>
    <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit')): ?>
      <li>
        <span class="category_title hebadge_admin_tabg_category_creditbadge"><?php echo $this->translate('HEBADGE_ADMIN_MENU_CATEGORY_CREDITBADGE')?></span>
        <ul>
          <li><a <?php if ($menu_active == 'creditbadges_index'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'index'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_CREDITBADGE_MANAGE')?></a></li>
          <li><a <?php if ($menu_active == 'creditbadges_create'):?>class="active"<?php endif;?> href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'creditbadges', 'action' => 'create'), 'admin_default', true)?>"><?php echo $this->translate('HEBADGE_ADMIN_MENU_CREDITBADGE_CREATE')?></a></li>
        </ul>
      </li>
    <?php endif;?>
  </ul>
</div>

