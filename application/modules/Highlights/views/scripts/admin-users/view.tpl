<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: delete.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <div>
      <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user))?>
      <h3><?php echo $this->user->getTitle() ?></h3>
    </div>
    <ul style="margin-bottom: 8px">
      <li>
        <?php echo $this->translate('Profile Views:') ?>
        <?php echo $this->translate(array('%s view', '%s views', $this->user->view_count),
          $this->locale()->toNumber($this->user->view_count)) ?>
      </li>
      <li>
        <?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction');
        if ( $direction == 0 ): ?>
          <?php echo $this->translate('Followers:') ?>
          <?php echo $this->translate(array('%s follower', '%s followers', $this->user->member_count),
            $this->locale()->toNumber($this->user->member_count)) ?>
        <?php else: ?>
          <?php echo $this->translate('Friends:') ?>
          <?php echo $this->translate(array('%s friend', '%s friends', $this->user->member_count),
            $this->locale()->toNumber($this->user->member_count)) ?>
        <?php endif; ?>
      </li>
      <li>
        <?php echo $this->translate('Last Update:'); ?>
        <?php echo $this->timestamp($this->user->modified_date) ?>
      </li>
      <li>
        <?php echo $this->translate('Joined:') ?>
        <?php echo $this->timestamp($this->user->creation_date) ?>
      </li>
    </ul>
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->highlight_id ?>"/>
      <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
