<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2013-20-03 9:30:11 ulan t $
 * @author     Ulan T
 */

?>

<?php if(count($this->packages)) : ?>
<div class="package-list-layout">
    <div>
        <div class="package-list-description">
            <h3>
              <?php echo $this->translate('Page Packages'); ?>
            </h3>

            <p>
              <?php echo $this->translate('Avialable Page Packages List'); ?>
            </p>
        </div>

      <?php foreach($this->packages as $package) : ?>
        <div class="package_list">
            <div>
                <div class="package_title"><?php echo $package->getTitle();?></div>

                <div class="package_info">
                    <div>
                        <b><?php echo $this->translate('Price');?>:</b>
                        <div style="color: #FF0000"><?php echo $this->locale()->toCurrency($package->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'))?></div>
                    </div>

                    <div>
                        <b><?php echo $this->translate('PAGE_Auto Approved'); ?>:</b>
                        <div class="<?php if($package->autoapprove) echo 'he-hint-tip-links'?>"><?php echo ($package->autoapprove) ? $this->translate('Yes') : $this->translate('No'); ?></div>
                      <?php if($package->autoapprove) : ?>
                        <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Auto Approved_DESC')?></div>
                      <?php endif;?>
                    </div>

                    <div>
                        <b><?php echo $this->translate('PAGE_Sponsored'); ?>:</b>
                        <div class="<?php if($package->sponsored) echo 'he-hint-tip-links'?>"><?php echo ($package->sponsored) ? $this->translate('Yes') : $this->translate('No'); ?></div>
                      <?php if($package->sponsored) : ?>
                        <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Sponsored_DESC')?></div>
                      <?php endif;?>
                    </div>

                    <div>
                        <b><?php echo $this->translate('PAGE_Featured'); ?>:</b>
                        <div class="<?php if($package->featured) echo 'he-hint-tip-links'?>"><?php echo ($package->featured) ? $this->translate('Yes') : $this->translate('No'); ?></div>
                      <?php if($package->featured) : ?>
                        <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Featured_DESC')?></div>
                      <?php endif;?>
                    </div>

                    <div>
                        <b><?php echo $this->translate('Billing'); ?>:</b>
                        <div class=""><?php echo $package->getPackageDescription(); ?></div>
                    </div>

                    <div>
                        <b><?php echo $this->translate('PAGE_Column Change'); ?>:</b>
                        <div class="<?php if($package->edit_columns) echo 'he-hint-tip-links'?>"><?php echo ($package->edit_columns) ? $this->translate('Yes') : $this->translate('No'); ?></div>
                      <?php if( $package->edit_columns ) : ?>
                        <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Column Change_DESC')?></div>
                      <?php endif;?>
                    </div>

                    <div>
                        <b><?php echo $this->translate('PAGE_Layout Editor'); ?>:</b>
                        <div class="<?php if($package->edit_layout) echo 'he-hint-tip-links'?>"><?php echo ($package->edit_layout) ? $this->translate('Yes') : $this->translate('No'); ?></div>
                      <?php if( $package->edit_layout ) : ?>
                        <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Layout Editor_DESC')?></div>
                      <?php endif;?>
                    </div>

                  <?php foreach($this->available_modules as $key => $module):?>
                    <div>
                        <b><?php echo $this->translate($module); ?>:</b>
                        <div class="<?php if(in_array($key,is_array($package->modules) ? $package->modules : array())) echo 'he-hint-tip-links'?>"><?php echo (in_array($key,is_array($package->modules) ? $package->modules : array())) ? $this->translate('Yes') : $this->translate('No'); ?></div>
                      <?php if(in_array($key,is_array($package->modules) ? $package->modules : array())) : ?>
                        <div class="he-hint-text hidden"><?php echo $this->translate($module . '_DESC')?></div>
                      <?php endif;?>
                    </div>
                  <?php endforeach;?>

                </div>

                <div class="package_description"> <?php echo $package->description; ?> </div>
            </div>
        </div>
      <?php endforeach;?>

    </div>
</div>

<?php else:?>
  <?php echo $this->translate('There is no Packages');?>
<?php endif;?>