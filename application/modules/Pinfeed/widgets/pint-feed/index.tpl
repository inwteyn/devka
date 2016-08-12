<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     bolot
 */

?>
<script>
  function getSmile(links) {
    if ($('wall_comment_smile')) {
      var elem = document.getElementById("wall_comment_smile");
      elem.parentNode.removeChild(elem);
    }
    $$('body')[0].addEvent('click', function (e) {
      if (!e.target.getParent('#wall_comment_smile') && !e.target.getParent('#smile_composer_comment-element')) {
        hideSmile();
      }
    });
    var smiles = <?php echo $this->wallSmiles()->getJson()?>;
    var link = links;
    var container = new Element('div', {
      'class': 'wall-smile-container',
      'id': 'wall_comment_smile',
      'html': '<div class="wall_data_comment"></div>'
    });
    container = injectAbsoluteCommentSmile(link, container, true);

    var arrow = new Element('div', {'class': 'wall_arrow_container', 'html': '<div class="wall_arrow"></div>'});
    arrow.inject(container, 'top');

    var ul = new Element('ul');

    for (var i = 0; i < smiles.length; i++) {
      var item = smiles[i];
      var a = new Element('a', {
        'title': item.title,
        'href': 'javascript:void(0)',
        'html': item.html,
        'rev': item.index_tag
      });
      var li = new Element('li', {});
      a.inject(li);
      li.inject(ul);

      a.addEvent('click', function () {
        var body_in = link.getParent('form').getChildren('#body')[0].value;
        link.getParent('form').getChildren('#body')[0].value = body_in + ' ' + $(this).get('rev') + ' ';
        link.getParent('form').getChildren('#submit')[0].setStyle('display', 'block');
        hideSmile();
      });
    }

    ul.inject(container.getElement('.wall_data_comment'));
  }
  ;


  function hideSmile() {
    if ($('wall_comment_smile')) {
      var elem = document.getElementById("wall_comment_smile");
      elem.parentNode.removeChild(elem);

    }
  }
  function injectAbsoluteCommentSmile(element, container) {
    element = $(element);
    container = $(container);

    if ($type(element) != 'element' || $type(container) != 'element') {
      return;
    }

    var build = function () {
      var pos = element.getCoordinates();

      container
        .setStyle('position', 'absolute')
        .setStyle('top', pos.top + pos.height)
        .setStyle('right', ($$('body')[0].getCoordinates().width - pos.left - pos.width) - 15);

    };

    container.inject(Wall.externalDiv(), 'bottom');
    build();

    return container;

  }
  ;
  function select_file_comment(id) {
    if (id && id.toInt() > 0) {

      var file_button = $('photo_comment_' + id);
      if (file_button) {
        file_button.click();
      }
    }
  }
  function deleteImage(id) {
    var photo_id = 0;
    var container = $('comment_attach_preview_image_wall' + id);
    if (container) {
      photo_id = container.getChildren('a').get('href')[0].split('/').pop();
    }
    if (!photo_id) {
      return;
    }
    if (window.load_image_deletes_comment == 1) {
      return;
    }
    var loading = $('comment_attach_loading_wall' + id);
    loading.setStyle('display', 'block');
    container.setStyle('display', 'none');
    window.load_image_deletes_comment = 1;
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'wall/index/album',
      data: {
        'do': '1',
        'photos_id_del': photo_id
      },
      onComplete: function (response) {
        container.set('html', '');
        loading.setStyle('display', 'none');
        $('select_photo_' + id).setStyle('display', 'block');
        window.load_image_deletes_comment = 0;
      }
    }).send();
  }
</script>
<style type="text/css">
  <?php if($this->subject):?>
  .pins_xount_2 {
    width: 65%;
  }

  div#pinfeed1 {
    width: 50% !important;
    float: left;
  }

  div#pinfeed2 {
    width: 50% !important;
    float: left;
  }

  div#pinfeed3 {
    width: 100% !important;
    float: left;
  }

  div#pinfeed3 {
    width: 32.5% !important;
    float: left;
  }

  div#pinfeed1 .wall-items-pinfeed {
    width: 95% !important;
  }

  div#pinfeed2 .wall-items-pinfeed {
    width: 95% !important;
  }

  div#pinfeed3 .wall-items-pinfeed {
    width: 95% !important;
  }

  <?php endif;?>

  .wallFeed,
  .wall-stream {
    margin: 0 !important;
  }

  html #activity-feed {
    margin-top: 2px !important;
  }

  .layout_left .generic_layout_container {
    border: 1px solid #BBBBBB \0/;
    box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.3);
    display: block;
    float: right;
    margin-top: 13px;
    padding: 20px 9px 9px;
    width: 200px;
  }

  h3 {
    background: none;
    border-radius: 3px 3px 3px 3px;
    font-size: 1.2em;
    padding: 0.4em 0.7em;
  }

  .video_info {
    clear: both;
  }

  .feed_item_attachments .video_thumb_wrapper {
    max-width: 202px;

  }

  .thumb_video_activity {
    max-width: 190px;
    width: 189px;
  }

  <?php

  if($this->width==1 && !$this->subject){
  ?>
  .layout_active_theme_modern .layout_left {
    margin-right: 15px !important;
  }

  #global_content {
    width: 100% !important;
    box-sizing: border-box;
  }

  .layout_left {
    margin-left: 80px;

  }

  .layout_middle {
    margin-right: 80px;

  }

  .layout_active_theme_modern .wallFeed {
    margin: -10px 0 0 !important;
  }

  .layout_left .generic_layout_container {
    margin-top: 0px !important;
    margin-bottom: 15px;

  }

  <?php
  }else{
  ?>
  #activity-feed {
    margin-top: 25px;
  }

  <?php
  }
  ?>
  .layout_left .generic_layout_container {

  }
</style>
<script type="text/javascript">
  <?php if ($this->width == 1){
?>
  width_res = 1;
  <?php
  }else{

  ?>
  width_res = 0;
  <?php
  }

  ?>

</script>

<?php
$pin = 1;
if (!empty($this->feedOnly) && empty($this->checkUpdate)): // ajax
  ?>

  <?php echo $this->wallActivityLoop($this->activity, array(
  'action_id' => $this->action_id,
  'viewAllComments' => $this->viewAllComments,
  'viewAllLikes' => $this->viewAllLikes,
  'comment_pagination' => $this->comment_pagination,
  'module' => 'pinfeed',
));?>


  <?php if ((!empty($this->feed_config['next_page']) || $this->nextid) && !$this->endOfFeed): ?>

  <li class="utility-viewall">
    <div class="pagination">
      <a href="javascript:void(0);"
         rev="<?php if (!empty($this->feed_config['next_page'])) { ?>page_<?php echo $this->feed_config['next_page'] ?><?php } else { ?>next_<?php echo $this->nextid ?><?php } ?>"><?php echo $this->translate('View More'); ?></a>
    </div>
    <div class="loader" style="display: none;">
      <div class="wall_icon"></div>
      <div class="text">
        <?php echo $this->translate('Loading ...') ?>
      </div>
    </div>
  </li>

<?php endif;?>

  <li class="utility-feed-config wall_displaynone"
      onclick='return(<?php echo Zend_Json::encode($this->feed_config)?>)'></li>

  <?php if ($this->firstid): ?>
  <li class="utility-setlast wall_displaynone" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
<?php endif;?>


  <script type="text/javascript">


    // Prepare layout options.
    if ($('pinfeed')) {
      var options = {
        autoResize: true, // This will auto-update the layout when the browser window is resized.
        container: $('pinfeed'),
        item: $$('.wall-action-item'),
        offset: 2,
        itemWidth: 255,
        bottom: 1
      };
      var handler = $$('.wall-action-item');
      pinfeed(options);
    }

  </script>
  <?php return; ?>
<?php endif; ?>



<?php if (!empty($this->checkUpdate)): ?>

  <?php if ($this->activityCount): ?>

    <li class="utility-getlast">

      <script type='text/javascript'>
        Wall.activityCount(<?php echo $this->activityCount?>);
      </script>

      <div class='tip'>
        <span>
          <a href='javascript:void(0);' class="link">
            <?php echo $this->translate(array(
              '%d new update is available - click this to show it.',
              '%d new updates are available - click this to show them.',
              $this->activityCount),
              $this->activityCount) ?>
          </a>
        </span>
      </div>

      <?php return; ?>

    </li>

  <?php endif; ?>
  <?php return; ?>

<?php endif;

?>



<?php
try {
  echo $this->render('_header.tpl');
} catch (Exception $e) {
  print_die($e . '');
}

?>

<div id="wall-feed-scripts">
  <script type="text/javascript">


    Wall.runonce.add(function () {

      var feed = new Wall.Feed({
        feed_uid: '<?php echo $this->feed_uid?>',
        enableComposer: <?php echo ($this->enableComposer) ? 1 : 0?>,
        url_wall: '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'wall.feed'), 'default', true) ?>',
        last_id: <?php echo sprintf('%d', $this->firstid) ?>,
        subject_guid: '<?php echo $this->subjectGuid ?>',
        fbpage_id: <?php echo ($this->fbpage_id) ? "'{$this->fbpage_id}'" : 0;?>
      });

      feed.params = <?php echo $this->jsonInline($this->list_params);?>;

      <?php if (empty($this->action_id)):?>

      <?php if ($this->updateSettings):?>

      feed.watcher = new Wall.UpdateHandler({
        baseUrl: en4.core.baseUrl,
        basePath: en4.core.basePath,
        identity: 4,
        delay: <?php echo $this->updateSettings;?>,
        last_id: <?php echo sprintf('%d', $this->firstid) ?>,
        subject_guid: '<?php echo $this->subjectGuid ?>',
        feed_uid: '<?php echo $this->feed_uid?>'
      });
      try {
        setTimeout(function () {
          feed.watcher.start();
        }, 1250);
      } catch (e) {
      }

      <?php endif;?>

      <?php else:?>

      var tab_link = $$('.tab_layout_wall_feed')[0];
      if (tab_link && tabContainerSwitch) {
        tabContainerSwitch(tab_link, 'generic_layout_container layout_wall_feed');
      }

      <?php endif;?>

    });

  </script>
</div>


<div class="wallFeed feed row-fluid" id="<?php echo $this->feed_uid ?>">


  <?php

  $tabs = Engine_Api::_()->wall()->getManifestType('wall_tabs');


  // show only feed

  if ($this->subject || !$this->viewer()->getIdentity()) {

    $tab_disabled = array_diff(array_keys($tabs), array('social'));
    $tab_default = 'social';

    // show tabs

  } else {

    $tab_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.disabled'));
    $tab_default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.default', 'social');

  }

  ?>




  <div class="wall-streams">

    <?php if ($this->enableComposer): ?>
    <?php if (!in_array('social', $tab_disabled)): ?>
    <div class="wall-stream wall-stream-social <?php if ($tab_default == 'social'): ?>is_active<?php endif; ?>">
      <ul class="wall-feed feed" id="activity-feed">
        <div class="pinfeed-main">
          <div id="header_hashtag" style="display: none"></div>
          <div class="pinfeeds">

            <div style="float: left;" class="pins_xount_2">
              <!--     wall types-->
              <?php if (!$this->subject): ?>
                <li
                  class="wall-stream-option wall-stream-option-social <?php if ($tab_default == 'social'): ?>is_active<?php endif; ?>">

                  <div class="wall-lists">

                    <?php echo $this->partial('_activeList.tpl', 'pinfeed', array(
                      'list_params' => $this->list_params,
                      'types' => $this->types,
                      'lists' => $this->lists,
                      'friendlists' => $this->friendlists
                    )) ?>
                    <ul class="wall-types">
                      <?php echo $this->partial('_list.tpl', 'pinfeed', array(
                        'list_params' => $this->list_params,
                        'types' => $this->types,
                        'lists' => $this->lists,
                        'friendlists' => $this->friendlists
                      )) ?>
                    </ul>
                  </div>

                </li>
              <?php endif; ?>
              <div class="wallComposer wall-social-composer pinfeed_comments">

                <div class="wallFormComposer">
                  <form class="activity" method="post" action="<?php echo $this->url() ?>">

                    <div class="wallComposerContainer">
                      <div class="wallTextareaContainer">
                        <div class="inputBox">
                          <div class="labelBox is_active">
                            <span><?php echo $this->translate("WALL_What's on your mind?"); ?></span>
                          </div>
                          <div class="textareaBox">
                            <div class="close"></div>
                            <textarea rows="1" cols="1" name="body"></textarea>
                            <input type="hidden" name="return_url" value="<?php echo $this->url() ?>"/>
                            <?php if ($this->viewer() && $this->subject && !$this->viewer()->isSelf($this->subject)): ?>
                              <input type="hidden" name="subject" value="<?php echo $this->subject->getGuid() ?>"/>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="toolsBox"></div>

                      </div>
                    </div>

                    <div class="wall-compose-tray"></div>

                    <div class="submitMenu">
                      <button type="submit" class="wall_composer_button">
                        &nbsp;&nbsp;&nbsp;<?php echo $this->translate("WALL_Share") ?>&nbsp;&nbsp;&nbsp;</button>

                      <?php if ($this->allowPrivacy && count($this->privacy) > 1): ?>

                        <div class="wall-privacy-container">
                          <a href="javascript:void(0);" class="wall-privacy-link wall_tips wall_blurlink"
                             title="<?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_' . strtoupper($this->privacy_active)); ?>">
                            <span class="wall_privacy hei hei-lock">&nbsp;</span>
                            <span class="wall_expand">&nbsp;</span>
                          </a>
                          <ul class="wall-privacy">
                            <?php foreach ($this->privacy as $item): ?>
                              <li>
                                <a href="javascript:void(0);"
                                   class="item wall_blurlink <?php if ($item == $this->privacy_active): ?>is_active<?php endif; ?>"
                                   rev="<?php echo $item ?>">
                                  <span class="wall_icon_active">&nbsp;</span>
                                  <span
                                    class="wall_text"><?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_' . strtoupper($item)); ?></span>
                                </a>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                          <input type="hidden" name="privacy" value="<?php echo $this->privacy_active; ?>"
                                 class="wall_privacy_input"/>
                        </div>

                      <?php endif; ?>

                      <ul class="wallShareMenu">
                        <?php

                        if ($this->viewer()->getIdentity()) {

                          foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service) {
                            $class = Engine_Api::_()->wall()->getServiceClass($service);
                            if (!$class || !$class->isActiveShare()) {
                              continue;
                            }
                            $a_class = 'wall-share-' . $service . ' wall_tips disabled';

                            echo '<li class="service">
                        <a href="javascript:void(0);" class="' . $a_class . '" rev="' . $service . '" title="' . $this->translate('WALL_SHARE_' . strtoupper($service) . '') . '" style="text-decoration: none;">
                        <i class="hei hei-' . $service . '"></i>
                        </a>
                        <input type="hidden" name="share[' . $service . ']" class="share_input" value="0"/>
                      </li>';

                          }
                        }
                        ?>
                      </ul>

                    </div>


                    <?php foreach ($this->composePartials as $partial): ?>
                      <?php echo $this->partial($partial[0], $partial[1], array(
                        'feed_uid' => $this->feed_uid
                      )) ?>
                    <?php endforeach; ?>

                  </form>
                </div>

              </div>

              <?php endif; ?>


              <div class="pinfeed" id="pinfeed1" style="width: 280px; float: left">
              </div>
              <div class="pinfeed" id="pinfeed2" style="width: 280px; float: left">
              </div>
            </div>


            <!--     wall typs ends -->


            <div class="pinfeed" id="pinfeed3" style="width: 280px; float: left;margin-top: 0; "></div>
            <div class="pinfeed" id="pinfeed4" style="width: 280px; float: left;margin-top: 0; "></div>
            <div class="pinfeed" id="pinfeed5" style="width: 280px; float: left;margin-top: 0; "></div>
            <div class="pinfeed" id="pinfeed6" style="width: 280px; float: left;margin-top: 0; "></div>
            <div class="pinfeed" id="pinfeed7" style="width: 280px; float: left;margin-top: 0; "></div>
            <div style="clear: both;"></div>
            <div id='feed_block'></div>


            <?php if ($this->activity): ?>
              <?php echo $this->wallActivityLoop($this->activity, array(
                'action_id' => $this->action_id,
                'viewAllComments' => $this->viewAllComments,
                'viewAllLikes' => $this->viewAllLikes,
                'comment_pagination' => $this->comment_pagination,
                'module' => 'pinfeed',
              )) ?>
            <?php endif; ?>

            <?php if ((!empty($this->feed_config['next_page']) || $this->nextid) && !$this->endOfFeed): ?>

              <li class="utility-viewall">
                <div class="pagination">
                  <a href="javascript:void(0);"
                     rev="<?php if (!empty($this->feed_config['next_page'])) { ?>page_<?php echo $this->feed_config['next_page'] ?><?php } else { ?>next_<?php echo $this->nextid ?><?php } ?>"><?php echo $this->translate('View More'); ?></a>
                </div>
                <div class="loader" style="display: none;">
                  <div class="wall_icon">&nbsp;</div>
                  <div class="text">
                    <?php echo $this->translate('Loading ...') ?>
                  </div>
                </div>
              </li>

            <?php endif; ?>

            <?php if (!$this->activity): ?>
              <li class="wall-empty-feed">
                <div class="tip">
                <span>
                  <?php echo $this->translate("WALL_EMPTY_FEED") ?>
                </span>
                </div>
              </li>
            <?php endif; ?>

            <li class="utility-feed-config wall_displaynone"
                onclick='return(<?php echo Zend_Json::encode($this->feed_config) ?>)'></li>

            <?php if ($this->firstid): ?>
              <li class="utility-setlast" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
            <?php endif; ?>

      </ul>
    </div>
  </div>
<?php endif; ?>



  <?php
  /*if ($this->viewer()->getIdentity()){

    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){

      if (in_array($service, $tab_disabled)){
        continue ;
      }

      $class = Engine_Api::_()->wall()->getServiceClass($service);

      if (!$class || !$class->isActiveStream()){
        continue ;
      }

      $tpl = $class->getFeedTpl();

      echo '<div class="wall-stream wall-stream-'.$service.'">
            <ul>
              <li class="wall-stream-tab-login wall-stream-tab">
                <div class="tip"><span>
                '.$this->translate('WALL_STREAM_'.strtoupper($service).'_LOGIN', array('<a href="javascript:void(0);" class="stream_login_link">', '</a>')).'
                </span></div>
              </li>
              <li class="wall-stream-tab-loader wall-stream-tab"><div class="wall-loader"></div></li>
              <li class="wall-stream-tab-stream wall-stream-tab">';

      echo $this->partial(@$tpl['path'], @$tpl['module'], array('feed_uid' => $this->feed_uid));

      echo "</li></ul></div>";

    }

  }*/

  ?>

</div>

</div>
</div>
<div class="video_background" style="display: none;"></div>
<div id="videoViewer"></div>
<script type="text/javascript">

  if ($('activity-feed')) {
    column_count = Math.floor($('activity-feed').getComputedSize().width / 275);
  }
  else {
    column_count = 3;
  }
  if (column_count < 3) {
    column_count = Math.floor($('activity-feed').getComputedSize().width / 275);
    if (column_count < 3) {
      column_count = 3;
    }
  }
  pinfeed_page = 1;
  start = 0;
  array = [];
  for (var i = 0; i < column_count; array[i++] = 0);
  //options.container.setStyle('width', column_count * 1);

  var options = {
    autoResize: true, // This will auto-update the layout when the browser window is resized.
    container: $('pinfeed'),
    item: $$('.wall-items-pinfeed'),
    offset: 2,
    itemWidth: 255,
    bottom: 0
  };
  var handler = $$('.wall-action-item');

  pinfeed(options);


  //}
</script>
