<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2010-07-02 18:31 ermek $
 * @author     Ermek
 */
?>

<?php
  $this->headScript()
    ->appendFile('application/modules/Survey/externals/scripts/Survey.js');
?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Surveys'); ?>
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

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
  
  <?php if( $this->viewer()->getIdentity() ): ?>
  <div class="quicklinks">
    <ul>
      <?php if( $this->can_create): ?>
      <li>
        <a href='<?php echo $this->url(array(), 'survey_create', true) ?>' class='buttonlink icon_survey_new'><?php echo $this->translate('Create New Survey')?></a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
  <?php endif; ?>
</div>

<div class='layout_middle survey_theme_<?php echo $this->theme_name; ?>'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div class="surveyzes_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <div class="survey_item" id="survey_<?php echo $item->survey_id;?>">
          <div class='surveyzes_browse_photo float_right_rtl'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>
          <div class='surveyzes_browse_options float_left_rtl'>
            <a href='<?php echo str_replace("survey_id", $item->survey_id, $this->edit_url); ?>' class='buttonlink icon_survey_edit edit_survey_btn'><?php echo $this->translate('Edit Survey'); ?></a>
            <a href='<?php echo str_replace("survey_id", $item->survey_id, $this->delete_url); ?>' class='buttonlink icon_survey_delete delete_survey_btn'><?php echo $this->translate('Delete Survey'); ?></a>
          </div>
          <div class='surveyzes_browse_info'>
            <p class='surveyzes_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'float_right_rtl')) ?>&nbsp;&nbsp;&nbsp;&nbsp;
              <?php if ($item->getOwner()->isSelf($this->viewer) || $this->level_id == 1) : ?>
                <?php if ($item->published) : ?>
                  <span style="color: green;" class="surveyzes_pub_app"><?php echo $this->translate('survey_published'); ?></span>&nbsp;&nbsp;&nbsp;
                <?php else: ?>
                  <span style="color: red;" class="surveyzes_pub_app"><?php echo $this->translate('survey_not published'); ?></span>&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
                <?php if ($item->approved) : ?>
                  <span style="color: green;" class="surveyzes_pub_app"><?php echo $this->translate('survey_approved'); ?></span>&nbsp;&nbsp;&nbsp;
                <?php else : ?>
                  <span style="color: red;" class="surveyzes_pub_app"><?php echo $this->translate('survey_not approved'); ?></span>&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
              <?php endif; ?>
            </p>
            <p class='surveyzes_browse_info_date'>
              <?php echo $this->translate('survey_Posted by');?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
              <?php echo $this->translate('survey_about');?>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              &nbsp;-&nbsp;
              <?php echo $this->translate(array('survey_<b>%s</b> view', '<b>%s</b> views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              &nbsp;-&nbsp;
              <?php echo $this->translate(array('survey_<b>%s</b> take', '<b>%s</b> takes', $item->take_count), $this->locale()->toNumber($item->take_count)) ?>
           </p>
            <div class="rate_survey_item">
              <?php echo $this->itemRate('survey', $item->getIdentity()); ?>
            </div>
            <p class='surveyzes_browse_info_blurb'>
              <?php
                // Not mbstring compat
                echo $item->getDescription(true, 200);
              ?>
            </p>
          </div>
          <div class="clr"></div>
        </div>
      <?php endforeach; ?>
  </div>
  
  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any surveys that match your search criteria.');?>
      </span>
    </div>

  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any surveys.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by %1$screating%2$s a new survey.', '<a href="'.$this->url(array(), 'survey_create').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  
  <?php endif; ?>

  <div class='browse_nextlast'>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination/surveypagination.tpl","survey"), array("orderby"=>$this->orderby)); ?>
  </div>

</div>