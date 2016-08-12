<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  04.11.11 15:51 TeaJay $
 * @author     Taalay
 */


$this->headTranslate(array(
  'HEBADGE_PAGE',
  'city',
  'tag',
  'category',
  'Adv Search'
));

?>
<?php echo $this->gmap_js; ?>
<?php if ($this->markers && $this->count > 0): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      pages_map.construct( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?> );
    });
  </script>
<?php endif; ?>

<script type="text/javascript">
  page_manager.widget_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
  page_manager.page_num = <?php echo $this->params['page']; ?>;
  page_manager.view_mode = '<?php echo $this->view; ?>';

  page_manager.badge = <?php echo (empty($this->params['badge'])) ? 0 : intval($this->params['badge'])?>;
  en4.core.runonce.add(function(){
    if (page_manager.badge) {
      if ($$('.hebadge_browse_pages_badge_' + page_manager.badge).length) {
        $('page_badge_info').innerHTML = '<span class="bold">' + $$('.hebadge_browse_pages_badge_' + page_manager.badge)[0].innerHTML + '</span> ' + en4.core.language.translate('HEBADGE_PAGE') + '. [<a href="javascript:void(0)" class="bold" onClick="page_manager.setBadge(0);">x</a>]</ul>';
        $('page_badge_info').removeClass('hidden');
      }
    } else {
      $('page_badge_info').innerHTML = "";
      $('page_badge_info').addClass('hidden');
    }
  });

  <?php if($this->pagin_true==1){  ?>
    send_request_pin = 1;
 <?php }else{ ?>
   send_request_pin = 0;
<?php  }  ?>

  en4.core.runonce.add(function(){
    var miniTipsOptions1 = {
      'htmlElement': '.he-hint-text',
      'delay': 1,
      'className': 'he-tip-mini',
      'id': 'he-mini-tool-tip-id',
      'ajax': false,
      'visibleOnHover': false
    };

    var internalTips1 = new HETips($$('.he-hint-tip-links'), miniTipsOptions1);
  });

	var internalTips2 = null;
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
  window.addEvent('domready', function(){
    if( "<?php echo $this->params['category_name']?>" ) {
      $('page_category_info').innerHTML = '<span class="bold">'+page_manager.truncate('<?php echo $this->params['category_name']; ?>', 10)+'</span> '+en4.core.language.translate('category')+'. <a href="javascript:void(0)" class="bold" onClick="page_manager.setCategory(0);">x</a>';
      $('page_category_info').removeClass('hidden');

      page_manager.sort_type = 'category';
      page_manager.sort_value = "<?php echo $this->params['category_name']?>";
    }
    var city = '<?php echo $this->params['city']?>';
    if('<?php echo $this->params['city'];?>') {
      $('page_city_info').removeClass('hidden');
      $('page_city_info').innerHTML = '<span class="bold">'+page_manager.truncate("<?php echo $this->params['city']?>", 10)+'</span> '+en4.core.language.translate('city')+'. <a href="javascript:void(0)" class="bold" onClick="page_manager.setLocation(0);">x</a>';
      page_manager.city = "<?php echo $this->params['city'];?>";
    }

    if("<?php echo $this->params['tag_name']?>") {
      $('page_tag_info').innerHTML = '<span class="bold">'+page_manager.truncate("<?php echo $this->params['tag_name']?>", 10)+'</span> '+en4.core.language.translate('tag')+'. <a href="javascript:void(0)" class="bold" onClick="page_manager.setTag(0);">x</a>';
      $('page_tag_info').removeClass('hidden');
      page_manager.sort_type = 'tag';
      page_manager.sort_value = "<?php echo $this->params['tag_name']?>";
    }
  });
</script>
<div class="layout_core_container_tabs fw_active_theme_<?php echo $this->activeTheme()?>">
  <div class="tabs_alt tabs_parent">
    <ul id="main_tabs">
      <li class="<?php if ($this->sort == 'recent') echo 'active'; ?>">
        <a class="page_sort_buttons"
           id="page_sort_recent"
           href="<?php echo $this->url(array('sort_type'=>'sort', 'sort_value'=>'recent'), 'page_browse_sort'); ?>"
           onclick="page_manager.setSort('recent'); return false;"><?php echo $this->translate("Recent")?></a>
      </li>
      <li class="<?php if ($this->sort == 'popular') echo 'active'; ?>">
        <a class="page_sort_buttons"
           id="page_sort_popular"
           href="<?php echo $this->url(array('sort_type'=>'sort', 'sort_value'=>'popular'), 'page_browse_sort'); ?>"
           onclick="page_manager.setSort('popular'); return false;"><?php echo $this->translate("Most Popular")?></a>
      </li>
      <li class="<?php if ($this->sort == 'sponsored') echo 'active'; ?>">
        <a class="page_sort_buttons"
           id="page_sort_sponsored"
           href="<?php echo $this->url(array('sort_type'=>'sort', 'sort_value'=>'sponsored'), 'page_browse_sort'); ?>"
           onclick="page_manager.setSort('sponsored'); return false;"><?php echo $this->translate("Sponsored")?></a>
      </li>
      <li class="<?php if ($this->sort == 'featured') echo 'active'; ?>">
        <a class="page_sort_buttons"
           id="page_sort_featured"
           href="<?php echo $this->url(array('sort_type'=>'sort', 'sort_value'=>'featured'), 'page_browse_sort'); ?>"
           onclick="page_manager.setSort('featured'); return false;"><?php echo $this->translate("Featured")?></a>
      </li>
      <span class="modes">

        <li class="view_mode_wrapper">
          <a class="<?php if($this->view == 'map'){echo 'active he_active';}?> map pages-view-types "  href="javascript://" onclick="page_manager.setView('map', $(this));">
              <i class="hei hei-map-marker"></i>
          </a>
          <div class="he-hint-text hidden"><?php echo $this->translate('Map'); ?></div>
        </li>


        <li class="view_mode_wrapper">
          <a class="<?php if($this->view == 'list'){echo 'active he_active';} ?> list pages-view-types "  href="javascript://" onclick="page_manager.setView('list', $(this));">
              <i class="hei hei-th-list"></i>
          </a>
          <div class="he-hint-text hidden"><?php echo $this->translate('List'); ?></div>
        </li>
           <li class="view_mode_wrapper">
             <a class="<?php if($this->view == 'icons'){echo 'active he_active';} ?> icons pages-view-types " href="javascript://" onclick="page_manager.setView('icons', $(this));">
               <i class="hei hei-th-large"></i>
             </a>
             <div class="he-hint-text hidden"><?php echo $this->translate('Icons'); ?></div>
           </li>
      </span>
      <a id="page_loader_browse" class="page_loader_browse hidden"><?php echo $this->htmlImage($this->baseUrl().'/application/modules/Page/externals/images/loader.gif', ''); ?></a>
    </ul>
  </div>
</div>

<table>
  <tr>
    <td>
      <span id="page_tag_info" class="page_tag_info hidden"></span>
    </td>
    <td>
      <span id="page_city_info" class="page_city_info hidden"></span>
    </td>
    <td>
      <span id="page_category_info" class="page_category_info hidden"></span>
    </td>
    <td>
      <span id="page_badge_info" class="page_badge_info hidden"></span>
    </td>
    <td>
      <span id="page_adv_info" class="page_adv_info hidden"></span>
    <td/>
  </tr>
</table>

<div class="clr"></div>

<?php if( $this->count > 0 ): ?>
  <div style="position: relative;">
    <div id="page_map_cont" style="overflow: hidden;">
      <div id="map_canvas" class="browse_gmap" style="<?php echo ($this->view == 'map') ? 'position:relative;top:0;' : 'position:absolute;top:100000px;'; ?>">
        <?php if (!($this->markers && $this->count > 0)): ?>
          <ul class="form-notices"><li><?php echo $this->translate('There is no location data'); ?></li></ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <ul class="page_list_items" id="page_list_cont" style="display:<?php echo  ($this->view == 'list') ? 'block' : 'none'; ?>;">

    <?php foreach( $this->paginator as $page ): ?>
      <?php $page_id = $page->getIdentity(); $classPageActiveOffer = '';  $classBackgroundColor = ''; ?>

      <?php if (count($this->pagesIdsActiveOffers)) : ?>
        <?php foreach ($this->pagesIdsActiveOffers as $pageActiveOffer) : ?>
          <?php if ($pageActiveOffer->page_id == $page_id) : ?>
            <?php
              $classPageActiveOffer = 'page_active_offer_icon';
              $classBackgroundColor = 'page_active_offer_background_color';
            ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <li class="<?php if($page->sponsored) echo 'page_list_item_sponsored' ?> <?php echo $classBackgroundColor; ?>" >

        <div class="page_list_item_photo <?php  if($page->featured) echo 'featured_page' ?>">
          <a href="<?php echo $page->getHref()?>">
            <span style="background-image: url(<?php echo $page->getPhotoUrl('thumb.normal'); ?>);">
            </span>
          </a>

          <?php if($page->featured):?>
          <div class="page_featured">
            <span><?php echo $this->translate('Featured')?></span>
          </div>
          <?php endif;?>
          <?php if( $page->sponsored ) :?>
            <div class="sponsored_page"><?php echo $this->translate('SPONSORED')?></div>
          <?php endif;?>
        </div>

        <div class="page_list_item_info">
          <?php if ($classPageActiveOffer == 'page_active_offer_icon'): ?>
            <img class="page_active_offer_icon" title="<?php echo $this->translate("OFFERS_PageOffers"); ?>" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Offers/externals/images/oftype_free.png">
          <?php endif; ?>


            <div  class="item <?php echo (Engine_Api::_()->like()->isLike($page)) ? 'liked_item' : '' ?>" >
                <div onmouseover="listShowLike('<?php echo $page->getIdentity()?>')" onmouseout="listHideLike('<?php echo $page->getIdentity()?>')" style="position:relative">
                    <!--  <div class=" icon_view" style="background-image:url(<?php /*echo $ico*/?>)"></div>-->
                    <div class="page_list_browser_likebox list_page_status_<?php echo $page->getIdentity()?>" >
                <span style="display:<?php echo (Engine_Api::_()->like()->isLike($page)) ? 'none' : 'block' ?>;">
                  <a href="javascript:void(0)" class="like_button_link list_like" onfocus="this.blur();" id="listLike_<?php echo $page->getGuid(); ?>"><i class="hei hei-thumbs-o-up"></i><span class="like_button"><?php echo  $this->translate('like_Like'); ?></span></a>
                </span>
                <span style="display:<?php echo (Engine_Api::_()->like()->isLike($page)) ? 'block' : 'none' ?>;">
                  <a href="javascript:void(0)" class="like_button_link list_unlike" onfocus="this.blur();" id="listUnlike_<?php echo $page->getGuid(); ?>"><i class="hei hei-thumbs-o-down"></i><span class="unlike_button"><?php echo  $this->translate('like_Unlike'); ?></span></a>
                </span>

                    </div>
                    <div class="page_button_loader hidden list_page_loader_like_<?php echo $page->getIdentity()?>"></div>
                </div>
            </div>
          <div class="page_list_title">
            <a href="<?php echo $page->getHref(); ?>">
              <?php echo $page->getTitle(); ?>
            </a>
          </div>
          <div class="page_list_info">

            <div class="l">
              <div class="page_list_rating">
                <?php echo $this->itemRate('page', $page_id); ?>
              </div>
              <br/>
              <div class="page_list_submitted">
                <?php echo $this->timestamp($page->creation_date); ?> -
                
                <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1):?>
                  <?php echo $this->translate("Posted by"); ?>
                  <a href="<?php echo $page->getOwner()->getHref(); ?>"><?php echo $page->getOwner()->getTitle(); ?></a>
                <?php endif;?>
                <?php echo $this->translate('in')?>
              <?php $caetoryLabel = $page->category? $page->category_set.($page->category_id != 1? ' - '.$page->category: '') : $page->category; ?>
              <a class="category_<?php echo $page->category_id?>" href="javascript:page_manager.setCategory( <?php echo($page->set_id);?> , <?php echo $page->category_id; ?>);"></s><?php echo $caetoryLabel; ?></a>
                <?php echo $this->translate('category');?>
                | <?php if (!empty($this->page_likes[$page_id])) echo $this->page_likes[$page_id]; else echo 0; ?> <?php echo $this->translate('likes this')?>
                | <?php echo $page->view_count ?> <?php echo $this->translate("views"); ?>
              </div>


            </div>
            <div class="clr"></div>
            <div class="page_list_desc"><?php echo $page->getDescription(true, true, false, 200); ?></div>
            <div class="r">
                 <?php if ($page->country || $page->city || $page->state): ?>
                     <a href="<?php echo $this->url(array('page_id' => $page->getIdentity()), 'page_map', true); ?>" class="smoothbox">
                     <i class="hei hei-map-marker"></i>
                 <span>
                   <?php echo $page->displayAddress(); ?>
                 </span>
                     </a>
                 <?php endif; ?>
                 <div class="clr"></div>
            </div>
          </div>
        </div>

      </li>

    <?php endforeach; ?>
      <?php if( $this->paginator->getTotalItemCount() > 1 ): ?>
          <?php echo $this->paginationControl($this->paginator, null, array("pagination/index.tpl","page")); ?>
      <?php endif; ?>
  </ul>


    <ul  id="page_icons_cont" class="page_icons_items" style="display:<?php echo ($this->view == 'icons') ? 'block' : 'none';  ?>; ">
      <div class="pin_page" id="pin_page1" style="width: 33%; float: left">
      </div >
      <div class="pin_page" id="pin_page2" style=" width: 33%; float: left">
      </div>
      <div class="pin_page" id="pin_page3" style="width: 33%; float: left; ">
      </div>

      <?php foreach( $this->paginator_pin as $page ): ?>
        <?php $page_id = $page->getIdentity(); $classPageActiveOffer = '';  $classBackgroundColor = ''; ?>

        <?php if (count($this->pagesIdsActiveOffers)) : ?>
          <?php foreach ($this->pagesIdsActiveOffers as $pageActiveOffer) : ?>
            <?php if ($pageActiveOffer->page_id == $page_id) : ?>
              <?php
              $classPageActiveOffer = 'page_active_offer_icon';
              $classBackgroundColor = 'page_active_offer_background_color';
              ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>

        <li class="pin_items" style="width:95%;   float: left;    margin: 10px;">

          <div class="page_list_item_photo <?php  if($page->featured) echo 'featured_page' ?>">
            <a href="<?php echo $page->getHref()?>" style=" display: block;   padding: 0px;    width: 100%;">
              <img src="<?php echo $page->getPhotoUrl(); ?>" style=" width: 100%;display: block;" >


          <!--  <span style="background-image: url(<?php /*echo $page->getPhotoUrl(); */?>);    background-size: 100% auto;   height: 150px; width: 100%;">-->


            </span>
            </a>

            <?php if($page->featured):?>
              <div class="page_featured" >
                <span><?php echo $this->translate('Featured')?></span>
              </div>
            <?php endif;?>
            <?php if ($classPageActiveOffer == 'page_active_offer_icon'): ?>
              <img style="  margin: 5px;position: absolute;right: 0;top: 0;" class="page_active_offer_icon" title="<?php echo $this->translate("OFFERS_PageOffers"); ?>" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Offers/externals/images/oftype_free.png">
            <?php endif; ?>
           

          </div>

          <div class="page_list_item_info" >
           <?php if( $page->sponsored ) :?>
              <div class="sponsored_page pin_sponsored"><?php echo $this->translate('SPONSORED')?></div>
            <?php endif;?>
            <div class="page_list_title" >
              <a href="<?php echo $page->getHref(); ?>">
                <?php echo $page->getTitle(); ?>
              </a>
            </div>
            <div class="page_list_info">

              <div class="l" >
                <div class="page_list_rating">
                  <?php echo $this->itemRate('page', $page_id); ?>
                </div>


                  <div  class="item <?php echo (Engine_Api::_()->like()->isLike($page)) ? 'liked_item' : '' ?>" >
                  <div onmouseover="listShowLike('<?php echo $page->getIdentity()?>')" onmouseout="listHideLike('<?php echo $page->getIdentity()?>')" style="position:relative">
                    <!--  <div class=" icon_view" style="background-image:url(<?php /*echo $ico*/?>)"></div>-->
                      <div class="page_list_browser_likebox list_page_status_<?php echo $page->getIdentity()?>" >
                <span style="display:<?php echo (Engine_Api::_()->like()->isLike($page)) ? 'none' : 'block' ?>;">
                  <a href="javascript:void(0)" class="like_button_link list_like" onfocus="this.blur();" id="pageLike_<?php echo $page->getGuid(); ?>"><i class="hei hei-thumbs-o-up"></i><span class="like_button"><?php echo  $this->translate('like_Like'); ?></span></a>
                </span>
                <span style="display:<?php echo (Engine_Api::_()->like()->isLike($page)) ? 'block' : 'none' ?>;">
                  <a href="javascript:void(0)" class="like_button_link list_unlike" onfocus="this.blur();" id="pageUnlike_<?php echo $page->getGuid(); ?>"><i class="hei hei-thumbs-o-down"></i><span class="unlike_button"><?php echo  $this->translate('like_Unlike'); ?></span></a>
                </span>
                      </div>
                      <div class="page_button_loader hidden list_page_loader_like_<?php echo $page->getIdentity()?>"></div>
                  </div>
                      </div>
                <div class="page_list_submitted" >
                  <?php if (!empty($this->page_likes[$page_id])) echo $this->page_likes[$page_id]; else echo 0; ?> <?php echo $this->translate('likes')?>
                  | <?php echo $page->view_count ?> <?php echo $this->translate("views"); ?>
                </div>

              </div>


            </div>
          </div>

        </li>

      <?php endforeach; ?>
      <div id="temp_page_el"></div>
      <?php if($this->not != 1){?>
      <script>
        var options = {
          autoResize: true, // This will auto-update the layout when the browser window is resized.
          container: $('pinfeed'),
          item: $$('.pin_items'),
          offset: 2,
          itemWidth: 255,
          bottom: 0,
          column_count: 3
        };
        window.addEvent('domready', function() {

        })


      </script>
        <div style="clear: both; height: 30px"></div>
        <div class="pin-loader" id="pin-loader"></div>
    <?php }?>
    </ul>


<?php if($this->view == 'icons'){
        ?>
       <script type="text/javascript">
                    setTimeout(function(){
                        page_manager.setView('icons', $(this));
                        $$('.paginationControl').setStyle('display', 'none');
                    },500)

         </script>
    <?php }?>





<?php else: ?>
  <div class="tip"><span><?php echo $this->translate("There is no pages"); ?></span></div>
<?php endif;?>
