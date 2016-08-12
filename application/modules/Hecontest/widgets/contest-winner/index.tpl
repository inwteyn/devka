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
    <span><?php echo $this->translate($this->lang); ?></span>
</div>

<div class="hecontest_photos">
    <ul><li>
            <a class="hecontest-item"
               href="<?php echo $this->url(array(), 'hecontest_general') . '#' . $this->winner->getIdentity(); ?>"
               style="background-image: url('<?php echo $this->baseUrl() . '' . $this->winner->getPreviewPhotoUrl(); ?>');">
            </a>

            <div class="hecontest-items-info">
                <a href="<?php echo $this->user->getHref(); ?>"><?php echo $this->user->getTitle(); ?></a>
            <span class="hecontest-info-like">
                <i class="hei hei-thumbs-up-alt"></i>
                <span>
                    <?php echo $this->winner->votes; ?>
                </span>
            </span>
            </div>
    </li></ul>
</div>

<div class="hecontest_winner_winner_info">
    <div>
        <div style="float: left; margin-right: 5px;">
            <?php
            echo $this->htmlLink(
                $this->user->getHref(),
                $this->itemPhoto($this->user, ' thumb.icon')
            );
            ?>
        </div>
        <div>
            <p style="line-height: 150%;">
                <?php
                $winner = $this->htmlLink($this->user->getHref(), $this->user->getTitle());
                $contest = $this->htmlLink($this->contest->getHref(), $this->contest->getTitle());
                echo $this->translate($this->text, $winner, $contest);
                ?>
            </p>
        </div>
        <div class="clear"></div>
    </div>
</div>