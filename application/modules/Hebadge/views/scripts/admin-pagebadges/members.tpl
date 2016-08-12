<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: members.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<h2><?php echo $this->translate('HEBADGE_ADMIN_PAGE_MEMBERS_TITLE');?></h2>
<p><?php echo $this->translate('HEBADGE_ADMIN_PAGE_MEMBERS_DESCRIPTION');?></p>

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


    <div class='admin_search'>
      <?php echo $this->filterForm->render($this) ?>
    </div>

    <br />

    <div class='admin_results'>
      <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
          //'params' => $this->formValues,
        )); ?>
      </div>
    </div>

    <br />


    <?php if (count($this->paginator)):?>

      <table class="admin_table admin_hebadge_manage">
        <thead>
          <tr>
            <th class='admin_table_short'>ID</th>
            <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></th>
            <th><?php echo $this->translate("Category") ?></a></th>
            <th><?php echo $this->translate("Owner") ?></th>
            <th class="center"><?php echo $this->translate("Views") ?></th>
            <th class="center"><?php echo $this->translate("Date") ?></th>
            <th class="center"><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($this->paginator as $item):	 ?>
          <?php
            if($item->name == 'default') continue;
          ?>
            <tr class="<?php if ($item->sponsored) echo "admin_featured_page"; ?>">
              <td><?php echo $item->getIdentity() ?></td>
              <td><?php echo $this->htmlLink($item->getHref(), ($item->getTitle() ? $item->getTitle() : "<i>".$this->translate("Untitled")."</i>" )); ?></td>
              <td><?php echo ($item->category ? $item->category : ("<i>".$this->translate("Uncategorized")."</i>")); ?></td>
              <td><?php echo $this->htmlLink($this->user($item->user_id)->getHref(), $this->user($item->user_id)->getTitle()); ?></td>
              <td class="center"><?php echo $this->locale()->toNumber($item->view_count) ?></td>
              <td class="center"><?php echo $item->creation_date ?></td>
              <td class="center">
                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'edit-member', 'id' => $item->getIdentity(), 'page' => 1));?>'>
                  <?php echo $this->translate("HEBADGE_MEMBERS_PAGEITEM_BADGES") ?>
                </a>
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
          <?php echo $this->translate("HEBADGE_ADMIN_EMPTY") ?>
        </span>
      </div>
    <?php endif; ?>

  </div>

</div>


