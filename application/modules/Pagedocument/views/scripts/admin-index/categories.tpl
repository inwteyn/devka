<h2><?php echo $this->translate("Page Document Plugin") ?></h2>

<script type="text/javascript">
    function changeOrder(category_id, direction) {
        new Request.JSON({
            'url': "<?php echo $this->url(array('module' => 'pagedocument', 'controller'=>'index', 'action'=>'order') ,'admin_default', true); ?>",
            'method': 'post',
            'data': {
                'category_id': category_id,
                'direction': direction,
                'format': 'json'
            },
            onSuccess: function (response) {
                if (response.html) {
                    var el = $("pagedocument-table-wrapper");
                    el.setHTML(response.html);

                }
            }
        }).send();
    }
</script>

<?php if (count($this->navigation)): ?>
    <div class='page_admin_tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
        ?>
    </div>
<?php endif; ?>

<?php echo $this->content()->renderWidget('page.admin-settings-menu', array('active_item' => 'page_admin_main_documents')); ?>

<?php echo $this->action("frame", "index", "hecore"); ?>

<h2><?php echo $this->translate("pagedocument_Form Categories Form Title") ?></h2>
<p class="description">
    <?php echo $this->translate('pagedocument_Form Categories Form Description'); ?>
</p>

<div class="settings admin_home_middle" style="clear: none">

    <div class="settings">

        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'pagedocument', 'controller' => 'index', 'action' => 'addcategory'), $this->translate('Add New Category'), array(
            'class' => 'smoothbox buttonlink',
            'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);'))
        ?>


        <?php if ($this->cats->getTotalItemCount() > 0): ?>
            <div class="pagedocument-table-wrapper">

                <table class="admin_table">
                    <thead>
                    <tr>
                        <th>Category name</th>
                        <th class="admin_table_centered">Owner</th>
                        <th class="admin_table_centered">Order</th>
                        <th class="admin_table_centered">Options</th>
                        <th class="admin_table_centered"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0; ?>
                    <?php foreach ($this->cats as $cat): $i++; ?>
                        <tr>
                            <td>
                                <?php echo $cat->category_name; ?>
                            </td>
                            <td class="admin_table_centered">
                                <?php echo $cat->user_id; ?>
                            </td>
                            <td class="admin_table_centered">
                                <?php echo $cat->order; ?>
                            </td>
                            <td class="admin_table_centered">
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'pagedocument', 'controller' => 'index', 'action' => 'editcategory', 'id' => $cat->category_id), $this->translate('pagedocument_edit'), array(
                                    'class' => 'smoothbox',))
                                ?>
                                |
                                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'pagedocument', 'controller' => 'index', 'action' => 'deletecategory', 'id' => $cat->category_id), $this->translate('pagedocument_delete'), array(
                                    'class' => 'smoothbox',))
                                ?>
                            </td>
                            <td>
                                <?php if ($i != 1) { ?>
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'pagedocument', 'controller' => 'index', 'action' => 'categories', 'id' => $cat->category_id, 'direction' => 1), '', array(
                                        'class' => 'pagedocument-category-up',)) ?>
                                <?php } ?>
                                <?php if ($i != $this->cats->getTotalItemCount()) { ?>
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'pagedocument', 'controller' => 'index', 'action' => 'categories', 'id' => $cat->category_id, 'direction' => 0), '', array(
                                        'class' => 'pagedocument-category-down',)) ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php echo $this->paginationControl($this->cats, null, null,
                array(
                    'pageAsQuery' => true,
                    'query' => $this->cats->getCurrentPageNumber()
                )
            );
            ?>
        <?php endif; ?>
    </div>
</div>