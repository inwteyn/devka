<?php

/**
 * Example
 *
<div id="tablet-component-feed">
<div class="component-feed ui-bar-a">

</div>
</div>
 */

?>

<div id="deprecated-component-tabs">
    <div data-role="" data-grid="d" class="component-tabs profile_tabs_wrapper">
        <div class="tab-1">
            <div id="static-fields">
                <div data-role="" class="field" data-theme="c" data-collapsed="false">
                    <h3></h3>
                    <p>
                    <table>
                        <tbody>
                        <tr>
                            <th scope="row"></th>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                    </p>
                </div>
            </div>
        </div>
        <div class="tab-2">
            <ul class="tablet-profile_tabs" data-role="listview">
                <li style="" class="li_tab"><a href="#" class="tab">
                    <span class="tab_title">Tab Title</span>
                    <span class="ui-li-count"></span>
                </a></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>

<div id="tablet-component-timelineCover">
    <div data-role="" class="component-timelineCover" style="position: relative !important;">
        <div class="subject_photo_cover" style="border: none !important; background: none !important;">
            <div class="cover_photo_wrapper">
                <a class="cover_photo_href" href="javascript:void(0);" style="position: relative; max-height: 315px; display: block; overflow: hidden;"></a>
            </div>
            <div class="cover_actions_wrapper" style="display: none;">
                <div class="cover_actions_list">
                  <div class="some_wrapper">
                      <div data-role="controlgroup" data-type="horizontal" data-mini="false" data-inline="true" data-theme="c">
                          <a id="cover_choose" href="javascript:void(0);" data-role="button" data-iconpos="notext" data-rel="dialog" data-icon="picture"></a>
                          <a id="cover_upload" href="javascript:void(0);" data-role="button" data-iconpos="notext" data-icon="upload-alt"></a>
                          <a id="cover_remove" href="javascript:void(0);" data-role="button" data-iconpos="notext" data-rel="dialog" data-icon="remove"></a>
                      </div>
                    </div>
                    <div data-role="controlgroup" data-type="horizontal" data-mini="false" data-inline="true" data-theme="c" style="display:inline-block;">
                        <div class="cover_actions" id="cover_actions">
                            <a href="javascript:void(0);" data-role="button" data-icon="list-alt" data-iconpos="notext"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ui-btn-up-c subject_photo">
            <a>
                <img src="application/modules/Apptouch/externals/images/icons/lists/nophoto.gif"/>
            </a>
        </div>
        <h3 class="subject_title">Subject Title</h3>
    </div>
</div>



<div id="tablet-component-feed">
  <div class="component-feed  ui-bar-a">
    <a href="javascript:void(0)" class="composeLink ui-icon-edit"
       style="display: none;"></a>

    <div class="social-feed">
      <div class="active_hashtags" style="display: none;"></div>
      <div class="feed-new-updates" style="display: none;"
           data-url="<?php echo $this->url(array('module' => 'apptouch', 'controller' => 'component', 'action' => 'index', 'component' => 'feed'), 'default', true);?>">
        <a class="btn"></a>
      </div>

      <ul class="wall feedList"></ul>

      <div class="viewMore" data-icon="arrow-d"><a class="show-page-loading-msg"
                                                   data-msgtext="<?php echo $this->translate('Loading...'); ?>"><?php echo $this->translate('View More');?></a>
      </div>
      <div class="feed-filter" style="display: none;">
        <select name="" class="feed-filter-select" data-iconpos="bottom" data-icon="caret-down">
          <option value="" <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)){ ?> data-mode="recent" <?php } ?> ><?php echo $this->translate('WALL_RECENT');?></option>
        </select>
      </div>

    </div>
  </div>
</div>
