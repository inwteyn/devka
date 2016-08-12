
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: topic.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>


<div class="pagediscussion_topic">

  <div class="title"><?php echo $this->topic->getTitle()?></div>

  <?php echo $this->render('options.tpl')?>

  <ul>

  <?php foreach ($this->paginator as $post):?>

      <li id="post_<?php echo $post->getIdentity();?>">

        <div class="photo">

          <?php
            $owner = $post->getOwner();
            if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == '1' || Engine_Api::_()->getItem('page', $post->page_id)->getOwner() != $owner->getIdentity()) {
              echo $this->htmlLink($owner->getHref(), $owner->getTitle());
              echo '<br/>';
              echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'));
            }
            else {
              echo $this->htmlLink(Engine_Api::_()->getItem('page', $post->page_id)->getHref(), Engine_Api::_()->getItem('page', $post->page_id)->getTitle());
              echo '<br/>';
              echo $this->htmlLink(Engine_Api::_()->getItem('page', $post->page_id)->getHref(), $this->itemPhoto(Engine_Api::_()->getItem('page', $post->page_id), 'thumb.icon'));
            }
          ?>

        </div>

        <div class="info">

          <div class="details">

            <div class="options">

              <?php if ($this->canPost && !$this->topic->closed):?>
                 <a href="javascript:void(0);" onclick="Pagediscussion.quote(<?php echo $this->topic_id?>, '<?php echo $this->escape($owner->getTitle())?>','<?php echo $this->escape($owner->getHref())?>', this);" class="quote">
                  <?php echo $this->translate('PAGEDISCUSSION_POST_QUOTE')?>
                </a>
              <?php endif;?>
              <?php if ($this->hasViewer && ($owner->isSelf($this->viewer) || $this->isTeamMember)):?>
                <a href="javascript:Pagediscussion.edit(<?php echo $post->getIdentity()?>);" class="edit">
                  <?php echo $this->translate('PAGEDISCUSSION_POST_EDIT')?>
                </a>
                <a href="javascript:Pagediscussion.discussion('deletepost', <?php echo $post->getIdentity()?>);" class=" delete">
                  <?php echo $this->translate('PAGEDISCUSSION_POST_DELETE')?>
                </a>
              <?php endif;?>

            </div>

            <div class="date">
              <?php echo $this->translate('Posted');?> <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
            </div>

          </div>

          <div class="body">
            <?php echo nl2br($this->BBCode($post->body)) ?>
          </div>

          <div class="body_raw" style="display:none;">
            <?php echo $post->body; ?>
          </div>

        </div>

      </li>

  <?php endforeach;?>

   </ul>

<?php if ($this->paginator->getTotalItemCount() > 4): ?>

  <?php echo $this->render('options.tpl')?>

<?php endif;?>

</div>

<br />

<?php echo $this->paginationControl(
  $this->paginator,
  null,
  array("paginationPost.tpl","pagediscussion"),
  array('topic_id' => $this->topic_id)
)?>

<?php if ($this->canCreate): ?>

  <a href="javascript:Pagediscussion.create();" class="buttonlink create_link"><?php echo $this->translate('PAGEDISCUSSION_CREATE')?></a>

<?php endif;?>