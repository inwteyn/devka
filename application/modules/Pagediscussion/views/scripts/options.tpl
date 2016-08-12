
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: options.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<div class="options">

  <a href="javascript:Pagediscussion.list();" class="back">
    <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_BACK')?>
  </a>

  <?php if ($this->hasViewer):?>

    <?php if ($this->canPost && !$this->topic->closed):?>
      <a href="javascript:Pagediscussion.post(<?php echo $this->topic_id?>);" class="post">
        <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_POST')?>
      </a>
    <?php endif;?>

    <?php if ($this->isWatching):?>
      <a href="javascript:Pagediscussion.discussion('watch', <?php echo $this->topic_id?>);" class="unwatching">
        <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_UNWATCHING')?>
      </a>
    <?php else: ?>
       <a href="javascript:Pagediscussion.discussion('watch', <?php echo $this->topic_id?>, true);" class="watching">
        <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_WATCHING')?>
      </a>
    <?php endif;?>

    <?php if ($this->isTeamMember):?>

      <?php if ($this->topic->sticky):?>
      <a href="javascript:Pagediscussion.discussion('sticky', <?php echo $this->topic_id?>);" class="unsticky">
        <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_UNSTICKY')?>
      </a>
      <?php else: ?>
        <a href="javascript:Pagediscussion.discussion('sticky', <?php echo $this->topic_id?>, true);" class="sticky">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_STICKY')?>
        </a>
      <?php endif;?>

      <?php if ($this->topic->closed):?>
        <a href="javascript:Pagediscussion.discussion('close', <?php echo $this->topic_id?>);" class="unclose">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_UNCLOSE')?>
        </a>
      <?php else: ?>
        <a href="javascript:Pagediscussion.discussion('close', <?php echo $this->topic_id?>, true);" class="close">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_CLOSE')?>
        </a>
      <?php endif;?>

    <?php endif;?>

    <?php if ($this->isTeamMember || $this->isOwner):?>

    <a href="javascript:Pagediscussion.rename(<?php echo $this->topic_id?>);" class="rename">
      <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_RENAME')?>
    </a>
    <a href="javascript:Pagediscussion.discussion('deletetopic', <?php echo $this->topic_id?>);" class="delete">
      <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_DELETE')?>
    </a>

    <?php endif;?>

    <?php if ($this->topic->closed && !$this->isTeamMember):?>
      <div class="pagediscussion_topic_closed"><?php echo $this->translate('PAGEDISCUSSION_CLOSED');?></div>
    <?php endif;?>

  <?php endif;?>

</div>