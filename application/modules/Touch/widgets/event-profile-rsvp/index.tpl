<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<script type="text/javascript">

  var url = '<?php echo $this->url(array(
    'route' => 'default',
    'module' => 'event',
    'controller' => 'member',
    'action' => 'rsvp',
    'event_id' => $this->subject->getIdentity(),
  'default', true))?>';

  en4.core.runonce.add(function (){
    $$('.touch_event_rsvp a').addEvent('click', function (){
      $$('.touch_event_rsvp .loader').addClass('active');
      var $element = $(this);
      var data = {
        'rsvp': $element.get('id').substr(5),
        'format': 'json'
      };
      var request = new Request.HTML({
        secure: false,
        url: url,
        method: 'post',
        data: data,
        onSuccess: function() {
          $$('.touch_event_rsvp .loader').removeClass('active');
          $$('.touch_event_rsvp a').removeClass('active');
          $element.addClass('active');
        }
      }).send();
    });
  });

</script>

<ul class="touch_event_rsvp">
  <li><a href="javascript:void(0);" class="<?php echo ($this->member->rsvp == 2) ? 'active' : '';?>" id="rsvp_2"><?php echo $this->translate('Attending')?></a></li>
  <li><a href="javascript:void(0);" class="<?php echo ($this->member->rsvp == 1) ? 'active' : '';?>" id="rsvp_1"><?php echo $this->translate('Maybe Attending')?></a></li>
  <li><a href="javascript:void(0);" class="<?php echo ($this->member->rsvp == 0) ? 'active' : '';?>" id="rsvp_0"><?php echo $this->translate('Not Attending')?></a></li>
  <li><div class="loader"></div></li>
</ul>
<div style="clear:both;"></div>
