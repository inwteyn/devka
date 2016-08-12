<script type="text/javascript">

  window.addEvent('load', function (){

    <?php if ($this->tokenRow):?>

    window.opener.getElementById('soundclouddiv').setStyle('display','block');


    <?php endif;?>

    window.close();

  });

</script>