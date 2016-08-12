<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10128 2014-01-24 18:47:54Z lucas $
 * @author     John
 */
?>
<div id="pages-likes-widget-container">

    <script type="text/javascript">

        function page_likes_list_prev() {
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'widget/index/name/page.page-likes/',
                data: {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: <?php echo sprintf('%d', $this->users->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function (res1, res2, res3) {
                    $('pages-likes-widget-container').set('html', res2[0].innerHTML);
                }
            }))
        }

        function page_likes_list_next() {
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'widget/index/name/page.page-likes/',
                data: {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    page: <?php echo sprintf('%d', $this->users->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function (res1, res2, res3) {
                    $('pages-likes-widget-container').set('html', res2[0].innerHTML);
                }
            }))
        }

    </script>

    <ul class='profile_friends page-likes-list' id="user_profile_friends">

        <?php foreach ($this->users as $user): ?>

            <li id="user_friend_<?php echo $user->getIdentity() ?>">

                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'profile_friends_icon')) ?>

                <div class='profile_friends_body'>
                    <div class='profile_friends_status'>
                      <span>
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                      </span>
                    </div>
                </div>

            </li>

        <?php endforeach ?>

    </ul>

        <div>

            <?php if ($this->users->getCurrentPageNumber() != 1): ?>
                <div id="user_profile_friends_previous" class="paginator_previous">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                        'onclick' => 'page_likes_list_prev()',
                        'class' => 'buttonlink icon_previous'
                    )); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->users->count() != $this->users->getCurrentPageNumber()): ?>
                <div id="user_profile_friends_next" class="paginator_next">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                        'onclick' => 'page_likes_list_next()',
                        'class' => 'buttonlink_right icon_next'
                    )); ?>
                </div>
            <?php endif; ?>

        </div>

</div>