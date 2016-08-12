<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: requests.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<h2><?php echo $this->translate('HEBADGE_ADMIN_PAGE_REQUESTS_TITLE');?></h2>
<p><?php echo $this->translate('HEBADGE_ADMIN_PAGE_REQUESTS_DESCRIPTION');?></p>

<br />

<div class="hebadge_layout_general">


  <?php if( count($this->navigation) ): ?>
     <div class='tabs'>
       <?php
         echo $this->navigation()->menu()->setContainer($this->navigation)->render();
       ?>
     </div>
   <?php endif; ?>

  <div class="hebadge_layout_left">
    <?php echo $this->partial('_adminMenuTabs.tpl', 'hebadge');?>
  </div>

  <div class="hebadge_layout_center">

    <?php if (count($this->paginator)):?>

      <table class="admin_table admin_hebadge_requests_manage">
        <thead>
          <tr>
            <th width="40%"><?php echo $this->translate('HEBADGE_ADMIN_REQUEST_SENDER');?></th>
            <th width="40%"><?php echo $this->translate('HEBADGE_ADMIN_REQUEST_BADGE');?></th>
            <th width="40%"><?php echo $this->translate('HEBADGE_ADMIN_REQUEST_MESSAGE');?></th>
            <th width="40%"><?php echo $this->translate('HEBADGE_ADMIN_REQUEST_DATE');?></th>
            <th width="1%"><?php echo $this->translate('HEBADGE_ADMIN_PAGEBADGE_OPTIONS');?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->paginator as $item):

            if (empty($this->pages[$item->page_id]) || empty($this->badges[$item->pagebadge_id])){
              continue ;
            }
            $page = $this->pages[$item->page_id];
            $badge = $this->badges[$item->pagebadge_id];
          ?>
            <tr>
              <td>
                <div class="item_photo">
                  <a href="<?php echo $page->getHref();?>"><?php echo $this->itemPhoto($page, 'thumb.icon');?></a>
                </div>
                <div class="item_body">
                  <div class="item_title">
                    <a href="<?php echo $page->getHref();?>"><?php echo $page->getTitle();?></a>
                  </div>
                </div>
              </td>
              <td>
                <div style="overflow: hidden;">
                  <div class="item_photo">
                    <?php echo $this->itemPhoto($badge, 'thumb.icon')?>
                  </div>
                  <div class="item_body">
                    <div class="item_title"><?php echo $badge->getTitle()?></div>
                  </div>
                </div>
                <div class="item_description">
                  <?php echo $badge->getDescription()?>
                </div>
              </td>
              <td>
                <p>
                  <?php echo $item->message;?>
                </p>
              </td>
              <td>
                <?php echo $this->locale()->toDateTime($item->creation_date) ?>
              </td>
              <td>
                <a href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'request-approved', 'approved' => 1, 'page_id' => $page->getIdentity(), 'badge_id' => $badge->getIdentity()))?>"><?php echo $this->translate('HEBADGE_PAGEMEMBER_APPROVED');?></a>&nbsp;|&nbsp;<a href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'pagebadges', 'action' => 'request-approved', 'approved' => 0, 'page_id' => $page->getIdentity(), 'badge_id' => $badge->getIdentity()))?>"><?php echo $this->translate('HEBADGE_PAGEMEMBER_DISAPPROVED');?></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <br />

      <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->formValues,
        )); ?>
      <?php endif; ?>

    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("HEBADGE_ADMIN_PAGE_EMPTY") ?>
        </span>
      </div>
    <?php endif; ?>

  </div>

</div>