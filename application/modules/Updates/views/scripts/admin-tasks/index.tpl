<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: updates.tpl 2012-03-09 12:01 ratbek $
 * @author     Ratbek
 */
?>

<link type="text/css" rel="stylesheet" href="application/modules/Updates/externals/styles/main.css"/>
<?php include 'application/modules/Updates/views/scripts/_submenus.tpl'; ?>

<script type="text/javascript">
  var tasks_paginator = new paginator(<?php echo Zend_Json::encode($this->paginator_pages)?>);

  var cancelTask = function(task_id)  {
    var tasks_page = $('tasks_page').get('html');
    var request = new Request.JSON({
      url : '<?php echo $this->url(array("module" => "updates", "controller" => "tasks", "action" => "cancel"), "admin_default", true) ?>',
      method : 'post',
      data : {'format':'json', 'task_id':task_id, 'tasks_page': tasks_page},
      'onSuccess': function(response) {
        if (response.html){
          $('tasks_paginator_items').getChildren('table').destroy();
          $('tasks_paginator_items').set('html',response.html);
        }
      }
    }).send();
  }

  var restartTask = function(task_id)  {
    var tasks_page = $('tasks_page').get('html');
    var request = new Request.JSON({
      url : '<?php echo $this->url(array("module" => "updates", "controller" => "tasks", "action" => "restart"), "admin_default", true) ?>',
      method : 'post',
      data : {'format':'json', 'task_id':task_id, 'tasks_page': tasks_page},
      'onSuccess': function(response) {
        if (response.html){
          $('tasks_paginator_items').getChildren('table').destroy();
          $('tasks_paginator_items').set('html',response.html);
        }
      }
    }).send();
  }

  var openConfirmDelete = function(task_id) {
    var url = '<?php echo $this->url(array("module"=>"updates", "controller"=>"tasks", "action"=>"delete"), 'admin_default', true)?>';
    var urlObject = new URI(url);
    urlObject.setData({'task_id' : task_id });

    Smoothbox.open(urlObject.toString());
  }

  en4.core.runonce.add(function()
  {
    var isEnabledAutoRefresh = '<?php echo $this->isEnabledAutoRefresh ?>';
    var seconds = 60;
    if (isEnabledAutoRefresh == '1') {
      var autoRefresh = (function() {
        $('seconds').set('html', seconds--);
        if (seconds < 0) {
          seconds = 60;
          window.location.reload();
        }
      }).periodical(1000);
    }
  });
  
  var enableAutoRefresh = function(isEnable) {
    if(isEnable == 1) {
      var request = new Request.JSON({
        url : '<?php echo $this->url(array("module" => "updates", "controller" => "tasks", "action" => "enable-auto-refresh"), "admin_default", true) ?>',
        method : 'post',
        data : {'format':'json', 'isEnable':isEnable},
        'onSuccess': function() {
          window.location.reload();
        }
      }).send();
    }
    else {
      var request = new Request.JSON({
        url : '<?php echo $this->url(array("module" => "updates", "controller" => "tasks", "action" => "enable-auto-refresh"), "admin_default", true) ?>',
        method : 'post',
        data : {'format':'json', 'isEnable':isEnable},
        'onSuccess': function() {
          window.location.reload();
        }
      }).send();
    }
  }

function selectAll()
{
  var checkboxElements = $$('.checkbox');
  for (var i=1; i < checkboxElements.length; i++) {
    checkboxElements[i].checked = checkboxElements[0].checked;
  }
}

function deleteSelected()
{
  var i;
  var j = -1;
  var checkboxElements = $$('.checkbox');
  var $tasks = new Array();
  for (i = 1; i < checkboxElements.length; i++) {
    if (checkboxElements[i].checked) {
      j++;
      $tasks[j] = checkboxElements[i].getProperty('value');
    }
  }

  if ($tasks.length > 0) {
    var url = '<?php echo $this->url(array("module"=>"updates", "controller"=>"tasks", "action"=>"delete-selected"), 'admin_default', true)?>';
    var urlObject = new URI(url);
    urlObject.setData({'tasks' : $tasks });
    Smoothbox.open(urlObject.toString());
  }
}
</script>

<div>
<h2><?php echo $this->translate("UPDATES_Newsletter Updates Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
</div>
<div class="tasks_container">
  <h3 id="tasks_title"><?php echo $this->translate("UPDATES_Tasks Title"); ?></h3>
  <div id="tasks_description">
    <?php echo $this->translate("UPDATES_Tasks Description"); ?>
  </div>
  <?php if ($this->paginator->count() > 0): ?>
    <div id="tasks_auto_refresh">
      <?php if ($this->isEnabledAutoRefresh == 1): ?>
        Tasks can be run again in <span id="seconds">60</span> seconds. <a href="javascript:void(0);" onclick="enableAutoRefresh(0)">Disable Auto-Refresh</a>
      <?php else: ?>
        Tasks are ready to be run again. <a href="javascript:void(0);" onclick="enableAutoRefresh(1)">Enable Auto-Refresh</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div id="tasks_paginator_container">
    <?php if ($this->paginator->count() == 0): ?>
      <?php echo $this->translate('UPDATES_No task has been created yet.'); ?>
    <?php endif;?>

    <?php if ($this->paginator->count() > 0): ?>
      <div>
        <?php echo $this->paginationControl($this->paginator, null, array('_pagination.tpl', 'updates'), array('paginator_name'=>'tasks_paginator')); ?>
        <span id="tasks_page" style="display: none;">1</span>
      </div>

      <div id="tasks_paginator_items">
        <?php echo $this->ajaxPaginator($this->paginator, 'tasks_paginator'); ?>
      </div>
      <div class='buttons'>
        <button type='button' name="delete_selected" onclick="deleteSelected()" style="margin-top: 12px"><?php echo $this->translate("Delete Selected") ?></button>
      </div>
    <?php endif; ?>
  </div>
</div>