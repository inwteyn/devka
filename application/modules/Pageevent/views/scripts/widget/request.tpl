
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: request.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<script type="text/javascript">

  function PageeventRequest(id, notification_id, approve, rsvp)
  {
    var $container = $('pageevent-request-'+notification_id);

    var request = new Request.JSON({
      secure: false,
      url: '<?php echo $this->url(array('action' => 'member-approve'), 'page_event')?>',
      method: 'post',
      data: {'id': id, 'rsvp': rsvp, 'format': 'json', 'approve': approve, 'rsvp': rsvp},
      onComplete: function(obj) {
        $container.set('html', obj.message);
      }
    }).send();
  }

</script>

<li id="pageevent-request-<?php echo $this->notification->notification_id ?>">
  <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
  <div>
    <div>
      <?php echo $this->translate('%1$s has invited you to the event %2$s', $this->notification->getSubject()->__toString(), $this->notification->getObject()->__toString()); ?>
    </div>
    <div>
      <button type="submit" onclick='PageeventRequest(<?php echo $this->notification->getObject()->getIdentity()?>, <?php echo $this->notification->notification_id ?>, 1, 2)'>
        <?php echo $this->translate('Attending');?>
      </button>
      <button type="submit" onclick='PageeventRequest(<?php echo $this->notification->getObject()->getIdentity() ?>, <?php echo $this->notification->notification_id ?>, 1, 1)'>
        <?php echo $this->translate('Maybe Attending');?>
      </button>
      <?php echo $this->translate('or');?>
      <a href="javascript:void(0);" onclick='PageeventRequest(<?php echo $this->notification->getObject()->getIdentity() ?>, <?php echo $this->notification->notification_id ?>, 0)'>
        <?php echo $this->translate('ignore request');?>
      </a>
    </div>
  </div>
</li>
