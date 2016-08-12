<?php
$headScript = new Zend_View_Helper_HeadScript();
$headScript->appendFile('application/modules/Page/externals/scripts/core.js');
?>
<div>

    <h3>Search</h3>

  <span id="as_default_type" class="as_default_search_type">
    <?php

    if ($this->stype && $this->stype != 'all'){
      echo $this->translate(strtoupper('ITEM_TYPE_' . $this->stype));}else{
     echo $this->translate('Everywhere');
      $this->stype='all';
    }?>

  </span>
 <div style="clear: both"></div>
    <input type="text" class="as_query_input" style="padding: 5px 50px 5px 0px;"
           value="<?php if (isset($_GET['query'])){
                    echo $_GET['query'];
                    }else{if ($this->squery) echo $this->squery;}
                   ?>" id="query" name="query">
     <img id="as_loading"
         src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'
         style="margin-top: -5px;vertical-align: middle; display: none"/>

<br><br>
<!-------------------------Menu---------------------------------->
<div id="#container_for_search">
     <div class="layout_core_container_tabs">
       <div class="menu_tabs_search">

         <input id="tab1" type="radio" name="tabs" checked>
         <label for="tab1" id="a">
           <div class="as_type_container <?php if ($this->stype == 'all') echo 'active'; ?>">
             <span data-type="all" style="padding: 11px 20px;display: inline-block;">
                  <?php echo $this->translate('Everywhere'); ?>
             </span>
             </div>
         </label>

         <!--Testing-->
         <div class="tab-buttons">
           <!--Tab Button Cantainer-->
             <ul class="he-nav he-nav-tabs"> <!--Tabs-->
                 <?php $cnt = 0; $last = 0;

                 foreach ($this->types as $key=> $tab): ?>

                     <?php if(!$tab['childCount']  && $tab['childCount'] != -2) continue; $last++;?>
                     <?php $tabCnt = $tab['childCount']; ?>
                     <?php if ($cnt < 5): ?>
                         <li <?php if ($cnt == 0) {
                             echo 'class="he-active"';
                         } ?>>
                           <label class="a_dropdown" for="tab<?php echo $tab['id'];?>">
                             <div class="as_type_container <?php if ($this->stype == $tab) echo 'active'; ?>">
                             <span data-type="<?php echo $tab; ?>" style="padding: 11px 25px;display: inline-block;">
                             <?php echo $this->translate(strtoupper('ITEM_TYPE_' . $tab));?>
                             </span>
                             </div>
                           </label>
                         </li>
                   <?php $cnt++;
                   unset($this->types[$key]);
                   endif; ?>
                 <?php endforeach; ?>

                 <?php if ($last > $cnt): ?>
                     <li class="he-dropdown">
                         <a data-toggle="dropdown" class="he-dropdown-toggle" id="myTabDrop1"
                            href="javascript://">More
                             <b class="he-caret"></b></a>
                         <ul aria-labelledby="myTabDrop1" role="menu" class="he-dropdown-menu" id="dropdown">

                             <?php foreach ($this->types as $tab):?>
                                 <?php if(!$tab['childCount'] &&  $tab['childCount'] != -2) continue;?>
                                 <?php $tabCnt = $tab['childCount']; ?>
                                 <li>
                                   <label class="a_dropdown" for="tab<?php echo $tab['id'];?>">
                                     <div class="as_type_container <?php if ($this->stype == $tab) echo 'active'; ?>">
                                     <span data-type="<?php echo $tab; ?>" style="padding: 17px 25px;display: inline-block;">
                                     <?php echo $this->translate(strtoupper('ITEM_TYPE_' . $tab));?>
                                     </span>
                                      </div>
                                   </label>
                                 </li>
                             <?php

                             endforeach; ?>
                         </ul>
                     </li>
                 <?php endif; ?>
             </ul>
         </div>
         <!--END_Testing_Blok-->


        </div>
       <div style="clear: both"></div>
     </div>
<!-------------------------End_Menu---------------------------------->
<!-------------------------Content---------------------------------->
         <section id="browsemembers_results" class="browsemembers_results">
           <div style="position: absolute">
                  <div class="advancedsearch_types_list" id="advancedsearch_types_list" style="display: none">

                  </div>
             <input type="hidden" id="type" value="<?php if ($this->stype) echo $this->stype; ?>" name="type"></div>
                       <div id="as_found_items"></div>
                       <div style="clear: both"></div>
           </div>
        </section>
    </div>
 </div>
<!-------------------------END_Content---------------------------------->

<div class="as_more_button">
    <span id="more_btn"><?php echo $this->translate('More') ?></span>
</div>

</div>

<script type="text/javascript" data-cfasync="false">
    var asSearch;
    window.addEvent('domready', function () {
        var check = false;
        var page = 1;
        if ($('query').get('value') != '' && $('query').get('value').length > 2) {
            var size = $('as_default_type').getSize();
            var padd = parseInt(size.x) + 4;


          $('query').setStyle('padding-left', padd);
            search(false, page);
        }

        $('more_btn').addEvent('click', function () {
            if (!check) {
                ++page;
                search(true, page);
            }
        });

      var type_list = $('advancedsearch_types_list');
        type_list.setStyle('display', 'none');
        type_list.setStyle('opacity', '0');
        asSearch = new Fx.Slide($('advancedsearch_types_list')).hide();
        $('as_default_type').addEvent('click', function () {
            if ($('advancedsearch_types_list').getParent().getStyle('overflow') == 'hidden') {
                type_list.getParent().setStyle('overflow', 'visible');
                type_list.setStyle('display', 'block');
                type_list.setStyle('opacity', '1');
                asSearch.show();
            } else {
                type_list.getParent().setStyle('overflow', 'hidden');
                type_list.setStyle('display', 'none');
                type_list.setStyle('opacity', '0');
                asSearch.hide();
            }
        });


      //фигня для кнопок итд
        $$('body').addEvent('keyup', function (event) {
            if (event.key == 'esc') {
                type_list.setStyle('opacity', '0');
                type_list.setStyle('display', 'none');
                asSearch.hide();
                type_list.getParent().setStyle('overflow', 'hidden');
            }
        });
        $$('body').addEvent('click', function (e) {
            if ($(e.target).get('id') != 'as_default_type') {
                type_list.setStyle('opacity', '0');
                type_list.setStyle('display', 'none');
                asSearch.hide();
                type_list.getParent().setStyle('overflow', 'hidden');
            }
        });


        $$('.as_type_container').addEvent('click', function () {
            $$('.as_type_container').removeClass('active');
            var size = $(this).getChildren('span').getSize();
            var padd = parseInt(size[0].x);
            $('query').setStyle('padding-left', padd);
            $('type').set('value', $(this).getChildren('span').get('data-type'));
            $('as_default_type').set('html', $(this).getChildren('span').get('html'));
            $(this).toggleClass('active');
            type_list.setStyle('opacity', '0');
            type_list.setStyle('display', 'none');
            asSearch.hide();
            type_list.getParent().setStyle('overflow', 'hidden');
            page = 1;
            check = false;
            search(false, 1);
        });

        //запускает поиск полсе введения 2 символа
        $('query').addEvent('keyup', function () {
            if ($(this).get('value').length > 2) {
                check = false;
                page = 1;
                search(false, 1);
            } else {

                $$('as_found_items').set('html', '');
            }
        });


        $(window).addEvent('scroll', function () {
              var totalHeight, currentScroll, visibleHeight;

            if (document.documentElement.scrollTop) {
                currentScroll = document.documentElement.scrollTop;
            }
            else {
                currentScroll = document.body.scrollTop;
            }

            totalHeight = document.body.offsetHeight;

            visibleHeight = document.documentElement.clientHeight;
            if (totalHeight <= currentScroll + visibleHeight && $('as_loading').getStyle('display') == 'none' && !check) {
                ++page;
                search(true, page);
            }
        });

        function search(append, page) {
            var query = $('query').get('value');
            var type = $('type').get('value');
            if (query != '') {
                $('as_loading').setStyle('display', 'inline');
                var url = '<?php echo $this->url(array('action' => 'search'), 'advancedsearch')?>';
                var jsonRequest = new Request.JSON({
                    url: url,
                    method: 'post',
                  evalScripts: true,
                    data: {
                        'query': query,
                        'type': type,
                        'page': page,
                        'format': 'json'
                    },
                    onSuccess: function (data) {

                        if (data.html.trim() != '') {

                            if (!append) {
                                var found = data.html;

                                found = Elements.from(found);
                                $('as_found_items').set('html', data.html);
                                var myFx = new Fx.Tween('as_found_items');
                                $('as_found_items').setStyle('opacity', 0);
                                myFx.start('opacity', 0, 1);
                            } else {
                                var found = data.html;
                                found = Elements.from(found);
                                found.inject($('as_found_items'), 'bottom');
                            }
                           if(found.length>5){
                             $('more_btn').setStyle('display', 'block');
                           }else{
                             $('more_btn').setStyle('display', 'none');
                           }
                        } else if (!append) {
                            var div = new Element('div');
                            div.addClass('tip');
                            var el = new Element('span').set('text', '<?php echo $this->translate("AS_Nothing found")?>');
                            el.inject(div);
                            $('as_found_items').set('html', '');
                            div.inject($('as_found_items'));
                            $('more_btn').setStyle('display', 'none');
                        } else if (data.html.trim() == '') {
                              check = true;
                            $('more_btn').setStyle('display', 'none');
                        }
                        $('as_loading').setStyle('display', 'none');
                      like.init_like_buttons();
                      list_like.init_list_like_buttons();
                      Smoothbox.bind();
                    }
                }).send();

            } else {
                $('as_loading').setStyle('display', 'none');
            }
        }
    });

</script>
<?php
if(Engine_Api::_()->hasModuleBootstrap('like')){
if($this->url(array('action' => 'show-content'), 'like_default')){
?>
<script type="text/javascript"  data-cfasync="false">
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
    }

    function hideLike(id) {
        var $like_box = $('page_status_' + id);
        if (window.likeboxes == undefined) {
            window.likeboxes = [];
        }
        window.likeboxes[id] = window.setTimeout(function(){
        }, 40);
    }

    function listShowLike(id) {
        var $like_box = $$('.list_page_status_' + id);

        if (window.likeboxes && window.likeboxes[id]) { window.clearTimeout(window.likeboxes[id]); }
    }

    function listHideLike(id) {
        var $like_box = $$('.list_page_status_' + id);

        if (window.likeboxes == undefined) {
            window.likeboxes = [];
        }
        window.likeboxes[id] = window.setTimeout(function(){
        }, 40);
    }
</script>
<?php }}?>