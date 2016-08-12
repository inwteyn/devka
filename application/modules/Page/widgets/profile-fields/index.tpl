<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<div class="page_profile_fields">
    <ul>
        <?php if ($this->subject->getTitle()): ?>
            <li>
                <div class="profile_fields_name">
                    <i class="hei hei-home hei-lg"></i>
                    <span><?php echo $this->translate("Title"); ?></span>
                </div>
                <span class="profile_fields_content"><?php echo $this->subject->getTitle(); ?></span>
            </li>
        <?php endif; ?>
        <?php if ($this->subject->getDescription()): ?>
            <li>
                <div class="profile_fields_name">
                    <i class="hei hei-pencil hei-lg"></i>
                    <span><?php echo $this->translate("Description"); ?></span>
                </div>
                <span class="profile_fields_content"><?php echo $this->subject->getDescription(false, false, false); ?></span>
            </li>
        <?php endif; ?>
        <?php if ($this->subject->isAddress()): ?>
            <li>
                <div class="profile_fields_name">
                    <a class="smoothbox view-page-address-btn" href="<?php echo $this->url(array('page_id' => $this->subject->getIdentity()), 'page_map'); ?>">
                        <i class="hei hei-map-marker hei-lg"></i>
                    </a>
                    <span><?php echo $this->translate("Page Address"); ?></span>
                </div>
                <span class="profile_fields_content"> <?php echo $this->subject->getAddress();
                    echo $this->subject->getAdditionalAddressesText(); ?>
                </span>
            </li>
        <?php endif; ?>
        <?php if ($this->subject->website): ?>
            <li>
                <div class="profile_fields_name">
                    <i class="hei hei-globe hei-lg"></i>
                    <span><?php echo $this->translate("Website"); ?></span>
                </div>
                <span class="profile_fields_content"><?php echo $this->subject->getWebsite(); ?></span>
            </li>
        <?php endif; ?>
        <?php if ($this->subject->phone): ?>
            <li>
                <div class="profile_fields_name">
                    <i class="hei hei-phone hei-lg"></i>
                    <span><?php echo $this->translate("Phone"); ?></span>
                </div>
                <span class="profile_fields_content"><?php echo $this->subject->phone; ?></span>
            </li>
        <?php endif; ?>
    </ul>

    <?php echo $this->fieldValueLoop($this->subject, $this->fieldStructure); ?>
</div>

<script type="text/javascript">
    $$(".layout_page_profile_fields .tip").destroy();
    $$('.layout_page_profile_fields').each(function (elem) {
        if (elem.getParent('.layout_left_timeline')) {
            elem.getElements('.profile_fields_name span').each(function (e) {
                e.hide();
            });
            elem.getElements('.profile_fields_name').each(function (e) {
                e.setStyle('width', 'auto');
            });
            elem.getElements('.profile_fields_content').each(function (e) {
                e.setStyle('width', '92%');
            });
        }
    });
</script>
