<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2015-10-06 16:58:20  $
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
)
?>
<!--<h3>
  <?php /*echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) */?>
</h3>-->
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<?php if( count($this->users) ):


  ?>
  <ul id="browsemembers_ul_normal_advhe">
    <?php foreach( $this->users as $user ):

      $gender = $this->getGender($user);
      if(!$gender){
        $rand = rand(0,15);
      }elseif($gender != 'Male'){
        $rand = rand(0,7);
      }else{
        $rand = rand(7,15);
      }?>
      <li style="">
        <div class="user_list_left">
          <?php
          if(!$user->getPhotoUrl()){


            $imgProfile = 'background-color: '.$color[$rand].';color: #fff;font-size: 60px;width: 80px;height: 80px;padding: 20px; text-decoration: none;';
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
                <img class="irc_mi" style="    margin-bottom: -5px;cursor: pointer;width: 24px;position: absolute;top: 100px;height: 24px;right: 15px;"
                     src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_verified.png"
                     width="24" height="24" title="verified">
            <?php }  ?>

          <?php if( $row == NULL ): ?>
            <?php if( $this->viewer()->getIdentity() ): ?>
              <div class='browsemembers_results_links'>
                <?php //s echo $this->heuserFriendship($user);  ?>
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


          <div class='browsemembers_results_info'  style="margin: 10px 0px 10px 10px;display: inline-block;">
            <?php echo $this->htmlLink($user->getHref(), $user->getTitle());
            $fInfo = $this->heuserFriends($user);?>
            <div class="user_count" >
              <i class="hei hei-group"></i> <span class="counts"><?php echo $fInfo['count']; ?></span>
              <?php if($fInfo['photo']):?>
              <i class="hei hei-picture-o"></i> <span class="counts"><?php echo $fInfo['photo_count']; ?></span>
              <?php endif;
              if($fInfo['video']):?>
                <i class="hei hei-video-camera"></i> <span class="counts"><?php echo $fInfo['video_count']; ?></span>
              <?php endif;
              ?>
            </div>
          </div><hr style="    width: 100%;position: absolute;">
        <div class='browsemembers_results_info' style="margin: 30px 0px 10px 10px;display: inline-block;
             ">
            <?php if($fInfo['count']>0):?>
              <?php foreach($fInfo['friends'] as  $frind):
                $f = Engine_Api::_()->getItem('user', $frind->resource_id);
                $name = str_replace(' ','',$f->getTitle());

                if(!$f->getPhotoUrl()){
                  $new_styles = 'padding: 15px;width: 20px;';
                  if(in_array(ucwords($name[0]),$latters_not_standaert)){
                    $new_styles = 'padding: 15px 15px 15px 10px;width: 25px;';
                  }
                  $imgProfile = 'background-color: '.$color[rand(0,15)].';color: #fff;font-size: 30px;height: 20px;'.$new_styles.'text-decoration: none;';
                }else{
                  $imgProfile = 'background-image: url('.$f->getPhotoUrl('thumb.icon').');     color: transparent;';
                }

              ?>
                <a href="<?php echo $f->getHref();?>" class="profile_photo_headvmemebers_friends" style="display: inline-block;<?php echo $imgProfile ?>">
                   <?php
                $name = str_replace(' ','',$f->getTitle());
                echo ucwords($name[0]); ?>
          </a>
                </a>
              <?php
              endforeach;
            else:

            if(!$user->isSelf($viewer)){
              $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
              if($moduleTb->isModuleEnabled('suggest') && !$user->isSelf($viewer)){
                $url = 'javascript:void(0)';
                $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";
                $label = $this->translate('Suggest To Friends');
                $params = array('class' => 'buttonlink suggest_widget_link', 'onClick' => 'window.friends.friend_'.$user->getIdentity().'.box();' );
                echo $this->htmlLink($url, $label, $params);
                ?>
              <script type="text/javascript">
                HESuggest.scriptpath = <?php echo Zend_Json_Encoder::encode($path); ?>;
                en4.core.runonce.add(function(){
                  if (!window.friends) {
                    window.friends = {};
                  }

                  var options = {
                    c: "window.friends.callback_<?php echo $user->getIdentity(); ?>.suggest",
                    listType: "all",
                    m: "suggest",
                    l: "getSuggestItems",
                    t: "<?php echo $this->translate("Suggest %s to your friends", $user->getTitle()); ?>",
                    ipp: 30,
                    nli: 0,
                    params: {
                      scriptpath: <?php echo Zend_Json_Encoder::encode($path); ?>,
                      suggest_type: 'link_user',
                      object_type: 'user',
                      object_id: <?php echo (int)$user->getIdentity(); ?>
                    }
                  };

                  window.friends.callback_<?php echo $user->getIdentity(); ?> = new FriendSuggest(<?php echo $user->getIdentity(); ?>);
                  window.friends.friend_<?php echo $user->getIdentity(); ?> = new HEContacts(options);
                });
              </script>
              <?php
              }else {
                echo '<span class="nofriendsSpan">' . $this->translate('No friends') . '</span>';
              }
            }



            endif;?>
          </div>
      </li>

    <?php endforeach; ?>
  </ul>
  <?php else: ?>
  <h4><?php echo $this->translate('Members not found'); ?></h4>
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
  window.addEvent('domready', function(){
    if(350*3 >=$('hememberslist').getSize().x){
      $$('#browsemembers_ul_normal_advhe li').each(function(element){
        element.setStyle('width','48%')
      })
    }
    if(360*3 <=$('hememberslist').getSize().x){
      $$('#browsemembers_ul_normal_advhe li').each(function(element){
        element.setStyle('width','350px')
      })
    }
    if(375*3 <=$('hememberslist').getSize().x){
      $$('#browsemembers_ul_normal_advhe li').each(function(element){
        element.setStyle('width','370px')
      })
    }
  });

</script>
