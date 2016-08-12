
<?php
if( !empty($this->feedOnly) && empty($this->checkUpdate)): // ajax?>

  <?php echo $this->touchWallActivityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'comment_pagination' => $this->comment_pagination
  ));?>


  <?php if ($this->nextid && !$this->endOfFeed):?>

    <li class="utility-viewall">
      <div class="pagination">
        <a href="javascript:void(0);" rev="next_<?php echo $this->nextid?>"><?php echo $this->translate('View More')?></a>
      </div>
      <div class="loader" style="display: none;">
        <div class="icon"></div>
        <div class="text">
          <?php echo $this->translate('Loading ...')?>
        </div>
      </div>
    </li>

  <?php endif;?>

  <li class="utility-setlast" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>

  <?php return; ?>

<?php endif; ?>



<?php if (!empty($this->checkUpdate)):?>

  <?php if ($this->activityCount):?>

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
              $this->activityCount)?>
          </a>
        </span>
      </div>

      <?php return; ?>

    </li>

  <?php endif; ?>
  <?php return ;?>

<?php endif;?>



<?php
  echo $this->render('_twheader.tpl', 'touch');
?>


<script type="text/javascript">
  Wall.runonce.add(function (){

    var feed = new Wall.Feed({
      feed_uid: '<?php echo $this->feed_uid?>',
      enableComposer: <?php echo ($this->enableComposer) ? 1 : 0?>,
      url_wall: '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'touch.wall'), 'default', true) ?>',
      last_id: <?php echo sprintf('%d', $this->firstid) ?>,
      subject_guid : '<?php echo $this->subjectGuid ?>'
    });

    window.wall_object = feed;

      feed.watcher = new Wall.UpdateHandler({
      baseUrl : en4.core.baseUrl,
      basePath : en4.core.basePath,
      identity : 4,
      delay : <?php echo $this->updateSettings;?>,
      last_id: <?php echo sprintf('%d', $this->firstid) ?>,
      subject_guid : '<?php echo $this->subjectGuid ?>',
      feed_uid: '<?php echo $this->feed_uid?>'
    });
      try {
        setTimeout(function (){
          feed.watcher.start();
        },1250);
      } catch( e ) {}

  });

</script>

<div class="wallFeed" id="<?php echo $this->feed_uid?>">

  <?php if ($this->viewer()->getIdentity() && !$this->subject()):?>

    <div class="wall-stream-header">
      <ul class="wall-stream-types">
        <li>

          <a href="javascript:void(0);" class="is_active wall-stream-type wall-stream-type-social wall_blurlink" rev="social">
            <span class="icon"></span>
          </a>

      <?php

      foreach ($this->services as $service){
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class){
          continue ;
        }
        if (!$class->isActiveStream()){
          continue ;
        }
        echo '<li>';
        echo '<a href="javascript:void(0);" class="wall-stream-type wall-stream-type-'.$service.' wall_blurlink" rev="'.$service.'"><span class="icon"></span></a>';
        echo '</li>';
      }
        ?>

        </li>
      </ul>


      <ul class="wall-stream-options">

        <li class="wall-stream-option wall-stream-option-social is_active">

          <div class="wall-lists">
            <a href="javascript:void(0);" class="wall-list-button wall-button wall_blurlink"><span class="icon"></span><?php echo $this->translate('WALL_LIST');?></a>
            <ul class="wall-types">
              <?php echo $this->partial('_list.tpl', 'wall', array(
                'list_params' => $this->list_params,
                'types' => $this->types,
                'lists' => $this->lists
              ))?>
           </ul>
         </div>

        </li>

        <?php
          // or js inject
        ?>

        <li class="wall-stream-option wall-stream-option-facebook">

          <a href="javascript:void(0);" class="wall-button wall_blurlink">
            <span class="icon wall-refresh"></span>
            <?php echo $this->translate('WALL_REFRESH')?>
          </a>

        </li>

        <li class="wall-stream-option wall-stream-option-twitter">

          <a href="javascript:void(0);" class="wall-button wall_blurlink">
            <span class="icon wall-refresh"></span>
            <?php echo $this->translate('WALL_REFRESH')?>
          </a>

        </li>


      </ul>
    </div>

  <?php endif ;?>

  <ul class="wall-streams">
    <li class="wall-stream wall-stream-social is_active">


    <?php if ($this->enableComposer):?>

      <div class="wallComposer wall-social-composer">

        <form method="post" action="<?php echo $this->url()?>">

          <div class="wallTextareaContainer">
            <div class="inputBox">
              <div class="labelBox is_active">
                <span><?php echo $this->translate('WALL_Post Something...');?></span>
              </div>
              <div class="textareaBox">
                <div class="close"></div>
                <textarea rows="1" cols="1" name="body"></textarea>
                <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
                <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
                  <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
                <?php endif; ?>
              </div>
            </div>
            <div class="toolsBox"></div>

          </div>

          <div class="wall-compose-tray"></div>

          <div class="submitMenu touch_tab_group">
            <button type="submit" class="wall_share_button"><?php echo $this->translate("WALL_Share") ?></button>
            <ul class="wallShareMenu">
              <?php
                if ($this->viewer()->getIdentity()){

                  $setting = Engine_Api::_()->wall()->getUserSetting($this->viewer());

                  foreach ($this->services as $service){
                    $class = Engine_Api::_()->wall()->getServiceClass($service);
                    if (!$class) {
                      continue;
                    }
                    if (!$class->isActiveShare()){
                      continue ;
                    }
                    $tokenRow = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($this->viewer(), $service);

                    $enabled = false;
                    $setting_key = 'share_' . $service . '_enabled';
                    if (($tokenRow && $setting->$setting_key) && $tokenRow->check()){
                      $enabled = true;
                    }

                    $title = '';
                    $linkClass  = 'wall-share-'.$service.' wall_tips';
                    $disabled = '';
                    if ($enabled){
                      $title = $this->translate('WALL_SHARE_' . strtoupper($service) . '_ACTIVE', array($tokenRow->object_name));
                    } else {
                      $title = $this->translate('WALL_SHARE_' . strtoupper($service) . '');
                      $linkClass .= ' disabled';
                      $disabled = '_disabled';
                    }
                    echo '<li class="touch_tab service'.$disabled.'">
                      <a href="javascript:void(0);" class="'.$linkClass.'" rev="'.$service.'" ></a>
                      <input type="hidden" name="share['.$service.']" class="share_input" value="'.(($enabled) ? 1 : 0).'"/>
                    </li>';
                  }
                }
            ?>
            </ul>
            <?php if ($this->allowPrivacy):?>
              <div class="wall-privacy-container">
                <a href="javascript:void(0);" class="wall-privacy-link touch_tab touch_tab_dark wall_tips wall_blurlink" title="<?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_'  . strtoupper($this->privacy_active));?>">
                  <span class="wall_privacy">&nbsp;</span>
                </a>
                <ul class="wall-privacy">
                  <?php foreach ($this->privacy as $item):?>
                    <li>
                      <a href="javascript:void(0);" class="item wall_blurlink <?php if ($item == $this->privacy_active):?>is_active<?php endif;?>" rev="<?php echo $item?>">
                        <span class="wall_icon_active">&nbsp;</span>
                        <span class="wall_text"><?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_'  . strtoupper($item));?></span>
                      </a>
                    </li>
                  <?php endforeach ;?>
                </ul>
                <input type="hidden" name="privacy" value="<?php echo $this->privacy_active;?>" class="wall_privacy_input" />
              </div>
            <?php endif;?>
          </div>

           <?php foreach( $this->composePartials as $partial ): ?>
           <?php
            $params = array('feed_uid' => $this->feed_uid);
            if($this->photo_id != 0 && isset($this->composePartials['photo']))
              $params['photo_id'] = $this->photo_id;
              $params['photo_src'] = $this->photo_src;
              if($this->text != '')
                $params['photo_text'] = $this->text;

              if($this->callback_url != '')
                $params['callback_url'] = $this->callback_url;

            ?>
            <?php echo $this->partial($partial[0], $partial[1], $params) ?>

           <?php endforeach; ?>
        </form>

      </div>

    <?php endif;?>


      <ul class="wall-feed feed" id="activity-feed">
        <?php if( $this->activity ): ?>
          <?php echo $this->touchWallActivityLoop($this->activity, array(
            'action_id' => $this->action_id,
            'viewAllComments' => $this->viewAllComments,
            'viewAllLikes' => $this->viewAllLikes,
            'comment_pagination' => $this->comment_pagination
          ))?>
        <?php endif; ?>

        <?php if ($this->nextid && !$this->endOfFeed):?>

          <li class="utility-viewall">
            <div class="pagination">
              <a href="javascript:void(0);" rev="next_<?php echo $this->nextid?>"><?php echo $this->translate('View More')?></a>
            </div>
            <div class="loader" style="display: none;">
              <div class="icon"></div>
              <div class="text">
                <?php echo $this->translate('Loading ...')?>
              </div>
            </div>
          </li>

        <?php endif;?>

        <?php if( !$this->activity ): ?>
          <li class="wall-empty-feed">
              <div class="tip">
                <span>
                  <?php echo $this->translate("WALL_EMPTY_FEED") ?>
                </span>
              </div>
          </li>
        <?php endif; ?>


      </ul>

    </li>

    <li class="utility-setlast" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>

    <?php

    if ($this->viewer()->getIdentity() && !$this->subject()){

      foreach ($this->services as $service){
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class){
          continue ;
        }
        $path = array(
          'module' => 'wall',
          'controller' => strtolower(array_pop(explode("_", get_class($class)))),
          'action' => 'stream',
        );

        echo '<li class="wall-stream wall-stream-'.$service.'">';
        echo $this->action($path['action'], $path['controller'], $path['module'], array('feed_uid' => $this->feed_uid));
        echo '</li>';
      }

    }
      ?>

  </ul>

</div>
