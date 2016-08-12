<select id="store_user_currency" name="store_user_currency" onchange="changeUserCurrency(this);">
    <?php foreach($this->currencies as $currency): ?>
        <option <?php if($this->usercurrency == $currency->currency) echo 'selected="selected"'; ?> value="<?php echo $currency->currency; ?>">
            <?php echo $currency->name; ?>
        </option>
    <?php endforeach; ?>
</select>

<script type="text/javascript">
    function changeUserCurrency(el) {
        request = new Request.JSON({
            'format': 'json',
            'url': '<?php echo $this->url(array('controller' => 'index','action' => 'change-user-currency'), 'store_extended') ?>',
            'data': {'currency': el.value},
            'onSuccess': function () {
                document.location.reload(true);
            }
        }).send();
    }
</script>