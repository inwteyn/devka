<?php if ($this->global): ?>
  <?php
  $items = $this->items;
  for($i=0;$i<$this->count_row_tipe;$i++){
    $name_category = $this->name_category;
    $name_category[$i];
    $itemscount = count($items[$name_category[$i]]);
    if(!$itemscount){
      continue;
    }else{


      ?>
      <a class="as_global_found_more_link" href="<?php echo $this->url(array('squery' => $this->query, 'stype' => $name_category[$i]), 'advancedsearch')?>">
        <div class="category_items_search">
          <p class="cat_item">
            <?php echo $this->translate('AS_'.$name_category[$i]);?>
          </p>
          <?php if($name_category[$i]=="store_product"){?>

          <?php }else{?>
            <p class="count_item"><?php echo $itemscount;?> results</p>
          <?php }?>
        </div>
      </a>
    <?php
    }
    foreach( $items[$name_category[$i]] as $item){
      if (!$item) continue; ?>

      <?php if($name_category[$i]=="store_product" && $item->getQuantity()==0){ ?>
      <?php }else{
        ?>
        <a id="link_item" style="display: block!important;  width: 100%;" href="<?php echo $item->getHref() ?>">
          <div class="as_global_search_result search_result">
            <div class="as_global_search_photo">
              <?php if ($item->getPhotoUrl() != ''): ?>
                <?php echo $this->itemPhoto($item, 'thumb.icon') ?>
              <?php else: ?>
                <img src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Advancedsearch/externals/images/nophoto_icon.png' ?>">
              <?php endif;?>
            </div>
            <div class="as_global_search_info">
              <span><?php echo $this->highlightText($item->getTitle(), $this->query) ?></span>
              <?php
              echo $this->itemSearch($item,$name_category[$i]);
              ?>
              <span style="color: #999999"></span>
            </div>
          </div>
          <div style="clear:left"></div>
        </a>
      <?php }?>

    <?php
    }
  }
  ?>
  <?php
  if($this->name_category[0]!=""){
    ?>
    <a class="as_global_found_more_link" href="<?php echo $this->url(array('squery' => $this->query, 'stype' => 'all'), 'advancedsearch')?>">
      <div class="as_global_found_more">
        <?php echo $this->translate('AS_show_all');?><?php echo $this->countItem;?>
      </div>
    </a>
  <?php }?>
<?php else:?>
  <?php
  if(@$this->items)
    foreach ($this->items as $item):
      $type = $item->type;
      if (!Engine_Api::_()->hasItemType($item->type)) {
        continue;
      }
      $checkItem = $item;
      $item = $this->item($item->type, $item->id);
      if (!$item) {
        Engine_Api::_()->advancedsearch()->deleteItem($checkItem->type, $checkItem->id);
        continue;
      } ?>


      <?php if($type=="store_product" && $item->getQuantity()==0){ ?>
    <?php }else{?>

      <div class="search_result">
        <div class="search_photo">
          <a href="<?php echo $item->getHref(); ?>">
            <?php if ($item->getPhotoUrl() != ''): ?>
              <span style="background-size: contain; background-position: center; width:200px; height: 135px;background-image: url(<?php echo $item->getPhotoUrl($this->imageTypes($type))?>);display: block;background-repeat: no-repeat"></span>
            <?php else:?>
              <span style="background-size: contain; background-position: center; width:200px; height: 135px;background-image: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Advancedsearch/externals/images/nophoto.png'?>);background-position: center 50%;display: block;"></span>
            <?php endif;?>
          </a>
        </div>
        <div class="search_info">
          <?php if( '' != $this->query ){ ?>
            <?php if($type=='donation' && $item->status != 'expired'){?>

              <?php $href='/making-donation/donate/object/donation/object_id/'.$item->getIdentity(); echo $this->htmlLink($href, $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
            <?php } else { ?>
              <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
            <?php }?>
          <?php }else{ ?>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
          <?php } ?>
          <p class="search_description2">
            <?php echo $this->translate('ITEM_TYPE_' . strtoupper($type))?>
            <?php if ($type == 'page') {
            echo ' <b>&#149;</b> '. $this->translate('AS_likes');?>
            <?php if(Engine_Api::_()->hasModuleBootstrap('like')){?>
          <div  class="item <?php echo (Engine_Api::_()->like()->isLike($item)) ? 'liked_item' : '' ?>" >
            <div onmouseover="listShowLike('<?php echo $item->getIdentity()?>')" onmouseout="listHideLike('<?php echo $item->getIdentity()?>')" style="position:relative">
              <div class="page_list_browser_likebox list_page_status_<?php echo $item->getIdentity()?>" >
                                         <span style="display:<?php echo (Engine_Api::_()->like()->isLike($item)) ? 'none' : 'block' ?>;" class="padding_button_and_link">
                                           <a href="javascript:void(0)" class="like_button_link list_like" onfocus="this.blur();" id="popuLike_<?php echo $item->getGuid(); ?>"><i class="hei hei-thumbs-o-up"></i><span class="like_button"><?php echo  $this->translate('like_Like'); ?></span></a>
                                         </span>
                                         <span style="display:<?php echo (Engine_Api::_()->like()->isLike($item)) ? 'block' : 'none' ?>;" class="padding_button_and_link">
                                           <a href="javascript:void(0)" class="like_button_link list_unlike" onfocus="this.blur();" id="popuUnlike_<?php echo $item->getGuid(); ?>"><i class="hei hei-thumbs-o-down"></i><span class="unlike_button"><?php echo  $this->translate('like_Unlike'); ?></span></a>
                                         </span>

              </div>
              <div class="page_button_loader hidden list_page_loader_like_<?php echo $item->getIdentity()?>"></div>
            </div>
          </div>
        <?php }?>
        <?php
        } elseif ($type == 'album' || $type == 'pagealbum') {
        }  elseif ($type == 'video' || $type == 'pagevideo') {
          echo ' <b>&#149;</b> '. $this->translate('AS_views');
        } elseif ($type == 'group' || $type == 'event' || $type == 'pageevent') {
        } elseif ($type == 'offer'){ ?>
          <?php $link_view = $this->url(array('action' => 'view', 'offer_id' => $item->getIdentity()), 'offers_specific'); ?>
          <div class="offer_view_button">
            <button name="submit" class="btn_view_offer" onclick='javascript:Offers.view("<?php echo $link_view; ?>");'
                    type="submit"><?php echo $this->translate('OFFERS_offer_view'); ?></button>
          </div>
        <?php } ?>
          </p>
          <p class="search_description2">
            By: <?php echo $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle()) ?>
          </p>
          <p class="search_description2">
            <?php if ($type == 'page') {  if(Engine_Api::_()->hasModuleBootstrap('like')){ echo '<b>'.$this->translate('AS_like_count').':</b><span class="padding_serch">'.$item->getLikesCount().'</span><br>'.'<b>Views:</b><span class="padding_serch">'.$item->getTotalViewsCount().'</span>'; }?>
            <?php } elseif ($type == 'album') {echo '<b>'.$this->translate('AS_photo_album').':</b><span class="padding_serch">'.$item->count().'</span>';?>
            <?php } elseif ($type == 'pagealbum') {echo '<b>'.$this->translate('AS_count_album_photos_in_page').':</b><span class="padding_serch">'.$item->count().'</span>';?>
            <?php }  elseif ($type == 'video' || $type == 'pagevideo') { echo '<b>'.$this->translate('AS_view').':</b><span class="padding_serch">'.$item->view_count.'</span>';?>
            <?php } elseif ($type == 'group') {echo '<b>'.$this->translate('AS_members_count').':</b></b><span class="padding_serch">'.$item->member_count.'</span>';?>
            <?php } elseif ($type == 'donation') {echo '<b>'.$this->translate('AS_raised').':</b><span class="padding_serch">'.$item->getRaised().'</span>';?>
            <?php } elseif ($type == 'hequestion') {echo '<b>'.$this->translate('AS_vote').':</b><span class="padding_serch">'.$item->vote_count.'</span>';?>
            <?php } elseif ($type == 'quiz') {echo '<b>'.$this->translate('AS_take').':</b><span class="padding_serch">'.$item->take_count.'</span>';?>
            <?php } elseif ($type == 'music_playlist' || $type == 'playlist') {echo '<b>'.$this->translate('AS_plays').':</b><span class="padding_serch">'.$item->play_count.'</span>';?>
            <?php } elseif ($type == 'event') {?>
            <span class="event_btn">
                <a id="join_as_button" href="event/<?php echo $item['event_id']?>"><?php echo $this->translate('Join') ?> </a>
              </span>
          <div><i class="hei hei-clock-o"></i> <?php echo $item->starttime; ?></div>
        <?php if ($item->location) { ?>
          <div>
            <i class="hei hei-map-marker"></i> <?php echo ' ';?><?php echo $this->htmlLink('http://maps.google.com/?q=' . urlencode($item->location), $item->location, array('target' => 'blank')) ?>
          </div>
        <?php } ?>
        <?php } elseif ($type == 'user') {echo '<b>'.$this->translate('AS_friend').':</b></b><span class="padding_serch">'.$item->member_count.'</span>'.'<br><b>'.$this->translate('AS_birthday').':</b><span class="padding_serch">'.$item->creation_date.'</span>';?>
          <div class='browsemembers_results_links'>
                             <span style="z-index: 99;">
                             <?php echo $this->userFriendship($item) ?>
                             </span>
          </div>
        <?php } elseif ($type == 'store_product') {echo '<b>'.$this->translate('AS_price').':</b><span class="padding_serch">'.$item->getPrice($item).'</span><br>'.'<b>Items available:</b><span class="padding_serch">'.$item->getQuantity().'<span>';?>
        <?php } elseif ($type == 'hecontest') {echo '<b>'.$this->translate('AS_date_end').': </b><span class="padding_serch">'.$item->date_end.'</span><br> '.'<b>Participants: </b><span class="padding_serch">'.$item->getParticipantsCount().'</span>';?>
          <?php if ($item->isActive() && $item->allowJoin()): ?>
            <div class="hecontest_participants_controls">
              <?php if (!$this->isParticipant): ?>
                <span class="padding_button_and_link">
                              <button class="hecontest_widget_button hecontest_join_button" onclick="hecontestCore.join(this, 1);">
                                <?php echo $this->translate("HECONTEST_Join"); ?>
                              </button>
                        </span>
              <?php else : ?>
                <?php echo $this->translate("HECONTEST_Already participant"); ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php } ?>
          </p>
          <p class="search_description">
            <?php if( '' != $this->query ): ?>
              <?php echo $this->highlightText($this->viewMore($item->getDescription()), $this->query); ?>
            <?php else: ?>
              <?php echo $this->viewMore($item->getDescription()); ?>
            <?php endif; ?>
          </p>
        </div>
      </div>
    <?php }?>
    <?php endforeach; ?>
<?php endif; ?>


