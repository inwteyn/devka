<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9979 2013-03-19 22:07:33Z john $
 * @author     Bolot
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
)
?>
<!--<h3>
  <?php /*echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) */?>
</h3>-->
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<?php if( count($this->users) ): ?>
  <ul id="browsemembers_ul_advs_large">
    <?php foreach( $this->users as $user ):

      $gender = $this->getGender($user);
      if(!$gender){
        $rand = rand(0,15);
      }elseif($gender != 'Male'){
        $rand = rand(0,7);
      }else{
        $rand = rand(7,15);
      }?>
      <li style="" rev="<?php echo $user->getIdentity();?>">
        <div class="user_list_left">
          <?php
          if(!$user->getPhotoUrl()){
            $imgProfile = 'background-color: '.$color[$rand].';color: #fff;font-size: 70px;    width: 85px;height: 85px;padding: 32px; text-decoration: none;';
          }else{
            $imgProfile = 'background-image: url('.$user->getPhotoUrl().');     color: transparent;';

          }

          ?>
          <a href="<?php echo $user->getHref();?>" class="profile_photo_headvmemebers"
             style="display: inline-block;<?php echo $imgProfile ?>">
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
                <?php // echo $this->heuserFriendship($user);  ?>
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
<?php endif ?>

<?php if( $this->users ):
    $pagination = $this->paginationControl($this->users, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
    ));
  ?>
  <?php if( trim($pagination) ): ?>
    <div class='browsemembers_viewmore' id="browsemembers_viewmore">
      <?php echo $pagination ?>
    </div>
  <?php endif ?>
<?php endif; ?>

<script type="text/javascript">
  page = '<?php echo sprintf('%d', $this->page) ?>';
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
  userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
  $$('#browsemembers_ul_advs_large li').each(function(element){
    element.addEvents({
      mouseover: function(){
       $('user_button_'+element.get('rev')).show();
      },
      mouseleave: function(){
        $('user_button_'+element.get('rev')).hide();
      }
  })

  });
</script>
