<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_stat_tabs.tpl  14.11.11 12:50 TeaJay $
 * @author     Taalay
 */
?>

<div id="sideNav" class="page_edit_dashboard">
    <ul  class="he-nav he-nav-pills he-nav-stacked">
    <li class="sideNavItem <?php if ($this->action == 'visitors') echo 'selectedItem  he-active' ?>">
      <a class="item clearfix" href="<?php echo $this->url(array('action' => 'visitors', 'page_id' => $this->page->getIdentity()), 'page_stat', true)?>">

            <?php echo $this->translate('Visitors')?>

      </a>
    </li>
    <li class="sideNavItem <?php if ($this->action == 'views') echo 'selectedItem  he-active' ?>">
      <a class="item clearfix" href="<?php echo $this->url(array('action' => 'views', 'page_id' => $this->page->getIdentity()), 'page_stat', true)?>">

            <?php echo $this->translate('Views')?>

      </a>
    </li>
    <li class="sideNavItem <?php if ($this->action == 'map') echo 'selectedItem  he-active' ?>">
      <a class="item clearfix" href="<?php echo $this->url(array('action' => 'map', 'page_id' => $this->page->getIdentity()), 'page_stat', true)?>">

            <?php echo $this->translate('Map Overlay')?>

      </a>
    </li>
  </ul>
</div>