<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    AvatarStyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    AvatarStyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  var changeCredit = function(elem)
  {
    if (elem.get('checked') ) {
      $('credit_amount-wrapper').setStyle('display', 'block');
    } else {
      $('credit_amount-wrapper').setStyle('display', 'none');
    }
  }

  var changeOffer = function(elem)
  {
    try {
      if (elem.get('checked') ) {
        $('choose_offer-wrapper').setStyle('display', 'block');
        $('choosen_offer-wrapper').setStyle('display', 'block');
      } else {
        $('choose_offer-wrapper').setStyle('display', 'none');
        $('choosen_offer-wrapper').setStyle('display', 'none');
      }
    } catch( e ) {}
  }

  window.addEvent('domready', function(){
    changeCredit($('credit'));
    changeOffer($('offers'));
  });
</script>

<div class="settings">
  <?php echo $this->form->render($this);?>
</div>
