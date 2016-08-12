<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>


<script type="text/javascript">
  en4.core.runonce.add(function (){

    var submit = function (e){

      if (e){
        e.stop();
      }
      var elms = $$('.layout_core_container_tabs .layout_hebadge_badges, .layout_core_container_tabs .layout_hebadge_badges_friend, .layout_core_container_tabs .layout_hebadge_badges_recent');

      elms.each(function (item){

        if (!item.get('id')){
          return ;
        }
        var content_id = item.get('id').substr(11);
        if (!content_id){
          return ;
        }
        if (item.hasClass('hebadge_loading')){
          return ;
        }
        item.addClass('hebadge_loading');
        if( $('bage_loading')) $('bage_loading').setStyle('visibility','visible');
        Hebadge.requestHTML( en4.core.baseUrl + 'core/widget/index/content_id/' + content_id + '/container/0/format/html', function (){
          item.removeClass('hebadge_loading');
          if( $('bage_loading')) $('bage_loading').setStyle('visibility','hidden');
        }, item , $$('.hebadge_form_search form')[0].toQueryString() );
      });
    }

    $$('.hebadge_form_search form').addEvent('submit', function (e){ submit(e); });
    $$('.hebadge_form_search a').addEvent('click', function (e){ submit(e); });
  });
</script>

<div class="hebadge_form_search">
  <form action="">
    <input type="text" name="text" value="" />
    <a href="javascript:void(0)"><span class="he-glyphicon he-glyphicon-search"></span></a>
  </form>
    <div style="position: absolute">
        <img style="left: 145px; position: relative; top: -24px; visibility: hidden; z-index: 101;" src="/application/modules/Core/externals/images/loading.gif" id="bage_loading">
    </div>
</div>
