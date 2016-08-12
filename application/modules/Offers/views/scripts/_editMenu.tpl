<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2012-09-21 17:53 teajay $
 * @author     TJ
 */
?>

<div class="page_edit_title">
  <div class="l">
    <?php echo $this->htmlLink( $this->page->getHref(), $this->itemPhoto($this->page, 'thumb.icon') ); ?>
  </div>
  <div style="overflow: hidden;">
    <h3><?php echo $this->page->getTitle(); ?></h3>

    <div class="pages_layoutbox_menu" style="float: right; height: auto;">
      <ul>
        <li id="pages_layoutbox_menu_viewpage">
          <?php echo $this->htmlLink($this->url(array('page_id' => $this->page->url), 'page_view', true), $this->translate('View Page')); ?>
        </li>
        <li id="pages_layoutbox_menu_todashboard">
          <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->page->page_id), 'page_team', true),
          $this->translate('Back to Dashboard')); ?>
        </li>
      </ul>
    </div>
  </div>
  <div class="clr"></div>
</div>

<div class="clr"></div>