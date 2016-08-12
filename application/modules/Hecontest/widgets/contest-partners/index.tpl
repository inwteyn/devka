<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<div class="hecontest_sponsor_label">
    <span><?php echo $this->translate("HECONTEST_Partners"); ?></span>
</div>
<div class="hecontest_partners_description">
    <p>
        <?php $href = $this->htmlLink($this->baseUrl() . "/help/contact", $this->translate("HECONTEST_Contact us")); ?>
        <?php echo $this->translate("HECONTEST_Partners text", $href); ?>
    </p>
</div>