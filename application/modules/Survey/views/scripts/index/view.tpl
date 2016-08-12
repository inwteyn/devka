<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<?php

  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js')
    ->appendFile($this->baseUrl().'/externals/swfobject/swfobject.js');
?>

<script type="text/javascript">
en4.core.runonce.add(function(){

    <?php if (isset($this->firstMatches) || isset($this->secondMatches)) : ?>
      survey.firstMatchCount = <?php echo $this->firstMatchCount; ?>;
      survey.secondMatchCount = <?php echo $this->secondMatchCount; ?>;
      survey.firstMatches = <?php echo $this->jsonInline($this->firstMatches); ?>;
      survey.secondMatches = <?php echo $this->jsonInline($this->secondMatches); ?>
    <?php endif; ?>

    survey.view_survey(<?php echo $this->survey->getIdentity(); ?>);

    var bg_color = ($$('.survey_user_result')[0]) ? $$('.survey_user_result')[0].getStyle('background-color') : '#ffffff';
    if (bg_color == 'transparent') {
      bg_color = $$('body')[0].getStyle('background-color');
      bg_color = (bg_color == 'transparent' || '') ? '#ffffff' : bg_color;
    }
    if(bg_color == '#fff') {
      bg_color = '#ffffff';
    }
    var color = ($$('#content-results h3')[0]) ? $$('#content-results h3')[0].getStyle('color') : '#ffffff';
    var data_file = '<?php echo $this->chart_data_url?>'
      .replace('bg_color_value', bg_color.replace('#', ''))
      .replace('color_value', color.replace('#', ''));

    $('chart_div').empty();
    swfobject.embedSWF(
        "<?php echo $this->baseUrl() ?>/externals/open-flash-chart/open-flash-chart.swf",
        "chart_div",
        "580",
        "250",
        "9.0.0",
        "expressInstall.swf",
        {
            "data-file": data_file
        },
        {
            'wmode': 'opaque'
        }
    );
});
</script>

<div class="survey_theme_<?php echo $this->theme_name; ?>">
<div class="view_survey_page">

<div class="generic_layout_container layout_left">
  <div class="generic_layout_container survey_photo">
    <?php $link_options = array('title' => $this->translate('View fullsize'), 'onclick' => "he_show_image('" . $this->survey->getPhotoUrl() . "', $(this).getElement('img'))"); ?>
    <?php echo $this->htmlLink('javascript://', $this->itemPhoto($this->survey, 'thumb.profile'), $link_options); ?>
  </div>

  <?php if ($this->rateEnabled) : ?>
    <div class="generic_layout_container survey_rating">
      <?php echo $this->action("index", "index", "rate", array("type"=>"surveys", "id"=>$this->survey->getIdentity(), "can_rate" => $this->can_rate, "error_msg" => $this->error_msg)) ?>
    </div>
  <?php endif; ?>

  <div class="generic_layout_container survey_options">
    <div id="profile_options">
      <?php
        echo $this->navigation()
          ->menu()
          ->setContainer($this->survey_options)
          ->setPartial(array('_navIcons.tpl', 'core'))
          ->render()
      ?>
    </div>
  </div>
  
  <div class="generic_layout_container survey_info">
    <h3><?php echo $this->translate('Survey Info'); ?></h3>
    <ul>
      <li class="survey_title">
        <span><?php echo $this->survey->getTitle() ?></span>

        <?php if ($this->viewer()->getIdentity() == $this->survey->user_id) : ?>
          <?php if ($this->survey->published) : ?>
            <span style="color: green;"><?php echo $this->translate('survey_published'); ?></span>
          <?php else: ?>
            <span style="color: red;"><?php echo $this->translate('survey_not published'); ?></span>
          <?php endif; ?>
          <?php if ($this->survey->approved) : ?>
            <span style="color: green;"><?php echo $this->translate('survey_approved'); ?></span>
          <?php else : ?>
            <span style="color: red;"><?php echo $this->translate('survey_not approved'); ?></span>
          <?php endif; ?>
        <?php endif; ?>

        <?php if( !empty($this->survey->category_id) ): ?>
          <?php echo $this->htmlLink(array('route' => 'survey_browse', 'category' => $this->survey->category_id), $this->survey->getCategory()->category_name) ?>
        <?php endif; ?>
      </li>
      <?php if ('' !== ($description = $this->survey->description)): ?>
        <li class="survey_description">
          <?php echo $description; ?>
        </li>
      <?php endif; ?>
      <li class="survey_took_info">
        <ul>
          <li><?php echo $this->translate(array('survey_<b>%s</b> view', '<b>%s</b> views', $this->survey->view_count), $this->locale()->toNumber($this->survey->view_count)) ?></li>
          <li><?php echo $this->translate(array('survey_<b>%s</b> take', '<b>%s</b> takes', $this->survey->take_count), $this->locale()->toNumber($this->survey->take_count)) ?></li>
          <li><?php echo $this->translate(array('survey_<b>%s</b> comment', '<b>%s</b> comments', $this->survey->comment_count), $this->locale()->toNumber($this->survey->comment_count)) ?></li>
          <li><?php echo $this->translate('survey_Last updated <b>%s</b>', $this->timestamp($this->survey->modified_date)) ?></li>
        </ul>
      </li>
    </ul>
  </div>
</div>

<div class="layout_middle">
  <div id="profile_status">
    <h2><?php echo $this->survey->title; ?></h2>

    <?php if ($this->can_take) :?>
    <div class="take_survey_btn float_left_rtl">
    <?php if ($this->userResult) :?>
        <button type="button" onclick="window.location.href='<?php echo $this->take_url?>'"><?php echo $this->translate("Take this survey again") ?></button>
    <?php else :?>
        <button type="button" onclick="window.location.href='<?php echo $this->take_url?>'""><?php echo $this->translate("Take this survey") ?></button>
    <?php endif;?>
    </div>
    <?php endif;?>
  </div>

  <div id="tabs_parent" class="tabs_alt view_survey_tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->survey_tabs)
        ->setPartial(array('tabs/surveyJsTabs.tpl', 'survey'))
        ->render();
    ?>
  </div>

  <div id="content-results" class="survey_content<?php echo ($this->userResult) ? ' display_none' : ''; ?>">
  <?php if ($this->userResult) : ?>
    <h3><?php echo $this->translate("survey_Your Result:") ?></h3>
    <div class="survey_user_result">
        <h4><?php echo $this->userResult->getTitle()?></h4>
        <div class="user-result-detail">
          <?php if ($this->userResult->getPhotoUrl()) :?>
            <div class="float_left user_result_desc  float_right_rtl">
                <?php $link_options = array('title' => $this->translate("View fullsize"), 'onclick' => "he_show_image('" . $this->userResult->getPhotoUrl() . "', $(this).getElement('img'))"); ?>
                <?php echo $this->htmlLink('javascript://', $this->itemPhoto($this->userResult, 'thumb.normal'), $link_options) ?>
            </div>
            <?php endif; ?>
            <?php if ('' !== ($description = $this->userResult->description)): ?>
            <div class="float_left user_result_desc  float_right_rtl">
                <?php echo $description; ?>
            </div>
            <?php endif; ?>
            <div class="clr"></div>
        </div>
    </div>
  <br/>
  <?php endif; ?>

    <h3><?php echo $this->translate("survey_Results Chart:") ?></h3>
    <div class="survey_user_result survey_results_chart">
        <div id="chart_div"></div>    
    </div>

  </div>

  <div id="content-matches" class="survey_content <?php echo (!$this->userResult) ? ' display_none' : ''; ?>">
  <?php if ($this->userResult) : ?>
    <h3><?php echo $this->translate("survey_Latest people with the same result as yours:") ?></h3>
    <div class="user_matches">
      <div class="first_row">
        <div class="user_match top_left"></div>
        <div class="user_match top_near_left"></div>
        <div class="user_match"></div>
        <div class="user_match"></div>
        <div class="user_match top_near_right"></div>
        <div class="user_match top_right"></div>
        <div class="clr"></div>
      </div>
      <div class="second_row">
        <div class="side_column">
          <div class="user_match"></div>
          <div class="user_match"></div>
          <div class="user_match"></div>
          <div class="user_match"></div>
          <div class="clr"></div>
        </div>
        <div class="main_column">
          <div class="user_close_matches">
            <div class="main_first_row">
              <div class="user_match top_left"></div>
              <div class="user_match top_near_left"></div>
              <div class="user_match top_near_right"></div>
              <div class="user_match top_right"></div>
              <div class="clr"></div>
            </div>
            <div class="main_second_row">
              <div class="main_side_column">
                <div class="user_match"></div>
                <div class="user_match"></div>
                <div class="clr"></div>
              </div>
              <div class="main_main_column">
                <span id="user_match_nophoto_tpl" class="display_none">
                  <?php
                    $viewer_photo_id = $this->viewer()->photo_id;
                    $this->viewer()->photo_id = 0;
                    echo $this->itemPhoto($this->viewer(), 'thumb.icon');
                    $this->viewer()->photo_id = $viewer_photo_id;                    
                  ?>
                </span>
                <?php $viewer_photo = $this->viewer()->getPhotoUrl('thumb.normal'); ?>
                <div class="user_match_owner" style="background-image:url(<?php echo $viewer_photo ? $viewer_photo :  $this->baseUrl() . '/application/modules/Survey/externals/images/nophoto_user_thumb_normal.png'; ?>);"></div>
              </div>
              <div class="main_side_column">
                <div class="user_match"></div>
                <div class="user_match"></div>
                <div class="clr"></div>
              </div>
            </div>
            <div class="main_third_row">
              <div class="user_match bottom_left"></div>
              <div class="user_match bottom_near_left"></div>
              <div class="user_match bottom_near_right"></div>
              <div class="user_match bottom_right"></div>
              <div class="clr"></div>
            </div>
            <div class="first_matches_paging">
              <a href="javascript://" title="<?php echo $this->translate('previous'); ?>" class="match_prev_btn"></a>
              <a href="javascript://" title="<?php echo $this->translate('next'); ?>"  class="match_next_btn"></a>
              <div class="clr"></div>
            </div>
          </div>
        </div>
        <div class="side_column">
          <div class="user_match"></div>
          <div class="user_match"></div>
          <div class="user_match"></div>
          <div class="user_match"></div>
          <div class="clr"></div>
        </div>
      </div>
      <div class="third_row">
        <div class="user_match bottom_left"></div>
        <div class="user_match bottom_near_left"></div>
        <div class="user_match"></div>
        <div class="user_match"></div>
        <div class="user_match bottom_near_right"></div>
        <div class="user_match bottom_right"></div>
        <div class="clr"></div>
      </div>
      <div class="second_matches_paging">
        <a href="javascript://" title="<?php echo $this->translate('previous'); ?>" class="match_prev_btn"></a>
        <a href="javascript://" title="<?php echo $this->translate('next'); ?>"  class="match_next_btn"></a>
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    </div>
  <?php endif; ?>
  </div>
  
  <div id="content-tooks" class="survey_content display_none">
    <?php
      foreach ($this->surveyResults as $surveyResult) { ?>
        <div class="survey_user_result user-matches" style="padding: 10px;">
          <?php $survey_users = $this->result_list[$surveyResult->result_id]; ?>
          <h4><?php echo $surveyResult->getTitle(); ?></h4>

          <?php if ($survey_users['took_count'] > 0) : ?>
            <?php
              $count = 0;
              foreach ($survey_users['tooks'] as $surveyTook) {
                if ($count == $this->maxShowUsers) {
                  echo '<div class="align_right">'
                    . '<a href="javascript://" class="survey_view_more_link" onclick="he_list.box(\'survey\', \'getSurveyTakers\', \''
                    . $this->translate('survey_Latest people with such result:') . '\', {list_title2: \''
                    . $this->translate('Friends') . '\', survey_id: '
                    . $this->survey->survey_id . ', result_id: ' . $surveyResult->result_id . '});">'
                    . $this->translate('survey_View More') . '</a>'
                    . '</div>';

                  break;
                }
                
                $user_photo = $this->htmlLink($surveyTook->getOwner()->getHref(), $this->itemPhoto($surveyTook->getOwner(), 'thumb.icon'));
                $user_title = $this->htmlLink($surveyTook->getOwner()->getHref(), $surveyTook->getOwner()->getTitle());

                echo '<div class="survey_user float_right_rtl">'
                  . $user_photo . '<br/>'
                  . $user_title
                  . '</div>';
                        
                $count++;
              }?>
              <div class="clr"></div>
            <?php else : ?>
            <div class="no_content">
                <?php echo $this->translate("survey_There are no such results") ?>
            </div>
            <?php endif; ?>
        </div>
    <?php }?>
  </div>
  
  <div id="content-comments" class="survey_content display_none">
    <?php echo $this->action("list", "comment", "core", array("type"=>"surveys", "id"=>$this->survey->getIdentity())) ?>
  </div>

</div>

</div>
</div>