<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create-question.tpl 2010-07-02 19:07 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js');

  $this->headTranslate(array(
    'survey_Are you sure you want to delete this question?',
    '<b>WARNING</b>: This will also delete all the answers associated with this question!'
  ));

?>

<script type="text/javascript">
en4.core.runonce.add(function(){
  survey.edit_question_url = '<?php echo $this->edit_url?>';
  survey.delete_question_url = '<?php echo $this->delete_url?>';
  survey.manage_question();
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
    <div class="view_survey_questions">
      <h3><?php echo $this->translate('View Survey Questions'); ?></h3>

      <?php foreach($this->surveyQuestions as $item) :?>

      <div class="view_survey_question" id="question_<?php echo $item->question_id?>">
        <div class="question_title">
          <a href="javascript://" class="question_actions float_left_rtl delete_question_btn" ><?php echo $this->translate('Delete'); ?></a>
          <a href="javascript://" class="question_actions float_left_rtl edit_question_btn"><?php echo $this->translate('survey_Edit'); ?></a>
          <div style="width:470px;"><?php echo $item->text?></div>
          <div class="clr"></div>
        </div>
        <div class="question-body">
          <div class="question_description">
          <?php foreach($item->answers as $answer) :?>
            <?php echo $answer->label?> -> <b><?php echo $this->result_list[$answer->result_id]?></b>
            <div style="height: 4px;"></div>
          <?php endforeach;?>
          </div>
          <div class="question_photo">
          <?php if ($item->getPhotoUrl()) : ?>
            <?php $link_options = array('title' => $this->translate('View fullsize'), 'onclick' => "he_show_image('" . $item->getPhotoUrl() . "', $(this).getElement('img'))"); ?>
            <?php echo $this->htmlLink('javascript://', $this->itemPhoto($item, 'thumb.normal'), $link_options) ?>
          <?php endif; ?>
          </div>
          <div class="clr"></div>
        </div>
      </div>

      <?php endforeach;?>

      <div id="tip_cont_tpl" class="<?php echo ($this->surveyQuestions->count() != 0) ? 'display_none' : ''; ?>">
        <br/>
        <div class="tip"><span><?php echo $this->translate(array('survey_There are no questions yet. You need to create at least %s question', 'There are no questions yet. You need to create at least %s questions', $this->minQuestionCount), $this->minQuestionCount);?></span></div>
        <br/>
      </div>

      <div id="add_question_btn" class="add_another_question <?php echo ($this->surveyQuestions->count() != 0) ? 'display_none' : ''; ?>">
        <button type="submit" onclick="$('survey_create_question_cont').toggleClass('display_none'); $('text').focus();"><?php echo $this->translate('survey_Add Question'); ?></button>
      </div>
      <div id="add_another_question_btn" class="add_another_question <?php echo ($this->surveyQuestions->count() == 0) ? 'display_none' : ''; ?>">
        <button type="submit" onclick="$('survey_create_question_cont').toggleClass('display_none'); $('text').focus();"><?php echo $this->translate('survey_Add Another Question'); ?></button>
      </div>
    </div>
  </div></div></div>

  <br/>

  <div id="survey_create_question_cont" <?php if(!$this->form->isErrors()) echo 'class="display_none"';?>>
    <?php echo $this->form->render($this) ?>
    <br/>
  </div>

  <div class="create_survey_next">
    <button type="button" id="survey_next_btn"><?php echo $this->translate('survey_NEXT STEP >>>'); ?></button>
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