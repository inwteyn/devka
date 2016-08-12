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
<?php
$color = array(
  '#0eea90 ',
  '#133390 ',
  '#096bd0 ',
  '#3794ce ',
  '#6f9bc9 ',
  '#4ee2ae ',
  '#43c524 ',
  '#7076ed ',
  '#fd6ea3 ',
  '#c818bc ',
  '#f55508 ',
  '#f3f365 ',
  '#9efd97 ',
  '#f76d65 ',
  '#cb4be9 ',
  '#ed2e2c '
);
$latters_not_standaert = array(
  'M',
  'W',
  'Ш',
  'М',
  'Ш',
  'Щ',
  'Ю',
  'Д'
)
?>


<?php $viewer = Engine_Api::_()->user()->getViewer();?>
    <?php if( count($this->users) ): ?>
      <ul id="browsemembers_ul_advs_large_popular"  class="browsemembers_ul_advs_large_popular" style="box-sizing: border-box;display: inline-block;width: 100%;">
        <?php foreach( $this->users as $user ):?>
          <li  rev="<?php echo $user->getIdentity();?>" >
            <div class="user_list_left" style="height: auto">
              <?php
              $gender = $this->getGender($user);
              if(!$gender){
                $rand = rand(0,15);
              }elseif($gender != 'Male'){
                $rand = rand(0,7);
              }else{
                $rand = rand(7,15);
              }
              if(!$user->getPhotoUrl()){


                $imgProfile = 'background-color: '.$color[$rand].';color: #fff;font-size: 35px;width: 45px;height: 45px;padding: 19px; text-decoration: none;';
              }else{
                $imgProfile = 'background-image: url('.$user->getPhotoUrl().');     color: transparent;';

              }
              ?>
              <a href="<?php echo $user->getHref();?>" class="profile_photo_headvmemebers" style="display: inline-block; <?php echo $imgProfile ?>">
                <?php
                $name = str_replace(' ','',$user->getTitle());
                echo ucwords($name[0]); ?>
              </a>
              <?php if (Engine_Api::_()->headvancedmembers()->isActive($user)){ ?>
                <img class="irc_mi" style="    margin-bottom: -5px;cursor: pointer;width: 24px;position: absolute;top: 50px;height: 24px;right: 8px;"
                     src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_verified.png"
                     width="24" height="24" title="verified"></h2>
            <?php } ?>
            </div>
          </li>

        <?php endforeach; ?>
      </ul>

<?php else: ?>
  <h4><?php echo $this->translate('Members not found'); ?></h4>
<?php endif ?>


<style>
  #browsemembers_ul_advs_large_popular a.profile_photo_headvmemebers{
    width: 80px;
    height: 80px;
  }
</style>