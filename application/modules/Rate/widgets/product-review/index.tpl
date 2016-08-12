<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Rate/externals/scripts/ProductReview.js');

  ?>

  <script type="text/javascript">
    function openCreateForm() {
      var t = $$('.tab_layout_rate_product_review');
      if(t && t.length > 0) {
        t[0].click();
        ProductReview.create();
      }
    }
    en4.core.runonce.add(function () {
      ProductReview.id = <?php echo $this->id?>;
      ProductReview.url.create = '<?php echo $this->url(array('action' => 'create'), 'prod_review')?>';
      ProductReview.url.edit = '<?php echo $this->url(array('action' => 'edit'), 'prod_review')?>';
      ProductReview.url.list = '<?php echo $this->url(array('action' => 'list'), 'prod_review')?>';
      ProductReview.url.remove = '<?php echo $this->url(array('action' => 'remove'), 'prod_review')?>';
      ProductReview.url.view = '<?php echo $this->url(array('action' => 'view'), 'prod_review')?>';
      ProductReview.allowedComment = <?php echo (int)(bool)$this->viewer->getIdentity()?>;
      ProductReview.init();
      <?php echo implode(" ", $this->js)?>
    });

    window.addEvent('load', function () {
      <?php echo $this->init_js; ?>
    });

  </script>

  <div class="pagerate_loader hidden" id="pagerate_loader">
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Rate/externals/images/loader.gif'); ?>
  </div>
  <div class="clr"></div>

  <div class="productreview_container_message hidden">
    <ul class="success form-notices" style="margin-top:0;">
      <li></li>
    </ul>
    <ul class="error form-errors" style="margin-top:0;">
      <li></li>
    </ul>
  </div>



  <div class="productreview_container_list">
    <?php echo $this->render('list.tpl'); ?>
  </div>


  <div class="productreview_container_create hidden">
    <?php echo $this->form->render() ?>
  </div>

  <div class="productreview_container_edit hidden"></div>
  <div class="productreview_container_view hidden"></div>
