<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<?php include 'application/modules/Updates/views/scripts/_submenus.tpl'; ?>
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


<style type="text/css">
table.admin_home_stats td:first-child {
    width:180px;
}

</style>

<h2><?php echo $this->translate('UPDATES_View Subscribers'); ?></h2>
<p>
  <?php echo $this->translate("UPDATES_VIEWS_SCRIPTS_ADMINSUBSCRIBERS_INDEX_DESCRIPTION") ?>
</p>
<br/>

<div class="updates_admin_home_right" style="width: 330px; float: right; margin-left: 20px; overflow: hidden;">
    <h3 class="sep">
      <span>
        <?php echo $this->translate('UPDATES_New subscriber') ?>
      </span>
    </h3>
		<div class='admin_search' style="clear:none;">
      <?php echo $this->formNewSubscriber->render($this) ?>
    </div>
</div>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

/*function multiModify()
{
  var subscribers_form = $('subscribers_form');
  if (subscribers_form.submit_button.value == 'delete')
  {
    return confirm('<?php //echo $this->string()->escapeJavascript($this->translate("UPDATES_Are you sure you want to delete the selected user accounts?")) ?>');
  }
}*/

function selectAll()
{
  var i;
  var subscribers_form = $('subscribers_form');
  var inputs = subscribers_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function deleteSelected()
{
  var i;
  var j = -1;
  var subscribers_form = $('subscribers_form');
  var inputs = subscribers_form.elements;
  var $subscribers = new Array();
  for (i = 1; i < inputs.length - 1; i++) {
    if (inputs[i].checked) {
      j++;
      $subscribers[j] = inputs[i].getProperty('value');
    }
  }

  if ($subscribers.length > 0) {
    var url = '<?php echo $this->url(array("module"=>"updates", "controller"=>"subscribers", "action"=>"delete-selected"), 'admin_default', true)?>';
    var urlObject = new URI(url);
    urlObject.setData({'subscribers' : $subscribers });
    Smoothbox.open(urlObject.toString());
  }
}
</script>

<?php $subscribersCount = $this->paginator->getTotalItemCount() ?>
<?php if ($subscribersCount > 0): ?>
<div class="updates_admin_home_left">
    <h3 class="sep">
        <span><?php echo $this->translate('UPDATES_Filter subscribers'); ?></span>
    </h3>
    <div class='admin_search' style="clear:none;">
      <?php echo $this->formFilter->render($this) ?>
    </div>
    
    <br />
</div>
<?php endif;?>
<br />
<div class="updates_admin_home_middle" style="overflow: hidden;">
  <form id='subscribers_form' method="post" action="">
    <div class='admin_results'>
		<div>
	    	<?php echo $this->translate(array("UPDATES_ %s subscriber found", "%s subscribers found", $subscribersCount), ($subscribersCount)) ?>
	  	</div>
	  	<div>
	    	<?php echo $this->paginationControl($this->paginator); ?>
	  	</div>
    </div>

    <br />
		<?php if ($subscribersCount > 0): ?>
    <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('subscriber_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('name', 'ASC');"><?php echo $this->translate("UPDATES_Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('email_address', 'ASC');"><?php echo $this->translate("UPDATES_Email"); ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate("UPDATES_Subscribed Date"); ?></a></th>
        <th style='width: 10%;'><?php echo $this->translate("UPDATES_Options"); ?></th>
      </tr>
    </thead>
	  <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><input name='subscriber_<?php echo $item->subscriber_id; ?>' value='<?php echo $item->subscriber_id; ?>' type='checkbox' class='checkbox'></td>
            <td><?php echo $item->subscriber_id ?></td>
            <td class='admin_table_bold'><?php echo $item->name; ?></td>
            <td>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email_address ?>'><?php echo $item->email_address ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td><?php echo $item->creation_date ?></td>
            <td>
            	<a class='smoothbox' href='<?php echo $this->url(array('action' => 'edit', 'id' => $item->subscriber_id));?>'>
                <?php echo $this->translate("edit") ?>
              </a>
                |
              <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $item->subscriber_id));?>'>
                <?php echo $this->translate("UPDATES_delete") ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
    </table>
    <?php endif; ?>
    <br />

    <div class='buttons'>
      <button type='button' name="delete_selected" onclick="deleteSelected()" ><?php echo $this->translate("Delete Selected") ?></button>
    </div>
  </form>
</div>
