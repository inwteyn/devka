
<style type="text/css">
  .btn {
    display: inline-block;
    *display: inline;
    padding: 4px 12px;
    margin-bottom: 0;
    *margin-left: .3em;
    line-height: 20px;
    color: #333333;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    vertical-align: middle;
    cursor: pointer;
    background-color: #f5f5f5;
    *background-color: #e6e6e6;
    background-repeat: repeat-x;
    border: 1px solid #cccccc;
    *border: 0;
    border-color: #e6e6e6 #e6e6e6 #bfbfbf;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
    border-bottom-color: #b3b3b3;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    *zoom: 1;
    -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
    -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  }

  .btn:hover,
  .btn:focus,
  .btn:active,
  .btn.active,
  .btn.disabled,
  .btn[disabled] {
    color: #333333;
    background-color: #e6e6e6;
    *background-color: #d9d9d9;
  }

  .btn:active,
  .btn.active {
    background-color: #cccccc  \9;
  }

  .btn:first-child {
    *margin-left: 0;
  }

  .btn:hover,
  .btn:focus {
    color: #333333;
    text-decoration: none;
  }

  .btn:focus {
    outline: thin dotted #333;
    outline: 5px auto -webkit-focus-ring-color;
    outline-offset: -2px;
  }

  .btn.active,
  .btn:active {
    background-image: none;
    outline: 0;
    -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
    -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05);
  }

  .btn.disabled,
  .btn[disabled] {
    cursor: default;
    background-image: none;
    opacity: 0.65;
    filter: alpha(opacity=65);
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
  }

  .optimizer_structure {
  }
  .optimizer_structure {
    width: 500px;
  }
  .optimizer_structure > li {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #CCCCCC;
    margin: 5px 0;
    padding: 5px;
  }
  .optimizer_structure > li > ul {
    margin: 5px 0;
  }
  .optimizer_structure > li > ul > li {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #CCCCCC;
    margin: 3px 0 3px 10px;
    padding: 3px;
  }
  .optimizer_structure .title {
    background-color: #F8F8F8;
    display: block;
    padding: 5px 10px;
  }



</style>

<h2>
  <?php echo $this->translate('Install Optimizer');?>
</h2>
<p><?php echo $this->translate('For correct installing of plugin need replace some files of SE. You can do it below (current files will saved and you can revert their)');?></p>


<?php if ($this->old_timeline):?>
  <ul class="form-errors">
    <li><?php echo $this->translate('You use old version of Timeline plugin. Please update the plugin to last version for correct work');?></li>
  </ul>
<?php endif;?>

<br />

<div>

  <?php echo $this->translate('Status');?>:

  <?php if (!$this->installed):?>

    <span style="color:red;margin-right: 10px;"><?php echo $this->translate('Not Installed');?></span>

    <a href="<?php echo $this->url(array('action' => 'manager', 'replace' => 1)); ?>" class="smoothbox btn">
      <?php echo $this->translate('Install');?>
    </a>

  <?php else:?>

    <span style="color:green;margin-right: 10px;"><?php echo $this->translate('Installed');?></span>

    <a href="<?php echo $this->url(array('action' => 'manager', 'revert' => 1)); ?>" class="smoothbox btn">
      <?php echo $this->translate('Revert files before installing');?>
    </a>

  <?php endif;?>
</div>

<br />

<?php if ($this->saved):?>
  <ul class="form-notices"><li><?php echo $this->translate('Changes has been saved!');?></li></ul>
<?php endif;?>

<form action="<?php echo $this->url(); ?>" method="post">

  <h2><?php echo $this->translate('Ajaxable widget');?></h2>
  <p>
    <?php echo $this->translate('Allows to increase loading speed and faster displays content to user by way asynchronous loading of widgets');?>
  </p>

  <br/>

  <?php echo $this->formCheckbox('ajax_enabled', null, array(
    'checked' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('optimizer.ajax.enabled', 1),
    'id' => 'ajax_enabled'
  ));?>
  <label for="ajax_enabled"><?php echo $this->translate('Asynchronous Loading of widgets');?></label>

  <br/><br/>


  <script type="text/javascript">
    function selectAllPage(page_id){
      $('page_'+page_id).getElements('input').set('checked', true);
    }
    function selectAllPages(){
      $$('.optimizer_structure')[0].getElements('input').set('checked', true);
    }
  </script>

  <a href="javascript:void(0);" onclick="selectAllPages();"><?php echo $this->translate('Select all widgets');?></a>

  <ul class="optimizer_structure">
  <?php foreach ($this->structure as $page_id => $widgets):?>
    <?php
      if (empty($this->pages[$page_id])){
        continue ;
      }
      $page = $this->pages[$page_id];
    ?>

    <li id="page_<?php echo $page_id;?>">
      <span class="title">
        <a href="<?php echo $this->baseUrl();?>/admin/content?page_id=<?php echo $page_id;?>" target="_blank">
          <?php echo $page->displayname;?>
        </a>
        (
        <a href="javascript:void(0);" onclick="selectAllPage(<?php echo $page_id;?>);"><?php echo $this->translate('Select all');?></a>
        )
      </span>
      <ul>
        <?php foreach ($widgets as $widget):?>
          <li>
            <input
                id="widget_<?php echo $widget->content_id?>"
                type="checkbox"
                name="widgets[]"
                value="<?php echo $widget->content_id;?>"
                <?php if (!empty($widget->params) && $widget->params['ajaxPostLoading']):?>
                  checked="checked"
                <?php endif;?>
            />
            <label for="widget_<?php echo $widget->content_id?>">
              <?php echo $widget->name;?>
            </label>
          </li>
        <?php endforeach;?>
      </ul>
    </li>

  <?php endforeach;?>
  </ul>


  <h2><?php echo $this->translate('Minify');?></h2>
  <p>
    <?php echo $this->translate('Reduces size of js files and their count');?>
  </p>

  <br/>

  <?php echo $this->formCheckbox('minify_enabled', null, array(
    'checked' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('optimizer.minify.enabled', 1),
    'id' => 'minify_enabled'
  ));?>
  <label for="minify_enabled"><?php echo $this->translate('Minify enabled');?></label>

  <br/>
  <br/>

  <button type="submit"><?php echo $this->translate('Save changes');?></button>

</form>