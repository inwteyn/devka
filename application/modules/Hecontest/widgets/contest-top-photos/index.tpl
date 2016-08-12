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
    <span><?php echo $this->translate('HECONTEST_Top Participants', $this->htmlLink($this->contest->getHref(), $this->contest->getTitle(), array())); ?></span>
</div>

<div class="hecontest_photos">
    <ul>
        <?php foreach($this->participants as $item) : ?>
            <?php $user = $item->getUser(); if(!$user) continue; ?>
            <li >
                <a class="hecontest-item"
                   href="<?php echo $this->url(array(), 'hecontest_general') . '#' . $item->getIdentity(); ?>"
                   onclick="hecontestCore.contest='<?php echo $this->contest->getIdentity(); ?>';"
                   style="background-image: url('<?php echo $item->getPreviewPhotoUrl(); ?>');">
                </a>

                <div class="hecontest-items-info">
                    <a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
                        <span class="hecontest-info-like">
                            <?php if($item->allowLike($this->viewer()->getIdentity())) : ?>
                                <i class="hei hei-thumbs-up-alt" onclick="hecontestCore.vote(this, '<?php echo $item->getIdentity();?>', '<?php echo $this->contest->getIdentity(); ?>')"></i>
                            <?php else :?>
                                <i class="hei hei-thumbs-up-alt"></i>
                            <?php endif; ?>
                            <span>
                                <?php echo $item->votes; ?>
                            </span>
                        </span>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="<?php echo $this->url(array(), 'hecontest_general'); ?>">
        <?php echo $this->translate('HECONTEST_View all participants'); ?>
    </a>
</div>
