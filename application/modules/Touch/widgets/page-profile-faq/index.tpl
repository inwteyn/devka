<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-09-21 17:53 ratbek $
 * @author     Ratbek
 */
?>


<script type="text/javascript">
  function showHide(faq_id)
  {
    if ($('pagefaq_answer_id_'+faq_id).getStyle('display') == 'none') {
      $('pagefaq_answer_id_'+faq_id).setStyle('display', 'block');
      $('pagefaq_question_id_'+faq_id).setProperty('class', 'pagefaq_question_class downarrow');
    } else {
      $('pagefaq_answer_id_'+faq_id).setStyle('display', 'none');
      $('pagefaq_question_id_'+faq_id).setProperty('class', 'pagefaq_question_class rightarrow');
    }
  }

  en4.core.runonce.add(function(){
     <?php echo $this->init_js_str; ?>
  });

  window.addEvent('domready', function (){
  });

</script>
<?php $url = $this->url(array('action' => 'edit', 'page_id' => $this->page_id), 'page_team', true); ?>
<?php if ($this->allFAQs->count()) : ?>

<div id="pagefaq_container_id" class="pagefaq_container_class">
  <div class="description">
    <?php if (isset($this->description->description) && $this->description->description) : ?>
      <?php echo $this->description->description; ?>
    <?php endif; ?>
  </div>
  <?php foreach($this->allFAQs as $faq): ?>
    <div id="pagefaq_question_id_<?php echo $faq->faq_id; ?>" class="pagefaq_question_class">
      <a href="javascript:showHide(<?php echo $faq->faq_id; ?>);"><?php echo $faq->question; ?></a>
    </div>

    <div id="pagefaq_answer_id_<?php echo $faq->faq_id; ?>" class="pagefaq_answer_class">
      <?php echo $faq->answer; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php else : ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('TOUCH_There are no FAQ.') ?>
    </span>
  </div>
<?php endif; ?>