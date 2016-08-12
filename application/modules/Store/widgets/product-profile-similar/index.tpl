<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<div class="">
  <ul class="similar-products-ul">
    <?php foreach ($this->products as $item): ?>
      <li class="sp">
        <div class="sp-wrapper">
          <div class="sp-photo">
            <a href="<?php echo $item->getHref() ?>">
              <?php $photo = $item->getPhotoUrl('thumb.profile'); ?>
              <img class="fake-img"
                   src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Store/externals/images/fake-4x3.gif"
                   alt="" style="background-image: url(<?php echo $photo; ?>)">
            </a>
          </div>
          <div class="sp-info">
            <div class="sp-info-title">
              <div style="/*float: left;*/">
                <h3>
                  <a href="<?php echo $item->getHref(); ?>" alt="<?php echo $item->getTitle(); ?>">
                    <?php echo $item->getTitle(); ?>
                  </a>
                </h3>
              </div>
              <div style="/*float: right;*/">
                <?php echo $this->getPriceBlock($item); ?>
              </div>
              <div style="clear :both;"></div>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>