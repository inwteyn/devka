<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: take.tpl 2010-07-02 18:21 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js');
   
  $this->headTranslate(array(
    'survey_Please answer for all questions!',
    'You successfully passed the survey.'
  ));
?>

<script type="text/javascript">
en4.core.runonce.add(function(){
  survey.take_survey();
});
</script>

<div class="headline">
  <h2>
    <?php echo $this->survey->title;?>
  </h2>
</div>

<div class="layout_middle" style="padding:10px;">

<div style="width: 300px;">
  <div class="survey_progress_bar" style="float: left;">
    <div class="progress_top progress_status"></div>
    <div class="progress_bottom progress_status"></div>
    <div class="progress_line"></div>
    <div class="progress_invite_text"><?php echo $this->translate('survey_Lets Go!!!'); ?></div>
  </div>
  <div style="float: left; padding: 8px; font-size: 15px; margin-left: 5px;">
    <?php echo $this->translate('survey_Question'); ?> <b><span id="answered_questions">1</span></b> <?php echo $this->translate('survey_of'); ?> <b><?php echo $this->question_count?></b>
  </div>
  <div class="clr"></div>
</div>
<br/>
<div style="margin-top: 10px;">
  <table style="margin: auto;">
    <tr>
      <td style="text-align: right; padding-right: 10px;">
        <button type="button" id="move_left"><?php echo $this->translate('survey_&laquo; Previous'); ?></button>
      </td>
      <td>
        <div class="take_survey">
          <?php echo $this->form->render($this) ?>
          <div class="clr"></div>
        </div>
      </td>
      <td style="text-align: left; padding-left: 10px;">
        <button type="button" id="move_right"><?php echo $this->translate('survey_Next &raquo;'); ?></button>
      </td>
    </tr>
    <tr>
      <td></td>
      <td style="text-align: center; padding: 10px;"><button type="button" onclick="survey.get_take_result();"><?php echo $this->translate('survey_Get Result'); ?></button></td>
      <td></td>
    </tr>
  </table>
</div>
</div>