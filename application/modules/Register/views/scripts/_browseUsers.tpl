<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<h3>
  <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
</h3>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<ul id="browsemembers_ul">
  <?php foreach( $this->users as $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
      <?php 
      $table = Engine_Api::_()->getDbtable('block', 'user');
      $select = $table->select()
        ->where('user_id = ?', $user->getIdentity())
        ->where('blocked_user_id = ?', $viewer->getIdentity())
        ->limit(1);
      $row = $table->fetchRow($select);
      ?>
      <?php if( $row == NULL ): ?>
        <?php if( $this->viewer()->getIdentity() ): ?>
        <div class='browsemembers_results_links'>
          <?php echo $this->TJFriendship($user) ?>
        </div>
      <?php endif; ?>
      <?php endif; ?>

        <div class='browsemembers_results_info'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
          <?php echo $user->status; ?>
          <?php if( $user->status != "" ): ?>
            <div>
              <?php echo $this->timestamp($user->status_date) ?>
            </div>
          <?php endif; ?>
        </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if( $this->users ): ?>
  <div class='browsemembers_viewmore' id="browsemembers_viewmore">
    <?php echo $this->paginationControl($this->users, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      //'params' => $this->formValues,
    )); ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  page = '<?php echo sprintf('%d', $this->page) ?>';
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
  userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
</script>