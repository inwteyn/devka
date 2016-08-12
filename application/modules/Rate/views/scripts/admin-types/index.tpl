<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 michael $
 * @author     Michael
 */

$this->headLink()
    ->appendStylesheet($this->baseUrl().'/application/modules/Rate/externals/styles/main.css');

?>

<h2><?php echo $this->translate("RATE_REVIEW_HEADER"); ?></h2>

<?php if (count($this->navigation)):?>
  <div class='tabs'><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?></div>
<?php endif; ?>

<div style="float:left;">
<!-- Select category -->
  <div class='settings'>
      <form action="<?php echo $this->url() ?>" method="get">
      <?php echo $this->translate('RATE_REVIEW_CATEGORY_PAGE')?>:
      <?php echo $this->formSelect('category_id', $this->category_id, array('onChange' => '$(this).getParent("form").submit()'), $this->categories)?>
    </form>
  </div>



<!--   REview Rates for Page pages      -->
  <div class='settings'>
      <?php echo $this->form->render(); ?>
  </div>
    <br />
<!--    end of Pages-->

<!--    Review Rates for Store products -->

    <div class='settings'>

        <form action="<?php echo $this->url() ?>" method="get">
            <?php echo $this->translate('RATE_REVIEW_CATEGORY_STORE')?>:
            <?php echo $this->formSelect('category2_id', $this->category2_id, array('onChange' => '$(this).getParent("form").submit()'), $this->categories2)?>
        </form>

    </div>

<div class="settings">
    <?php echo $this->form2->render(); ?>
</div>

<!--    end of Stores -->
  </div>

<div style="float:left;margin-left:20px;width:250px;">
  <?php echo $this->translate('RATE_REVIEW_FAQ')?>
</div>
<div style="clear:both;"></div>