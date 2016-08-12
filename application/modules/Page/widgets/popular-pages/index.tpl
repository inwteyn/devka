<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-08 17:53 taalay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        like.init_like_buttons();
        list_like.init_list_like_buttons();
        var options = {
            url: "<?php echo $this->url(array('action' => 'show-content'), 'like_default'); ?>",
            delay: 300,
            onShow: function(tip, element){
                var miniTipsOptions2 = {
                    'htmlElement': '.he-hint-text',
                    'delay': 1,
                    'className': 'he-tip-mini',
                    'id': 'he-mini-tool-tip-id',
                    'ajax': false,
                    'visibleOnHover': false
                };

                internalTips2 = new HETips($$('.he-hint-tip-links'), miniTipsOptions2);
                Smoothbox.bind();
            }
        };

        var $thumbs = $$('.page_icon_title');
        var $mosts_hints = new HETips($thumbs, options);
    });

    function showLike(id) {
        var $like_box = $('page_status_' + id);

        if (window.likeboxes && window.likeboxes[id]) { window.clearTimeout(window.likeboxes[id]); }

        // $like_box.setStyle('display', 'block');
        //$like_box.getParent().getElement('.icon_view').addClass('likebox_hover');
    }

    function hideLike(id) {
        var $like_box = $('page_status_' + id);

        if (window.likeboxes == undefined) {
            window.likeboxes = [];
        }

        window.likeboxes[id] = window.setTimeout(function(){
            // $like_box.setStyle('display', 'none');
            // $like_box.getParent().getElement('.icon_view').removeClass('likebox_hover');
        }, 40);
    }

    function listShowLike(id) {
        var $like_box = $$('.list_page_status_' + id);

        if (window.likeboxes && window.likeboxes[id]) { window.clearTimeout(window.likeboxes[id]); }

        // $like_box.setStyle('display', 'block');
        //$like_box.getParent().getElement('.icon_view').addClass('likebox_hover');
    }

    function listHideLike(id) {
        var $like_box = $$('.list_page_status_' + id);

        if (window.likeboxes == undefined) {
            window.likeboxes = [];
        }

        window.likeboxes[id] = window.setTimeout(function(){
            // $like_box.setStyle('display', 'none');
            // $like_box.getParent().getElement('.icon_view').removeClass('likebox_hover');
        }, 40);
    }
</script>

<div class="page_list">
  <ul>
    <?php foreach($this->pages as $page): ?>
    <li>
      <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_page')), array('class' => 'page_profile_thumb item_thumb')); ?>
      <div class="item_info">
        <div class="item_name">
          <?php echo $this->htmlLink($page->getHref(), $page->getTitle(), array('class' => 'page_profile_title')); ?><br />
        </div>

        <div class="clr"></div>
        <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1):?>
          <div class="item_date">
            <?php echo $this->translate("Published by"); ?> <?php echo $this->htmlLink($page->owner->getHref(), $page->owner->getTitle()); ?>
          </div>
          <div class="clr"></div>
        <?php endif;?>
        <div class="page_list_submitted">
          <?php if (!empty($this->page_likes[$page->getIdentity()])) echo $this->page_likes[$page->getIdentity()]; else echo 0; ?> <?php echo $this->translate('likes this')?>
          | <?php echo $page->view_count ?> <?php echo $this->translate("views"); ?>
        </div>
      </div>


        <div  class="item <?php echo (Engine_Api::_()->like()->isLike($page)) ? 'liked_item' : '' ?>" >
            <div onmouseover="listShowLike('<?php echo $page->getIdentity()?>')" onmouseout="listHideLike('<?php echo $page->getIdentity()?>')" style="position:relative">
                <!--  <div class=" icon_view" style="background-image:url(<?php /*echo $ico*/?>)"></div>-->
                <div class="page_list_browser_likebox list_page_status_<?php echo $page->getIdentity()?>" >
                <span style="display:<?php echo (Engine_Api::_()->like()->isLike($page)) ? 'none' : 'block' ?>;">
                  <a href="javascript:void(0)" class="like_button_link list_like" onfocus="this.blur();" id="popuLike_<?php echo $page->getGuid(); ?>"><i class="hei hei-thumbs-o-up"></i><span class="like_button"><?php echo  $this->translate('like_Like'); ?></span></a>
                </span>
                <span style="display:<?php echo (Engine_Api::_()->like()->isLike($page)) ? 'block' : 'none' ?>;">
                  <a href="javascript:void(0)" class="like_button_link list_unlike" onfocus="this.blur();" id="popuUnlike_<?php echo $page->getGuid(); ?>"><i class="hei hei-thumbs-o-down"></i><span class="unlike_button"><?php echo  $this->translate('like_Unlike'); ?></span></a>
                </span>

                </div>
                <div class="page_button_loader hidden list_page_loader_like_<?php echo $page->getIdentity()?>"></div>
            </div>
        </div>

    </li>
    <?php endforeach; ?>
      <div class="create_own_page">
        <?php echo $this->htmlLink($this->url(array(), 'page_create'), $this->translate('Page_Create_Own_Page'));?>
      </div>
  </ul>
</div>

