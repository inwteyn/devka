<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>
<?php if($this->isSelf){ ?>
<button id="verify_b"
  <?php if(!$this->disable){?>
  onclick="Verifyme()"
  <?php }else{
    echo 'class="disabled"';

  }?>
  ><?php echo $this->translate('Verify Me')?></button>
  <?php }else{ ?>
  <button id="verify_b"
    <?php if(!$this->disable){?>
      onclick="VerifyMember()"
    <?php }else{
      echo 'class="disabled"';

  }?>><?php echo $this->translate('Verify this member')?></button>
<?php }; ?>

<?php if(!$this->disable){?>
<script>
  function Verifyme(){
    var data = '';
    (new Request.JSON({
      secure: false,
      url: en4.core.baseUrl + 'headvancedmembers/index/verifyme',
      method: 'post',
      data: data,
      onSuccess: function (obj) {

      }
    })).send();
    $('verify_b').set('onclick','');
    $('verify_b').set('class','disabled');
    he_show_message('<?php echo 'Your request has been sent.'; ?>');
  }
  function VerifyMember(){
    var data = {'user_id':<?php echo $this->subject_identity ?>};
    (new Request.JSON({
      secure: false,
      url: en4.core.baseUrl + 'headvancedmembers/index/verifyuser',
      method: 'post',
      data: data,
      onSuccess: function (obj) {

      }
    })).send();
    $('verify_b').set('onclick','');
    $('verify_b').set('class','disabled');
    he_show_message('<?php echo 'Your request has been sent.'; ?>');
  }
</script>
<?php }?>