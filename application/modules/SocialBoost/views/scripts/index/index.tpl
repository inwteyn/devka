<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<div class="socialboost_modal" id="socialpopup" style="padding-bottom: 10px;">
  <div class="modal-body">
    <button id="socialboost_close_button" class="close" type="button">x</button>
    <div class="<!--sptitle-->">
      <h3>
        <?php
        if ($this->popupType == 'standard')
          echo $this->translate('Love us? Please share your love');
        else {
          echo $this->translate('Like Us');

          if ($this->isNewsletter) {
            echo ' ' . $this->translate('or') . ' ' . $this->translate('Subscribe');
          }
        }
        ?>
      </h3>
    </div>

    <div class="socialpopup_social">
      <table>
        <tr>
          <td class="social">
            <?php if ($this->twitter): ?>
<!--              <i id="sb_twitter" class="hei hei-twitter-square"></i>-->
              <div>
                <a href="<?php echo $this->twitter; ?>"
                   class="twitter-follow-button"
                   data-show-count="false" data-size="large"
                   data-show-screen-name="false"></a>
              </div>
            <?php endif; ?>
          </td>
          <td class="social">
            <?php if ($this->facebook && $this->fbAppId): ?>
<!--              <i id="sb_facebook" class="hei hei-facebook-square"></i>-->
              <div class="fb-like" data-href="<?php echo $this->facebook; ?>" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
            <?php endif; ?>
          </td>
          <td class="social">
            <?php if ($this->google): ?>
              <div data-callback="plusClick" data-href="<?php echo $this->google; ?>" class="g-plusone" data-annotation="none"></div>
            <?php endif; ?>
          </td>
        </tr>
        <?php if ($this->isNewsletter): ?>
          <tr>
            <td colspan="3" style="text-align: center;">
              <br/>
              <input type="text" class="input" placeholder="Email Address" id="socialboost_email">
              <button class="btn2" id="socialboost_subscribe_btn"><?php echo $this->translate('Subscribe'); ?></button>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>


    <div class="clearfix"></div>

    <?php if ($this->allowOffers || $this->allowCredits): ?>
      <hr>
      <div class="sptitle" style="text-align: center;"><?php echo $this->translate('To Redeem'); ?></div>
    <?php endif; ?>

    <?php if ($this->allowOffers): ?>
      <div class="sb-block spoffer" style="float: left;">
        <?php echo $this->itemPhoto($this->allowOffers, 'thumb.icon'); ?>
        <div>
          <h3><?php echo $this->htmlLink($this->allowOffers->getHref(), $this->allowOffers) ?></h3>

          <div class="">
            <?php $href = '<a href="' . $this->allowOffers->getHref() . '" target="_blank">...</a>'; ?>
            <?php echo $this->string()->truncate($this->allowOffers->getDescription(), 200, $href); ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($this->allowCredits): ?>
      <div class="sb-block spcredits" style="<?php if ($this->allowOffers) {
        echo 'float: right;';
      } ?> ">
        <img
          src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/SocialBoost/externals/images/coins.png'; ?>">

        <div>
          <?php echo $this->translate('%s credits pack!', $this->allowCredits); //todo ?>
        </div>
        <p>
          <?php echo $this->translate("Credits are a virtual currency you can use to send virtual gifts to your friends or purchase goods. Read more about the credits <a target='_blank' href='%s'>here</a>", $this->url(array(), 'credit_general')); ?>
        </p>
      </div>
    <?php endif; ?>

    <br>
  </div>
</div>