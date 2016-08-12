<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 18:44 ermek $
 * @author     Ermek
 */
?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Surveyzes');?>
  </h2>
  <div class="tabs surveyzes_tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<br/>

<div class="survey_theme_<?php echo $this->theme_name; ?>">
<div>

<div class='layout_middle' style="width: 75%; float: left;">

  <?php if( $this->browse_paginator->getTotalItemCount() > 0 ): ?>
    <div class="surveyzes_browse">
      <?php foreach( $this->browse_paginator as $survey_item ): ?>
        <div class="survey_item">
          <div class='surveyzes_browse_photo float_right_rtl'>
            <?php echo $this->htmlLink($survey_item->getHref(), $this->itemPhoto($survey_item, 'thumb.normal')) ?>
          </div>
          <div class='surveyzes_browse_info'>
            <p class='surveyzes_browse_info_title'>
              <?php echo $this->htmlLink($survey_item->getHref(), $survey_item->getTitle()) ?>
            </p>
            <p class='surveyzes_browse_info_date'>
              <?php echo $this->translate('survey_Posted');?>
              <?php echo $this->timestamp(strtotime($survey_item->creation_date)) ?>
              <?php echo $this->translate('survey_by');?>
              <?php echo $this->htmlLink($survey_item->getOwner()->getHref(), $survey_item->getOwner()->getTitle()) ?>
              &nbsp;-&nbsp;
              <?php echo $this->translate(array('survey_<b>%s</b> view', '<b>%s</b> views', $survey_item->view_count), $this->locale()->toNumber($survey_item->view_count)) ?>
              &nbsp;-&nbsp;
              <?php echo $this->translate(array('survey_<b>%s</b> take', '<b>%s</b> takes', $survey_item->take_count), $this->locale()->toNumber($survey_item->take_count)) ?>
            </p>
            <div class="rate_survey_item">
              <?php echo $this->itemRate('survey', $survey_item->getIdentity()); ?>
            </div>
            <p class='surveyzes_browse_info_blurb'>
              <?php
                // Not mbstring compat
                echo $survey_item->getDescription(true, 200);
              ?>
            </p>
          </div>
          <div class="clr"></div>
        </div>
      <?php endforeach; ?>
    </div>
  
  <?php elseif( $this->category || $this->show == 2 || $this->search ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no surveys with that criteria.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('survey_Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array(), 'survey_create').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>

  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no surveys.'); ?>
        <?php if ($this->can_create):?>
          <?php echo $this->translate('survey_Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array(), 'survey_create').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  <div class='browse_nextlast'>
    <?php echo $this->paginationControl($this->browse_paginator, null, array("pagination/surveypagination.tpl","survey")); ?>
  </div>

</div>


<div class='layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( $this->can_create): ?>
  <div class="quicklinks" style="margin-bottom:15px;">
    <ul>
      <li>
        <a href='<?php echo $this->url(array(), 'survey_create', true) ?>' class='buttonlink icon_survey_new'><?php echo $this->translate('Create New Survey');?></a>
      </li>
    </ul>
  </div>
  <?php endif; ?>

  <?php echo $this->content()->renderWidget('survey.most-taken'); ?>
  <?php echo $this->content()->renderWidget('survey.recent-taken'); ?>
  <?php echo ($this->rateEnabled) ? $this->content()->renderWidget('rate.survey-rate') : ''; ?>
  
</div>

</div>
</div>