<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<style>
  .view_mode_block{ height: 40px; margin-right: 20px;}
  .view_mode_wrapper{list-style: none}
  .view_mode_non_block{display: none}
</style>

<div class='browsemembers_results' id='browsemembers_results' style="display: inline-block;">
  <div class="view_mode_block">
    <span class="modes">

        <li class="view_mode_wrapper">
          <a class="map pages-view-types" href="javascript://" onclick="set_view_mode(3);">
            <i class="hei hei-map-marker"></i>
          </a>

        </li>


        <li class="view_mode_wrapper">
          <a class="list pages-view-types" href="javascript://" onclick="set_view_mode(1);">
            <i class="hei hei-th-list"></i>
          </a>

        </li>
           <li class="view_mode_wrapper">
             <a class="icons pages-view-types" href="javascript://" onclick="set_view_mode(2);">
               <i class="hei hei-th-large"></i>
             </a>
           </li>
      </span>
  </div>
  <div id="search_box" class=""><input type="text" class="search_headvanced_members" id="search_headvanced_members" placeholder="<?php echo $this->translate('Search')?>"/>
    <button style="font-size: 13px;margin: 0px 0px 10px 5px;"><i class="hei hei-search"></i> <?php echo $this->translate('Search');?></button></div>
  <div id="hememberslist1" style="display: none">
  </div>
  <div id="hememberslist">
  <?php

      if($this->mode == 0)
      {
        $s1 ='view_mode_non_block';
        $s3 ='view_mode_non_block';
      }elseif($this->mode == 1){
        $s2 ='view_mode_non_block';
        $s3 ='view_mode_non_block';
      }elseif($this->mode == 2){
        $s2 ='view_mode_non_block';
        $s1 ='view_mode_non_block';
      }
        echo '<div style="position: relative;" ><div class="map_block_view"></div>';
        echo $this->render('_browseMap.tpl');
        echo '</div>';

        echo '<div class="'.$s1.'"><div class="large_block_view"></div>';
        echo $this->render('_browseUsersLarge.tpl');
        echo '</div>';

        echo '<div class="'.$s2.'"><div class="mini_block_view"></div>';
        echo $this->render('_browseUsers.tpl');
        echo '</div>';



  ?>

  </div>
</div>
<div class="bg_form_headvuser" id="bg_form_headvuser"></div>
<div class="container_form_headvuser" id="container_form_headvuser"></div>

<script type="text/javascript">

  var type_view =<?=$this->mode?>;
  function set_view_mode(num)
  {


    if(num == 1){
      $$('.large_block_view').getParent().addClass('view_mode_non_block');
      $$('.map_block_view').getParent().addClass('view_mode_non_block');
      $$('.mini_block_view').getParent().removeClass('view_mode_non_block'); //open mini
      $$('#search_box').removeClass('view_mode_non_block');
      type_view = 0;
    }
    if(num == 2){
      $$('.mini_block_view').getParent().addClass('view_mode_non_block');
      $$('.map_block_view').getParent().addClass('view_mode_non_block');
      $$('.large_block_view').getParent().removeClass('view_mode_non_block'); //open large
      $$('#search_box').removeClass('view_mode_non_block');
      type_view = 1;
    }
    if(num == 3){
      $$('.mini_block_view').getParent().addClass('view_mode_non_block');
      $$('.large_block_view').getParent().addClass('view_mode_non_block');
      $('map_canvas').setStyle('position','relative');
      $('map_canvas').setStyle('top','0');
      $$('#search_box').addClass('view_mode_non_block');
      $$('.map_block_view').getParent().removeClass('view_mode_non_block'); //open map
      type_view = 2;
    }
    set_icon_vis();
  }
  function set_icon_vis()
  {
    if(type_view == 0){
      $$('.hei-th-list').getParent().addClass('active he_active');    //active min
      $$('.hei-th-large').getParent().removeClass('active he_active');
      $$('.hei-map-marker').getParent().removeClass('active he_active');
    }
    if(type_view == 1){
      $$('.hei-th-large').getParent().addClass('active he_active');   //active large
      $$('.hei-th-list').getParent().removeClass('active he_active');
      $$('.hei-map-marker').getParent().removeClass('active he_active');
    }
    if(type_view == 2){
      $$('.hei-map-marker').getParent().addClass('active he_active');  //active map
      $$('.hei-th-large').getParent().removeClass('active he_active');
      $$('.hei-th-list').getParent().removeClass('active he_active');
      $$('#search_box').addClass('view_mode_non_block');
    }
  }
  set_icon_vis();
</script>

<script type="text/javascript">

  window.addEvent('domready',HeadvancedMembers.init());
  en4.core.runonce.add(function() {
    var url = '<?php echo $this->url(array(), 'user_general', true) ?>';
    var requestActive = false;
    var browseContainer, formElement, page, totalUsers, userCount, currentSearchParams;

    formElement = $$('.layout_user_browse_search .field_search_criteria')[0];
    browseContainer = $('browsemembers_results');


    var searchMembers = window.searchMembers = function() {
      if( requestActive ) return;
      requestActive = true;

      currentSearchParams = formElement ? formElement.toQueryString() : null;

      var param = (currentSearchParams ? currentSearchParams + '&' : '') + 'ajax=1&format=html';
      if (history.replaceState){
        history.replaceState( {}, document.title, url + (currentSearchParams ? '?'+currentSearchParams : '') );
      }
      var request = new Request.HTML({
        url: url,
        onComplete: function(requestTree, requestHTML) {
          requestTree = $$(requestTree);
          browseContainer.empty();
          requestTree.inject(browseContainer);
          requestActive = false;
          Smoothbox.bind();
        }
      });
      request.send(param);
    }

    var browseMembersViewMore = window.browseMembersViewMore = function() {
      if( requestActive ) return;
      $('browsemembers_loading').setStyle('display', '');
      $('browsemembers_viewmore').setStyle('display', 'none');

      var param = (currentSearchParams ? currentSearchParams + '&' : '') + 'ajax=1&format=html&page=' + (parseInt(page) + 1);

      var request = new Request.HTML({
        url: url,
        onComplete: function(requestTree, requestHTML) {
          requestTree = $$(requestTree);
          browseContainer.empty();
          requestTree.inject(browseContainer);
          requestActive = false;
          Smoothbox.bind();
        }
      });
      request.send(param);
    }
  });
</script>
