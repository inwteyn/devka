<style type="text/css">
  .layout_store_admin_main_menu .tabs ul li.active a {
    padding: .5em .5em !important;
  }
  .tabs ul li ul li {
    display: block;
    padding: 10px;
  }

  .tabs ul li:hover ul {
    display: block;
  }

  .tabs ul li ul li:hover a {
    border: none;
    padding: 0;
  }

  .tabs ul li ul:hover a {
    border: none;
  }

  .tabs ul li ul {
    padding: 0;
    border: 1px solid #ccc;
    border-top: transparent;
    position: absolute;
    margin-top: 5px;
    display: none;
    background: #fff;
  }

  .tabs ul li:hover li ul {
    padding: 5px;
  }

  .tabs ul li:hover li a {
    border: none;
    color: #999;
    padding: 5px;
  }
  .tabs ul li:hover li:hover a {
    background: #eaeaea;
    color:#444;
  }
  .tabs ul li:hover li:hover {
    background: #eaeaea;
  }

<?php if($this->ordersCount): ?>
  .store_admin_main_orders:after {
    background-color: red;
    border-radius: 3px;
    color: #FFFFFF;
    content: "<?php echo $this->ordersCount; ?>";
    display: inline;
    font-size: 9px;
    padding: 2px;
    margin-left: 4px;
  }
<?php endif; ?>
  <?php if($this->rCount): ?>
  .store_admin_main_requests:after {
    background-color: red;
    border-radius: 3px;
    color: #FFFFFF;
    content: "<?php echo $this->rCount; ?>";
    display: inline;
    font-size: 9px;
    padding: 2px;
    margin-left: 4px;
  }
  <?php endif; ?>

  .tabs ul li a:hover {
    border-radius: 0;
  }

  .tabs ul li a.store_admin_main_add:hover {
    padding: 5px 7px 5px 7px !important;
    border: none !important;
    border-bottom: 1px solid #ccc;
    background: #269857;
  }

  .tabs ul li a.store_admin_main_add {
    color: #fff;
    background: #32BE6E;
    padding: 5px 7px 5px 7px !important;
  }
</style>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>