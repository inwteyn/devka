<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<div>
    <?php $total_items = $this->all_likes->getTotalItemCount(); ?>
    <?php if ($total_items == 0) : ?>
        <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>
    <?php else : ?>
        <ul class='like_tiles' id="user_tile_friends">
            <?php foreach ($this->all_likes as $like):
                $photoUrl = $like->getPhotoUrl('thumb.profile');
                $photoUrl = $photoUrl ? $photoUrl : 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
                ?>
                <li id="tile_friend_<?php echo $like->getIdentity() ?>">
                    <div>
                        <a class="tile-item-photo" href="<?php echo $like->getHref() ?>"
                           style="display: block;background-image: url(<?php echo $photoUrl ?>);"></a>
                        <a class="tile-item-title"
                           href="<?php echo $like->getHref() ?>"><?php echo $like->getTitle() ?></a>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif; ?>
</div>