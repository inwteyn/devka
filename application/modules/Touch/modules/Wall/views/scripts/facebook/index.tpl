
<script type="text/javascript">

  window.addEvent('load', function (){

    <?php if ($this->tokenRow):?>

      window.opener.Wall.services.get('<?php echo $this->tokenRow->provider?>').setServiceOptions(<?php echo $this->jsonInline(array_merge($this->tokenRow->publicArray(), array('enabled' => $this->tokenRow->check())))?>);
      window.opener.Wall.applyAll(function (item){
        item.fireEvent('<?php echo $this->task?>', ['<?php echo $this->tokenRow->provider?>']);
      });

    <?php endif;?>

    window.close();

  });

</script>