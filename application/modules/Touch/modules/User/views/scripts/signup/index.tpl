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

<h2>
  <?php echo ( $this->title ? $this->translate($this->title) : '' ) ?>
</h2>

<script type="text/javascript">
  function skipForm() {
    document.getElementById("skip").value = "skipForm";
    $('SignupForm').submit();
  }
  function finishForm() {
    document.getElementById("nextStep").value = "finish";
  }
</script>

<div class="layout_content">
<?php echo $this->partial($this->script[0], $this->script[1], array(
  'form' => $this->form
)) ?>
</div>