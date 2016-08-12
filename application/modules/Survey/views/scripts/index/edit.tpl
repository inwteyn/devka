<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2010-07-02 18:49 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });

  en4.core.runonce.add(function(){
    survey.manage_navigation(<?php echo $this->step_info?>);
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Edit Survey');?>
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
  <?php echo $this->form->render($this) ?>

  <br/>
  
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
