<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: publish.tpl 2010-07-02 18:20 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js')
?>


<script type="text/javascript">
en4.core.runonce.add(function(){
  survey.manage_publish();
  survey.manage_navigation(<?php echo $this->step_info?>);
});
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Publish survey'); ?>
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
  <div class="survey_publish_form">
    <?php echo $this->form->render($this) ?>
  </div>
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