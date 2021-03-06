<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 vadim $
 * @author     Vadim
 */
?>

<h2><?php echo $this->translate('Welcome Usernotes Plugin'); ?></h2>

<script type="text/javascript">
  var fetchLevelSettings = function(level_id){
    window.location.href = en4.core.baseUrl + 'admin/usernotes/level/' + level_id;
  }
</script>


<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>


<div class='clr'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>