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
    <span><?php echo $this->translate("HECONTEST_This Contest Sponsored by:"); ?></span>
</div>
<div class="hecontest_sponsor_info">
    <?php echo $this->contest->getSponsorHtml('hecontest_widget_like_btn'); ?>
</div>
<div class="hecontest_sponsor_contest_description_wrapper">
    <?php $href = '<a style="color:#5F93B4;cursor:pointer;" onclick="hecontestCore.join(this, 1);">' . $this->translate('HECONTEST_read more') .'</a>';?>
    
    <div><p><?php echo $this->truncate(htmlspecialchars(strip_tags($this->contest->description)), 200, ' '.$href); ?></p></div>
    <img src="<?php echo $this->contest->getPhotoUrl(); ?>">
</div>
<?php if ($this->contest->isActive()): ?>
<div>
    <?php if($this->viewer()->getIdentity()) : ?>
        <?php if (!$this->isParticipant): ?>
            <button id="hecontest_join_button" class="hecontest_widget_button hecontest_join_button" onclick="hecontestCore.join(this, 1);">
                <?php echo $this->translate("HECONTEST_Join"); ?>
            </button>
        <?php else : ?>
            <?php echo $this->translate("HECONTEST_Already participant"); ?>
        <?php endif; ?>
    <?php else: ?>
        <?php $href = $this->url(array('return_url' => '64-' . base64_encode( $this->url(array(), 'hecontest_general')) ), 'user_login'); ?>
        <button id="hecontest_join_button" class="hecontest_widget_button hecontest_join_button" onclick="document.location.href='<?php echo $href;?>';">
            <?php echo $this->translate("HECONTEST_Join"); ?>
        </button>
    <?php endif; ?>
</div>
<?php endif; ?>