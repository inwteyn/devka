<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_edit_tabs.tpl  10.11.11 17:27 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  var myFx;
  window.addEvent('domready', function(){
    myFx = new Fx.Slide($('submenus'));
    if( "<?php echo $this->action ;?>" != 'apps' )
      myFx.hide();
    $('show_apps').addEvent('click', function(){
      myFx.toggle();
    });
  });

</script>









<?php if ($this->action != 'get-started'){?>
<div id="sideNav" class="page_edit_dashboard">
  <ul  class="he-nav-page he-nav-pills-page he-nav-stacked-page">

      <?php if ($this->action == 'edit' || $this->action == 'edit-photo') { ?>

    <li class="sideNavItem <?php if ($this->action == 'edit') echo 'selectedItem he-active' ?>">
      <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">

            <?php echo $this->translate('Basic Information')?>

      </a>
    </li>
      <?php }
      if ($this->action == 'privacy' || $this->action == 'style'  || $this->action == 'badges' || $this->action == 'manage-admins'){
      ?>


    <li class="sideNavItem <?php if ($this->action == 'privacy') echo 'selectedItem  he-active' ?>" id="privacy">
      <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'privacy', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">

            <?php echo $this->translate('Privacy Settings')?>

      </a>
    </li>
      <?php
      }
      if ($this->action == 'edit' || $this->action == 'edit-photo'){ ?>
    <li class="sideNavItem <?php if ($this->action == 'edit-photo') echo 'selectedItem  he-active' ?>" id="photo">
      <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'edit-photo', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">

            <?php echo $this->translate('Page Photo')?>

      </a>
    </li>
      <?php } ?>
      <?php if ($this->action == 'privacy' || $this->action == 'style'  || $this->action == 'badges' || $this->action == 'manage-admins'): ?>
      <li class="sideNavItem <?php if ($this->action == 'manage-admins') echo 'selectedItem  he-active' ?>" id="team">
          <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'manage-admins', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">

              <?php echo $this->translate('PAGE_TEAM_MANAGE')?>

          </a>
      </li>
      <?php endif; ?>
    <?php if ($this->action == 'privacy' || $this->action == 'style'  || $this->action == 'badges' || $this->action == 'manage-admins'): ?>
      <li class="sideNavItem <?php if ($this->action == 'style') echo 'selectedItem  he-active' ?>" id="team">
        <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'style', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">

              <?php echo $this->translate('PAGE_Page Style')?>

        </a>
      </li>
    <?php endif; ?>
<?php if($this->action == 'privacy' || $this->action == 'style'  || $this->action == 'badges' || $this->action == 'manage-admins'){?>
    <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hebadge')):?>
      <li class="sideNavItem <?php if($this->action == 'badges') echo 'selectedItem  he-active'; ?>" id="badge">
        <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'badges', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">

              <?php echo $this->translate('HEBADGE_PAGE_MENUITEM')?>

        </a>
      </li>
    <?php endif; ?>
    <li >
        <?php echo $this->htmlLink( $this->url(array( 'action' => 'delete', 'page_id' => $this->page->getIdentity()), 'page_team'), $this->translate('Delete Page') ); ?>
    </li>
    <?php

}
    ?>


<?php if ($this->action == 'apps' ) { ?>
        <?php if ($this->isAllowPagecontact) : ?>
          <li class="subSideNavItem <?php if ($this->sub_menu == 'contact') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
            <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'contact'), 'page_team', true)?>">

                  <?php echo $this->translate('Contacts')?>

            </a>
          </li>
        <?php endif; ?>

        <?php if ($this->isAllowPagefaq) : ?>
          <li class="subSideNavItem <?php if ($this->sub_menu == 'faq') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
            <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'faq'), 'page_team', true)?>">

                  <?php echo $this->translate('FAQ')?>

            </a>
          </li>
        <?php endif; ?>

        <?php if ($this->isAllowInvite) : ?>
        <li class="subSideNavItem <?php if ($this->sub_menu == 'invite') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
          <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'invite'), 'page_team', true)?>">

                <?php echo $this->translate('Invite')?>

          </a>
        </li>
        <?php endif; ?>
    <?php if($this->page->getOwner()->getIdentity() ==  Engine_Api::_()->user()->getViewer()->getIdentity()): ?>
        <li class="subSideNavItem <?php if ($this->sub_menu == 'promote') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
          <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'promote'), 'page_team', true)?>">

                <?php echo $this->translate('Promote')?>

          </a>
        </li>
    <?php endif; ?>
        <li class="subSideNavItem <?php if ($this->sub_menu == 'update') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
          <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'update'), 'page_team', true)?>">

                <?php echo $this->translate('Send Update')?>

          </a>
        </li>

        <?php if( $this->page->isStore() && $this->isAllowStore && $this->page->getStorePrivacy()): ?>
          <li class="subSideNavItem <?php if ($this->sub_menu == 'store') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
            <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'store'), 'page_team', true)?>">

                  <?php echo $this->translate('Store')?>

            </a>
          </li>
        <?php endif; ?>

        <?php if($this->page->isDonation() && $this->isAllowDonation && ($this->page->getDonationPrivacy('charity') || $this->page->getDonationPrivacy('project'))): ?>
          <li class="subSideNavItem <?php if ($this->sub_menu == 'donation') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
            <a class="item clearfix_page" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'donation'), 'page_team', true)?>">

                    <?php echo $this->translate('DONATION_Donations')?>

            </a>
        </li>
        <?php endif; ?>

        <?php if($this->page->isOffers()) : ?>
          <li class="subSideNavItem <?php if ($this->sub_menu == 'offer') echo 'selectedItem  he-active' ?>" style="margin-left: 0px">
            <a class="item clearfix_page" href="<?php echo $this->url(array('page_id' => $this->page->getIdentity()), 'offer_page_backend', true)?>">

                  <?php echo $this->translate('OFFERS_Offers')?>

            </a>
          </li>
        <?php endif; ?>
   <?php }

?>




  </ul>
</div>
<?php }?>
<div class="page_edit_package global_form_box">

  <div class="profile_img <?php if($this->page->featured) echo 'edit_page_featured'?>">
    <?php if($this->page->sponsored): ?>
    <span class="sponsored_page"><?php echo $this->translate('SPONSORED')?></span>
    <?php endif;?>
    <?php echo $this->itemPhoto($this->page, 'thumb.profile', '', array('width' => 165)); ?>

    <?php if($this->page->featured):?>
    <div class="page_featured">
      <span><?php echo $this->translate('Featured')?></span>
    </div>
    <?php endif;?>
  </div>

  <div style="background-image: url(<?php echo $this->layout()->staticBaseUrl?>application/modules/Page/externals/images/approved<?php echo $this->page->approved?>.png);" class="approved_icon"><?php echo $this->translate('PAGE_page_approved' . $this->page->approved)?></div>

  <?php if ( $this->packageEnabled ): ?>
  <table width="100%" cellpadding="0" cellspacing="0">
    <?php if ( $this->package ): ?>
      <tr>
        <td class="label" colspan="2"><b><?php echo $this->translate("PAGE_Package"); ?><b></b></td>
      </tr>
      <tr>
        <td class="label"><?php echo $this->translate("Title"); ?>:</td>
        <td class="value"><?php echo $this->package->getTitle(); ?></td>
      </tr>
      <tr>
        <td class="label"><?php echo $this->translate("Price"); ?>:</td>
        <td class="value green"><?php echo $this->locale()->toCurrency($this->package->price, $this->currency); ?></td>
      </tr>
      <tr>
        <td class="label"><?php echo $this->translate("Expiration"); ?>:</td>
        <?php if( $this->subscription_expired ): ?>
          <td class="value red"><?php echo $this->locale()->toDateTime($this->subscription_expired)?></td>
        <?php else: ?>
          <td class="value green"><?php echo $this->translate('Never'); ?></td>
        <?php endif; ?>
      </tr>
      <tr>
        <td class="label" colspan="2" style="padding-top: 10px"><?php echo $this->package->getPackageDescription(); ?></td>
      </tr>

      <?php if( $this->isOwner ) : ?>
      <tr style="border-bottom: 0px">
        <td class="label" colspan="2" style="padding-top: 10px; text-align: center">
          <a href="<?php echo $this->url(array('page_id'=>$this->page->getIdentity()), 'page_package_choose'); ?>" style="text-decoration: none;">
            <button type="submit" style="padding: 3px"><?php echo $this->translate('PAGE_Change Package'); ?>&nbsp;&raquo;</button>
          </a>
        </td>
      </tr>
      <?php endif;?>
    <?php else: ?>
      <tr>
        <td class="label" colspan="2" style="padding-top: 10px"><?php echo $this->translate('PAGE_Your page does not have a package'); ?></td>
      </tr>

      <?php if( $this->isOwner ) : ?>
      <tr style="border-bottom: 0px">
        <td class="label" colspan="2" style="padding-top: 10px; text-align: center">
          <a href="<?php echo $this->url(array('page_id'=>$this->page->getIdentity()), 'page_package_choose'); ?>" style="text-decoration: none;">
            <button type="submit" style="padding: 3px"><?php echo $this->translate('PAGE_Upgrade'); ?>&nbsp;&raquo;</button>
          </a>
        </td>
      </tr>
      <?php endif;?>

    <?php endif; ?>
  </table>
  <?php endif; ?>
</div>
