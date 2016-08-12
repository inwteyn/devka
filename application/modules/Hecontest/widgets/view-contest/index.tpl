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

<div class="hecontest_view_label">
    <span>
        <?php echo $this->contest->getTitle(); ?>
    </span>
</div>

<div class="hecontest_sponsor_contest_description_wrapper">
    <div><p><?php echo $this->truncate($this->contest->description, 200); ?></p></div>
    <a class="hecontest-item" style="background-image: url('<?php echo $this->baseUrl() . $this->contest->getPhotoUrl(); ?>');">
    </a>
</div>

<div>
    <div>
        <?php $href = $this->htmlLink($this->sponsor['sponsor']['href'], $this->sponsor['sponsor']['title']); ?>
        <span><?php echo $this->translate("HECONTEST_Sponsored by", $href); ?></span>
        <button style="margin-top: 5px;" class="hecontest_widget_button" onclick="window.location.href='<?php echo $this->contest->getHref(); ?>';">
            <?php echo $this->translate("HECONTEST_View Contest"); ?>
        </button>
    </div>
</div>
