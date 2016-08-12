<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: index.tpl 2/9/12 11:10 AM mt.uulu $
 * @author Mirlan
 */
?>

<?php
$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Touch/modules/Timeline/externals/scripts/manager.js')
  ->appendFile($this->baseUrl().'/application/modules/Touch/externals/scripts/libs/dragscroll.js');

?>

<?php
/**
 * @var $navigation Zend_Navigation
 * @var $nav Zend_Navigation_Page_Mvc
 */
?>
<script type="text/javascript">

</script>
<div class="profile" id='profile'>
  <?php echo $this->content()->renderWidget('touch.timeline-cover'); ?>

  <div class="tl-block info">

    <div class="main-row">
      <div class="name">
        <div class="tl-profile-photo">
          <?php echo $this->htmlLink(
          $this->subject()->getHref(),
          $this->itemPhoto($this->subject(), 'thumb.profile'),
          array('id' => 'profile-photo', 'class' => 'tl-in-block')); ?>
        </div>

        <div class="tl-profile-title">
          <?php echo $this->content()->renderWidget('touch.profile-status') ?>
        </div>
      </div>
      <div class="options">
        <?php echo $this->content()->renderWidget('touch.user-profile-options'); ?>
      </div>
    </div>

    <?php if( !$this->private ): ?>
    <div class="additional-row" id="additional-row">
      <div class="applications">
        <div  style="overflow: hidden; width:<? echo (100*(count($this->widgets) + 3)).'px' ?>;">
          <?php $pf_name = 'touch.user-profile-fields';
          if( array_key_exists($pf_name, $this->widgets)) {
          $pf_widget = $this->widgets[$pf_name];
          ?>
          <a href="<?php echo $this->url() . '?tab=' .$pf_widget->content_id.'&from_tl=true' ?>" class="application touchajax">
              <div class="photo about">
                <?php echo $this->itemPhoto($this->subject(), 'thumb.profile') ?>
              </div>
              <div class="title">
                <?php echo $this->translate('TOUCH_tl_profile_info'); ?>
              </div>
          </a>
            <?php } ?>
        <?php $i = 0;
        foreach ($this->widgets as $widget):
          if($widget->name=='touch.user-profile-fields')
            continue;
          try{
          ?>
          <?php if (!array_key_exists($widget->name, $this->noneActiveApplications)): $i++;?>
            <a href="<?php echo $this->url() . '?tab=' .$widget->content_id.'&from_tl=true' ?>" class="application touchajax">

                <div class="photo <?php echo str_replace('.', '-', $widget->name); ?>">
                <?php
                if( array_key_exists($widget->name, $this->activeApplications)): $j = 0; ?>
                    <?php foreach ($this->activeApplications[$widget->name]['items'] as $item): ?>
                        <?php if ($j == 3): $j = 0; ?> <br/><?php endif; ?>
                        <?php echo $this->itemPhoto($item, 'thumb.icon');
                        $j++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                  <div class="default">&nbsp;</div>
                <?php endif; ?>
              </div>

              <div class="title">
                  <?php echo  $this->translate($this->widget_params[$widget->name]['title']); ?>
                  <span>
                    <?php if(array_key_exists('count', $this->widget_params[$widget->name])): ?>
                      <?php echo '('. $this->widget_params[$widget->name]['count'] . ')'; ?>
                    <?php endif; ?>
                  </span>
              </div>
            </a>
            <?php endif; ?>
        <?php
          } catch( Exception $e) {
            print_log($e);
          }
        endforeach;
        ?>
        </div>
      </div>

    </div>
    <?php endif; ?>

  </div>
</div>

<?php if( $this->private ): ?>
<div class="tip private">
  <span><?php echo $this->translate("This profile is private - only friends of this member may view it."); ?></span>
</div>
<?php endif; ?>