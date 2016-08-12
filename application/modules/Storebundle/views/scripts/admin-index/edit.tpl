<script>
    var selectedProducts = JSON.parse('<?php echo $this->selectedProducts; ?>');
    StorebundleCore.initEditFormProducts(selectedProducts);
</script>
<?php echo $this->form->render($this); ?>