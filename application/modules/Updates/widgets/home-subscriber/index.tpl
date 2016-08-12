<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-09-09 13:15 mirlan $
 * @author     Mirlan
 */
?>

<script type='text/javascript'>

en4.core.runonce.add(function()
{
  var $subscriber_input = $$('.updates_subscribe_input');
  var email = '<?php echo $this->translate('UPDATES_email...'); ?>';

  $subscriber_input.addEvents({
      'focus':function(){
        if ($(this).value.trim() == email)
        {
          $(this).setProperty('value', '');
          $(this).addClass('updates_subscribe_element_focused');
        }
      },
      'blur':function(){
        if ($(this).value.trim() == '')
        {
            $(this).removeClass('updates_subscribe_element_focused');
            if ($(this).getProperty('id') == 'updates_email_box')
            {
              $(this).setProperty('value', email);
            }
        }
      }
    });

  $$('.updates_subs_button').addEvent('click', function(){
    $email_address = $('updates_email_box').getProperty('value');

    if ($email_address == '' || $email_address == email)
    {
      he_show_message('<?php echo $this->translate('UPDATES_Please, provide email address!!!'); ?>', 'error');
      $('updates_email_box').addClass('updates_subscribe_element_focused');
      $('updates_email_box').focus();
    }
    else
    {
      switch($(this).getProperty('id'))
      {
        case 'subscribe':
          $task = 'subscribe';
          break;

        case 'unsubscribe':
          $task = 'unsubscribe';
          break;

        default:
          return ;
      }

      new Request.JSON({
        'url' : 'updates/ajax',
        'method' : 'post',
        'data' : {'format':'json', 'task':$task, 'updates_email_box':$email_address},
        onRequest: function(){
          $subscriber_input.setProperty('disabled', 'true');
          $subscriber_input.setStyle('background', '#E5E5E5');
          $subscriber_input.setStyle('background-image', "url('application/modules/Updates/externals/images/loading.gif')");
          $subscriber_input.setStyle('background-position', 'right center');
          $subscriber_input.setStyle('background-repeat', 'no-repeat');
          },
        onSuccess: function(response)
        {
          if (response.result.status == 1)
          {
            he_show_message(response.result.message);
          }
          else
          if (response.result.status == 0)
          {
            he_show_message(response.result.message, 'error');
          }

          $subscriber_input.setStyle('background-image', "");
          $subscriber_input.setStyle('background', '#ffffff');
          $subscriber_input.removeProperty('disabled');

          switch($task)
          {
            case 'subscribe':
              $item = 'email'; $item2 = email;
              break;

            case 'unsubscribe':
              $item = 'email'; $item2 = 'name';
              break;

            default:
              return ;
          }

          $('updates_'+$item+'_box').value = '';
          $('updates_'+$item+'_box').focus();
          $('updates_'+$item2+'_box').removeClass('updates_subscribe_element_focused');

        }
      }).send();
    }
    return false;
  });
});

</script>

<div id='loading' class='subscribe_loading' style="text-align: center; display: none;">
  <?php echo $this->htmlImage($this->baseUrl()
                      .  '/application/modules/Updates/externals/images/loading.gif'
                      , 'loading...', array('border'=>'0', 'style'=>''));
      ?>
</div>
    
<div class='generic_layout_container layout_user_list_popular1'>
<h3><?php echo $this->translate('UPDATES_Subscribe to updates'); ?></h3>

<ul>
  <li>
     <?php echo $this->translate('UPDATES_FORM_SUBSCRIBE_DESCRIPTION'); ?>
  </li>

  <li>
      <?php echo $this->form->render($this)?>
  </li>
</ul>
</div>