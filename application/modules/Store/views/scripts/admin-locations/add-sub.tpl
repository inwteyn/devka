<style type="text/css">
    .add-wrapper {
        padding: 10px;
    }
    .add-wrapper #add-location {
        width: 950px;
    }
</style>

<div class="add-wrapper">
    <?php echo $this->render('application/modules/Store/views/scripts/admin-locations/_add_form.tpl'); ?>
    <div>
        <ul id="notice-messages" class="form-notices hidden">
            <li>

            </li>
        </ul>
        <ul id="error-messages" class="form-errors hidden">
            <li>

            </li>
        </ul>
    </div>
</div>
