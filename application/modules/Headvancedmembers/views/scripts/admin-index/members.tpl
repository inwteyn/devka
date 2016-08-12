
<h2>
    <?php echo $this->translate('Hire-Expert Advanced Members') ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<br />
<?php
$settings = Engine_Api::_()->getApi('settings', 'core');

?>
<br />
<br />

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function(order, default_direction){
        // Just change direction
        if( order == currentOrder ) {
            $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

    function multiModify()
    {
        var multimodify_form = $('multimodify_form');
        if (multimodify_form.submit_button.value == 'delete')
        {
            return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
        }
    }

    function selectAll()
    {
        var i;
        var multimodify_form = $('multimodify_form');
        var inputs = multimodify_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
    function change_status_verified(element){
        var id = $(element).get('rev');
        var data = {'user_ud':id};
        (new Request.HTML({
            secure: false,
            url: en4.core.baseUrl + 'admin/headvancedmembers/index/verify',
            method: 'post',
            data: data,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $(element).set('html',responseHTML);
            }
        })).send();
    }
    function change_status_verified_selected(element){
        var id = $(element).get('rev');
        var data = {'user_ud':id};
        (new Request.HTML({
            secure: false,
            url: en4.core.baseUrl + 'admin/headvancedmembers/index/verify',
            method: 'post',
            data: data,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $(element).set('html',responseHTML);
            }
        })).send();
    }
    function view_verified_members(element){
        var bg = new Element('div',{
            'class':'bg_form_headvuser',
            'id': 'bg_form_headvuser',
            'style':'position:fixed; top:0px; left:0px; width: 100%; height: 100%; background-color:rgba(0,0,0,0.4);display:none;z-index:999;'
        });
       var contaner = new Element('div',{
            'class':'container_form_headvuser',
            'id': 'container_form_headvuser',
            'style':'position:absolute; top:100px; left:40%;padding: 20px; width: 400px; min-height: 200px; background-color:#fff;display:none;z-index:1000;'
        });

        var body_element = $$('body')[0];
        bg.inject(body_element);
        contaner.inject(body_element);
        bg.addEvent('click',function(){
            bg.setStyle('display','none');
            contaner.setStyle('display','none');
            contaner.set('html','');
        });
        contaner.setStyle('display','block');
        bg.setStyle('display','block');
        contaner.set('html',' <img class="irc_mi" style="" src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/loading.gif" width="16" height="16" title="loading "> Loading...');
        var id = $(element).get('rev');
        var data = {'user_ud':id,'format':'html'};
        (new Request.HTML({
            secure: false,
            url: en4.core.baseUrl + 'admin/headvancedmembers/index/verifymembers',
            method: 'post',
            data: data,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                contaner.set('html',responseHTML);
            }
        })).send();
    }

    <?php if( $this->openUser ): ?>
    window.addEvent('load', function() {
        $$('#multimodify_form .admin_table_options a').each(function(el) {
            if( -1 < el.get('href').indexOf('/edit/') ) {
                el.click();
                //el.fireEvent('click');
            }
        });
    });
    <?php endif ?>
</script>

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
    <div>
        <?php $count = $this->paginator->getTotalItemCount() ?>
        <?php echo $this->translate(array("%s member found", "%s members found", $count),
          $this->locale()->toNumber($count)) ?>
    </div>
    <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
            //'params' => $this->formValues,
        )); ?>
    </div>
</div>

<br />

<div class="admin_table_form">
    <form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
        <table class='admin_table'>
            <thead>
            <tr>
                <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
                <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
                <th style='width: 1%;'><?php echo $this->translate("Verified") ?></th>
                <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Supported by") ?></th>

            </tr>
            </thead>
            <tbody>
            <?php if( count($this->paginator) ): ?>
                <?php foreach( $this->paginator as $item ):
                    $user = $this->item('user', $item->user_id);
                    ?>
                    <tr>
                        <td><input <?php if ($item->level_id == 1) echo 'disabled';?> name='modify_<?php echo $item->getIdentity();?>' value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox'></td>
                        <td><?php echo $item->user_id ?></td>
                        <td class='admin_table_bold'>
                            <?php echo $this->htmlLink($user->getHref(),
                              $this->string()->truncate($user->getTitle(), 10),
                              array('target' => '_blank'))?>
                        </td>
                        <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
                        <td class='admin_table'>
                        <a href="javascript:void(0)" onclick="change_status_verified(this)" rev="<?php echo $item->user_id?>">
                                <?php if (Engine_Api::_()->headvancedmembers()->isActive($item)){ ?>
                <img class="irc_mi" style="margin-bottom: -5px;cursor: pointer;"
                     src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_verified.png"
                     width="24" height="24" title="verified">
            <?php } else { ?>
                      <img class="irc_mi" style="margin-bottom: -5px;cursor: pointer;" src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_not_verified.png" width="24" height="24" title="not verified">
                <?php
                                }?></a>
                        </td>
                        <td class="admin_table_centered nowrap">
                          <a href="javascript:void(0)" onclick="view_verified_members(this)"  rev="<?php echo $item->user_id?>"><?php echo Engine_Api::_()->headvancedmembers()->usersSupported($item)?> users</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class='buttons'>
            <button type='submit' name="submit_button" value="verify"><?php echo $this->translate("Verify Selected") ?></button>
        </div>
    </form>
</div>