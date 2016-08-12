<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create-result.tpl 2010-07-02 18:55 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js');

  $langVars = array(
    'survey_Are you sure you want to delete this result?',
    '<b>WARNING</b>: This will also delete all the answers associated with this result!'
  );

  $this->headTranslate($langVars);
?>


<script type="text/javascript">
en4.core.runonce.add(function(){
  survey.edit_result_url = '<?php echo $this->edit_url?>';
  survey.delete_result_url = '<?php echo $this->delete_url?>';
  survey.manage_result();
  survey.manage_navigation(<?php echo $this->step_info?>);
});
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Survey Results');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class="survey_edit_title">
  <span class="float_right_rtl"><?php echo $this->survey->getTitle(); ?></span>&nbsp;&nbsp;&nbsp;
  <?php if ($this->survey->published) : ?>
    <span style="color: green;" class="surveyzes_pub_app"><?php echo $this->translate('survey_published'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php else: ?>
    <span style="color: red;" class="surveyzes_pub_app"><?php echo $this->translate('survey_not published'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php endif; ?>
  <?php if ($this->survey->approved) : ?>
    <span style="color: green;" class="surveyzes_pub_app"><?php echo $this->translate('survey_approved'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php else : ?>
    <span style="color: red;" class="surveyzes_pub_app"><?php echo $this->translate('survey_not approved'); ?></span>&nbsp;&nbsp;&nbsp;
  <?php endif; ?>
</div>

<div class="layout_left" style="width: auto">

  <div class="global_form"><div><div>
    <div class="view_survey_results">
      <h3><?php echo $this->translate('View Survey Results');?></h3>

      <?php foreach($this->surveyResults as $item) :?>

      <div class="view_survey_result" id="result_<?php echo $item->result_id?>">
        <div class="result_title">
          <a href="javascript://" class="result_actions float_left_rtl delete_result_btn" ><?php echo $this->translate('Delete'); ?></a>
          <a href="javascript://" class="result_actions float_left_rtl edit_result_btn"><?php echo $this->translate('survey_Edit'); ?></a>
          <div style="width: 470px;"><?php echo $item->title?></div>
          <div class="clr"></div>
        </div>
        <div class="result-body">
          <div class="result_description"><?php echo $item->description?></div>
          <div class="result_photo">
          <?php if ($item->getPhotoUrl()) :?>
            <?php $link_options = array('title' => $this->translate('View fullsize'), 'onclick' => "he_show_image('" . $item->getPhotoUrl() . "', $(this).getElement('img'))"); ?>
            <?php echo $this->htmlLink('javascript://', $this->itemPhoto($item, 'thumb.normal'), $link_options) ?>
          <?php endif;?>
          </div>
          <div class="clr"></div>
        </div>
      </div>

      <?php endforeach;?>

      <div id="tip_cont_tpl" class="<?php echo ($this->surveyResults->count() != 0) ? 'display_none' : ''; ?>">
        <br/>
        <div class="tip"><span><?php echo $this->translate(array('survey_There are no results yet. You need to create at least %s result', 'There are no results yet. You need to create at least %s results', $this->minResultCount), $this->minResultCount); ?></span></div>
        <br/>
      </div>

      <div id="add_result_btn" class="add_another_result <?php echo ($this->surveyResults->count() != 0) ? 'display_none' : ''; ?>">
        <button type="submit" onclick="$('survey_create_result_cont').toggleClass('display_none'); $('title').focus();"><?php echo $this->translate('Add Result'); ?></button>
      </div>
      <div id="add_another_result_btn" class="add_another_result <?php echo ($this->surveyResults->count() == 0) ? 'display_none' : ''; ?>">
        <button type="submit" onclick="$('survey_create_result_cont').toggleClass('display_none'); $('title').focus();"><?php echo $this->translate('survey_Add Another Result'); ?></button>
      </div>
    </div>
  </div>
  </div>
  </div>

  <br/>

  <div id="survey_create_result_cont" <?php if(!$this->form->isErrors()) echo 'class="display_none"';?>>
    <?php echo $this->form->render($this) ?>
    <br/>
  </div>

  <div class="create_survey_next">
    <button type="button" id="survey_next_btn"><?php echo $this->translate('survey_NEXT STEP >>>') ?></button>
  </div>

  <br/>

</div>

<div class="layout_right">
<?php if( count($this->survey_navigation) ): ?>
  <div class='headline tabs survey_tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->survey_navigation)->render()
    ?>
  </div>
<?php endif; ?>
</div>