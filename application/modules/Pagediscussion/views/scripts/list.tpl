
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<?php if (count($this->paginator)) : ?>
  <ul class="item">
    <?php foreach ($this->paginator as $topic):
      $lastPost = $topic->getLastPost();
      $lastPoster = $topic->getLastPoster();
    ?>
      <li>
        <div class="replies">
          <span><?php echo $this->locale()->toNumber($topic->getCountPost())?></span>
          <?php echo $this->translate(array('reply', 'replies', $topic->getCountPost())) ?>
        </div>
        <div class="lastreply">
        <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $topic->page_id)->getOwner() != $lastPoster):?>
            <?php echo $this->htmlLink($lastPoster->getHref(), $this->itemPhoto($lastPoster, 'thumb.icon'))?>
        <?php else:?>
            <?php echo $this->htmlLink(Engine_Api::_()->getItem('page', $topic->page_id)->getHref(), $this->itemPhoto(Engine_Api::_()->getItem('page', $topic->page_id), 'thumb.icon'))?>
        <?php endif;?>
          <div class="info">
            <a href="<?php echo $topic->getHref(); ?>" onclick="Pagediscussion.topic(<?php echo $topic->getIdentity()?>, null, <?php echo $lastPost->getIdentity()?>); return false;">
              <?php echo $this->translate('PAGEDISCUSSION_LASTPOST')?>
            </a>
            <?php echo $this->translate('by');?>
            <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $topic->page_id)->getOwner() != $lastPoster):?>
              <?php echo $lastPoster->__toString()?><br />
            <?php else:?>
              <?php echo $this->htmlLink(Engine_Api::_()->getItem('page', $topic->page_id)->getHref(), Engine_Api::_()->getItem('page', $topic->page_id)->getTitle())?>
            <?php endif;?>
            <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'date')) ?>
          </div>
        </div>
        <div class="details">
          <div class="title"><a href="<?php echo $topic->getHref(); ?>" onclick="Pagediscussion.topic(<?php echo $topic->getIdentity()?>); return false;" class="<?php if ($topic->sticky):?>sticky<?php endif;?> <?php if ($topic->closed):?>closed<?php endif;?>"><?php echo $topic->getTitle()?></a></div>
          <?php echo $topic->getDescription()?>
        </div>
      </li>
    <?php endforeach;?>
  </ul>
  <br />

  <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","pagediscussion"), array(
    'page' => $this->pageObject
  ))?>

  <?php if ($this->canCreate): ?>
    <a href="javascript:Pagediscussion.create();" class="buttonlink create_link"><?php echo $this->translate('PAGEDISCUSSION_CREATE')?></a>
    <div style="clear:both;"></div>
  <?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('PAGEDISCUSSION_NOTOPIC');?>
      <?php if ($this->canCreate):?>
        <?php echo $this->translate('PAGEDISCUSSION_NOTOPIC_CREATE',  '<a href="javascript:void(0);" onClick="Pagediscussion.create();">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif;?>