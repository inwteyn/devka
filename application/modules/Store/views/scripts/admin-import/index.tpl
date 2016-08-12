<h2><?php echo $this->translate("Store Plugin") ?></h2>
<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active'=>$this->activeMenu)); ?>

<h2 class="hed">
    <span class="export"><?php echo $this->translate("STORE_export");?></span> / <span class="import"><?php echo $this->translate("STORE_import");?></span>
</h2>

<form id="form_exp_inp" method="post" action="admin/store/import/export">



<div class="block_check_all">
    <span class="check_all">
        <?php echo $this->translate("STORE_check");?>
        <input type="checkbox" id="all_select">
    </span>
    <span class="export_btn"><input id="submit_form" type="submit" value="Export"></span>
</div>


<div class="store_product">
    <ul>
        <?php foreach( $this->products as $product ): ?>
            <li>
                <div class="block_check">
                    <div class='product_name'>
                        <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 23, '...')) ?>
                    </div>
                    <div class="img_check">
                        <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.icon'), array('class' => 'product_thumb')) ?>
                    </div>

                    <div class='product_info'>
                        <div class='product_date'>
                            <?php echo $this->getPrice($product); ?>
                        </div>
                    </div>
                    <div class="check"><input type="checkbox"  name="product_id_<?php echo $product['product_id'];?>" data-id="<?php echo $product['product_id'];?>" value="<?php echo $product['product_id'];?>" class="check_inp"></div>
                </div>

            </li>
        <?php endforeach; ?>
    </ul>
</div>
<input type="hidden" name="ids" id="ids" value="">
</form>

<form id="form_inp" method="post" action="admin/store/import/import" enctype="multipart/form-data">
    <select name="category" style="width: 250px;">
        <?php

        echo "<option value='0'>".$this->translate("STORE_admin_stor")."</option>";

            foreach($this->category as $cat_item){
                echo "<option value='".$cat_item['page_id']."'>".$cat_item['name']."</option>";
            }
        ?>
    </select>
        <br><br>
    <input id="upload" type="file" name="load" value="Upload">
    <span class="export_btn"><input id="submit_form_inp" type="submit" value="Import"></span>
</form>


<script type="text/javascript">


    window.addEvent('domready', function() {
        $$('#form_inp').hide();

      $$('.export').addClass('activ_bt');

    });


    $$('.export').addEvent('click',function(){
        $$('.export').addClass('activ_bt');
        $$('.import').removeClass('activ_bt');

        $$('#form_exp_inp').show();
        $$('#form_inp').hide();
    });

    $$('.import').addEvent('click',function(){

        $$('.import').addClass('activ_bt');
        $$('.export').removeClass('activ_bt');

        $$('#form_inp').show();
        $$('#form_exp_inp').hide();
    });


    $$('.check_all').addEvent('click',function(){
        if ($$('#all_select').get('checked')[0]==false) {
            $$('#all_select').set('checked', 'checked');
            $$('.check_inp').set('checked', 'checked');
        } else {
            $$('#all_select').set('checked', '');
            $$('.check_inp').set('checked', '');
        }
    });

    $$('#form_exp_inp').addEvent('submit', function(e){
        e.stop();
        var ids = $$('.check_inp:checked').get('data-id');
        $$('#ids').set('value',ids);
        $('form_exp_inp').submit();
    });

</script>




