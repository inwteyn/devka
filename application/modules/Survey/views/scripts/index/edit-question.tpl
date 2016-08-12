<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit-question.tpl 2010-07-02 18:51 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js');
?>

<script type="text/javascript">
en4.core.runonce.add(function(){
  survey.manage_navigation(<?php echo $this->step_info?>);
});
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Edit Survey Question');?>
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

<div class="layout_left" style="width: auto">
  <?php echo $this->form->render($this) ?>
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