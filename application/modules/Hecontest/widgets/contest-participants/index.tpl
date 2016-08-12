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
        <span><?php echo $this->translate("HECONTEST_Participants"); ?></span>
    </div>

    <div class="hecontest_participants">
        <?php if ($this->participants->getTotalItemCount()): ?>
            <ul class="hecontest_participants_list">
                <?php foreach ($this->participants as $item): $user = $item->getUser(); if(!$user) continue;?>
                    <li>
                        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array()); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <div class="tip">
                <span><?php echo $this->translate("HECONTEST_No participants"); ?></span>
            </div>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
<?php if ($this->contest->isActive() && $this->contest->allowJoin()): ?>
    <div class="hecontest_participants_controls">
        <?php if (!$this->isParticipant): ?>
            <span><?php echo $this->translate("HECONTEST_Feel free to join"); ?></span>
            <button class="hecontest_widget_button hecontest_join_button" onclick="hecontestCore.join(this, 1);">
                <?php echo $this->translate("HECONTEST_Join"); ?>
            </button>
        <?php else : ?>
            <?php echo $this->translate("HECONTEST_Already participant"); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>