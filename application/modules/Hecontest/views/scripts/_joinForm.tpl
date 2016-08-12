<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeHecontest.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>

<?php
if (Engine_Api::_()->user()->getViewer()->getIdentity()) :
    $href = "<a href='javascript://' onclick='toggleTerms();'>" . $this->translate("terms") . "</a>";
    ?>
    <script type="text/javascript">
        hecontestCore.baseUrl = '<?php echo $this->baseUrl(); ?>';




        en4.core.runonce.add(function () {
            hecontestCore.uploader = hecontestCore.returnFancyUploadCreate(
                '<?php echo $this->url(array('action'=>'upload'),'hecontest_general'); ?>',
                '<?php echo $this->baseUrl() . "/externals/fancyupload/Swiff.Uploader.swf";?>',
                '<?php echo $this->hecontestJoinForm->_activeContestId;?>'
            );
            $('demo-browse-hecontest').removeEvents();
        });

        window.addEvent('domready', function (e) {
            if($('terms-element')) {
                $('terms-element').getElement('label').set("html", "<?php echo $this->translate('HECONTEST_Join form terms', $href); ?>");
            }
        });
        function toggleTerms() {
            var $terms = $('termsbody-wrapper');
            var height = $terms.getStyle('height');
            if (height == '0px') {
                height = '100px'
            } else {
                height = '0px'
            }
            var fx = new Fx.Morph(
                $terms,
                {
                    duration: 200,
                    link: 'chain'
                }
            );
            fx.start({'height': height});
        }
        function buyCredit(contest){
            var loader = $('hecontest_loader_form');
            if(contest>0){
                var params = {
                    'contest_id': contest,
                    'format': 'json'
                }
                if(loader) loader.setStyle('display','block');
                new Request.JSON({
                    url: en4.core.baseUrl + "contest/buycontest",
                    method: 'post',
                    data: params,
                    onSuccess: function (response) {
                        if (response.status) {
                            he_show_message("<?php echo $this->translate('Success, now you can join to this contest!');?>", "", 5000);
                           if($('hecontest-widget-join-form-wrappe-id')){
                               $('hecontest-widget-join-form-wrappe-id').set('html','<div id="hecontest-widget-join-contest-info"></div>'+response.form);
                               hecontestCore.uploader = hecontestCore.returnFancyUploadCreate(
                                 '<?php echo $this->url(array('action'=>'upload'),'hecontest_general'); ?>',
                                 '<?php echo $this->baseUrl() . "/externals/fancyupload/Swiff.Uploader.swf";?>',
                                 '<?php echo $this->hecontestJoinForm->_activeContestId;?>'
                               );
                               $('demo-browse-hecontest').removeEvents();
                               setTimeout(function(){
                                   if(loader) loader.setStyle('display','none');
                                   hecontestCore.join(this);
                               },1000);
                           }
                        } else {

                        }
                    }
                }).send();
            }
        }
    </script>
<?php endif; ?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        $('div.like_button_container').addEventListener('click', function(){

        });
    });
</script>

<div class="hecontest-widget-join-form-wrapper" style="display: none;" id="hecontest-widget-join-form-wrappe-id">
    <div class="hecontest_loader_form" style="display: none;" id="hecontest_loader_form"></div>
    <div id="hecontest-widget-join-contest-info"></div>
    <?php
    if($this->paided != 1) {
        echo $this->hecontestJoinForm->render($this);
    }else{ ?>
   <div id="show_message_choice" style="padding: 20px; text-align: center;">

       <?php echo $this->translate('This contest is paid, please purchase with credit.')?>
      <br />
       <span class="hecontest-price">
              <span class="hecontest_credit_icon">
                <span class="hecontest-credit-price"><?php echo $this->hecontest->getPrice()?> <?php echo $this->translate('Credit');?></span>
              </span>
            </span><br>
       <button onclick="buyCredit(<?php echo $this->hecontest->getIdentity();?>)"><?php echo $this->translate('Buy');?></button>
   </div>

    <?php

    }
    ?>

</div>
<div class="hecontest-join-screen" style="display: none;" onclick="hecontestCore.hideJoinForm(this);">
</div>
