<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright 2006-2010 Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl idris $
 * @author     Idris
 */
?>

<h3><?php echo $this->translate('Last Taken Surveys'); ?></h3>

<ul>
<?php foreach($this->surveyes as $survey): ?>
<li class="he_survey_block">
  <?php echo $this->htmlLink($survey->getHref(), $this->ItemPhoto($survey, 'thumb.icon'), array('class' => 'widget_survey_photo')); ?>
  <div class="he_survey_info">
    <div class="he_survey_title">
      <?php echo $this->htmlLink($survey->getHref(), $survey->getTitle()); ?>
    </div>
    <div class="he_survey_desc"><?php echo $survey->getDescription(true); ?></div>
    <div class="he_survey_misc">
      <span class="he_survey_misc_important"><?php echo $this->timestamp($survey->took_date); ?></span>
    </div>
  </div>
</li>
<?php endforeach; ?>
</ul>