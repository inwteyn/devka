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
);
?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
    <?php if( count($this->users) ): ?>
      <ul id="browsemembers_ul_advs_large"  class="browsemembers_ul_advs_widget" style="box-sizing: border-box;display: inline-block;width: 100%;">
        <?php foreach( $this->users as $user ):
          $gender = $this->getGender($user);
          if(!$gender){
            $rand = rand(0,15);
          }elseif($gender != 'Male'){
            $rand = rand(0,7);
          }else{
            $rand = rand(7,15);
          }?>
          <li style="width: auto" rev="<?php echo $user->getIdentity();?>" >
            <div class="user_list_left" style="height: auto">
              <?php
              if(!$user->getPhotoUrl()){


                $imgProfile = 'background-color: '.$color[$rand].';color: #fff;    font-size: 70px;width: 90px;height: 90px;padding: 29px; text-decoration: none;';
              }else{
                $imgProfile = 'background-image: url('.$user->getPhotoUrl().');     color: transparent;';

              }

              ?>
              <a href="<?php echo $user->getHref();?>" class="profile_photo_headvmemebers" style="display: inline-block;<?php echo $imgProfile ?>">
                <?php
                $name = str_replace(' ','',$user->getTitle());
                echo ucwords($name[0]); ?>
              </a>
              <?php if (Engine_Api::_()->headvancedmembers()->isActive($user)){ ?>
                <img class="irc_mi" style="    margin-bottom: -5px;cursor: pointer;width: 24px;position: absolute;top: 120px;height: 24px;right: 25px;"
                     src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_verified.png"
                     width="24" height="24" title="verified"></h2>
            <?php } ?>
              <?php if( $row == NULL ): ?>
                <?php if( $this->viewer()->getIdentity() ): ?>
                  <div class='browsemembers_results_links' id="user_button_<?php echo $user->getIdentity();?>" style="display: none">
                    <?php echo $this->heuserFriendship($user);  ?>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
            <?php

            $table = Engine_Api::_()->getDbtable('block', 'user');
            $select = $table->select()
              ->where('user_id = ?', $user->getIdentity())
              ->where('blocked_user_id = ?', $viewer->getIdentity())
              ->limit(1);
            $row = $table->fetchRow($select);

            ?>


            <div class='browsemembers_results_info'  style="">
              <?php echo $this->htmlLink($user->getHref(), $user->getTitle());
              echo '<br>';
              $b = $this->getBirthday($user);
              if($b) echo $this->getBirthday($user) .' '.$this->translate('years old');
              $ifon  = $this->heuserFriends($user);
              if($ifon['count']>0) echo '<br>'.$ifon['count'] . ' ' . $this->translate('Friends');
              ?>
          </li>

        <?php endforeach; ?>
      </ul>

<?php else: ?>
  <h4><?php echo $this->translate('Members not found'); ?></h4>
<?php endif ?>

<script>
  window.addEvent('domready', function(){
    $$('.browsemembers_ul_advs_widget').each(function(element){
      if(element.getSize().x<=350){
        var lis = element.getElements('li');
        if(lis.length){
          lis.each(function(li){
            li.setStyle('width','100%');
          });
        }
      }else{
        var lis = element.getElements('li');
        lis.each(function(li){
          li.setStyle('width','220px');
        });
      }
    });
  });
</script>