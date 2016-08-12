<script type="text/javascript">

    function confirmDelete(collection_id) {
        if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("HE-Emoticon_Are you sure you want to delete this collection?")) ?>')) {
            window.location.href = '<?php echo $this->url(array('action' => 'deletecollection'), 'heemoticon_admin_index', true); ?>/' + collection_id;
        } else {
            return false;
        }
    }

</script>

<h2>
    <?php echo $this->translate('HE-Emoticon Emoticon Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='admin_search'>
    <?php echo $this->filterForm->render($this); ?>
</div>
<br/>

<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<br/>

<div class='clear'>
    <div class='settings'>
        <form class="global_form">
            <div>
                <?php if (count($this->paginator)): ?>

                    <table class='admin_table collections-browse-table'>
                        <thead>

                        <tr>
                            <th></th>
                            <th><?php echo $this->translate("HE-Emoticon Collection Name") ?></th>
                            <th><?php echo $this->translate("HE-Emoticon Collection Description") ?></th>
                            <th style="text-align: center;"><?php echo $this->translate("HE-Emoticon Stickers Count") ?></th>
                            <th><?php echo $this->translate("HE-Emoticon Type") ?></th>
                            <th><?php echo $this->translate("Status") ?></th>
                            <th><?php echo $this->translate("Options") ?></th>
                        </tr>

                        </thead>
                        <tbody>
                        <?php foreach ($this->paginator as $collection): ?>

                            <tr>
                                <td class="admin-collection-icon"><img
                                        src="<?php echo $collection->getCollectionIconUrl(); ?>"></td>
                                <td class="collection-name"><?php echo $this->htmlLink(array('action' => 'editcollection', 'collection_id' => $collection->getIdentity()), $collection->name); ?></td>
                                <td class="collection-desc"><?php echo $this->string()->truncate($this->string()->stripTags($collection->description), 50) ?></td>
                                <td class="collection-stickers-count"><?php echo $collection->getStickersCount(); ?></td>
                                <?php $collectionTypeStatus = (!$collection->price == 0) && ($this->creditModuleStatus); ?>
                                <td class="collection-type-<?php echo ($collectionTypeStatus ? 'paid' : 'free'); ?>" > <?php echo ($collectionTypeStatus ? $this->translate("HE-Emoticon Paid") : $this->translate("HE-Emoticon Free")) ; ?> </td>
                                <td class="collection-status">
                                    <a class="status-collection-btn" href="javascript:void(0)" onclick="changeCollectionStatus(this)" identity="<?php echo $collection->getIdentity(); ?>" status="<?php echo $collection->status ? 1 : 0; ?>">
                                        <?php echo  $collection->status ? $this->translate('Disable') : $this->translate('Enable')?></a>
                                </td>
                                <td class='collection-options'>
                                    <?php echo $this->htmlLink(array('action' => 'editcollection', 'collection_id' => $collection->getIdentity()), '<i class="hei hei-pencil-square-o hei-lg"></i>', array(
                                        'class' => 'edit-collection-btn',
                                        'title' => $this->translate('Edit'))); ?>

                                    <?php echo $this->htmlLink(array('action' => 'export-collection', 'collection_id' => $collection->getIdentity()), '<i class="hei hei-upload hei-lg"></i>', array(
                                        'class' => 'export-collection-btn',
                                        'title' => $this->translate('Export'))); ?>

                                    <?php echo $this->htmlLink('javascript:void(0)', '<i class="hei hei-trash-o hei-lg"></i>', array('onClick' => "confirmDelete({$collection->getIdentity()})",
                                        'class' => 'delete-collection-btn',
                                        'title' => $this->translate('Delete')
                                    )) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>
                    </table>

                <?php else: ?>
                    <br/>
                    <div class="tip ">
                        <span><?php echo $this->translate("HE-Emoticon_There are currently no collections.") ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
    function changeCollectionStatus(el){
        var collection_id = el.get('identity');
        var collection_status = el.get('status');
        el.set('html','<?php echo $this->translate('Loading')?>');
        request = new Request.JSON({
            'format': 'json',
            'url': '<?php echo $this->url(array('action' => 'changestatus'), 'heemoticon_admin_stickers') ?>?cs=1',
            'data': {
                'format': 'json',
                'collection_id': collection_id,
                'collection_status': collection_status
            },
            'onSuccess': function (responseJSON) {
                el.set('status', responseJSON['new_status']);
                if( responseJSON['new_status'].toInt() == 0){
                    el.set('html','<?php echo $this->translate('Enable')?>');
                }else{
                    el.set('html','<?php echo $this->translate('Disable')?>');
                }
            }
        }).send();
    }
</script>