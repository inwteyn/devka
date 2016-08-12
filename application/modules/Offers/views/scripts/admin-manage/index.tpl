<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-06-06 17:01 ratbek $
 * @author     Ratbek
 */
?>

<?php
  $this->headTranslate(array(
    'OFFERS_Are you sure you want to delete the selected offers?',
    'OFFERS_Are you sure you want to delete this offer?',
    'OFFERS_Are you sure you want to disable this particular offer?'
  ));
?>

<script type="text/javascript">
  function multiDelete()
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("OFFERS_Are you sure you want to delete the selected offers?")) ?>');
  }

  function selectAll()
  {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
  }

  function confirmDelete(offer_id)
  {
    if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("OFFERS_Are you sure you want to delete this offer?")) ?>')){
      // if demoadmin
      var user_id = '<?php echo $this->user_id; ?>';
      if (user_id != 1250) {
        window.location.href = '<?php echo $this->url(array('action' => 'delete'), 'offer_admin_manage', true); ?>/'+offer_id;
      }
    } else {
      return false;
    }
  }

  function disableOffer(offer_id, isEnabled)
  {
    var message = '';
    if (isEnabled) {
      message = '<?php echo $this->string()->escapeJavascript($this->translate("OFFERS_Are you sure you want to disable this particular offer?")); ?>';
    }
    else {
      message = '<?php echo $this->string()->escapeJavascript($this->translate("OFFERS_Are you sure you want to enable this particular offer?")); ?>';
    }

    if (confirm(message)) {
      window.location.href = '<?php echo $this->url(array('action' => 'disable'), 'offer_admin_manage', true); ?>/offer_id/'+offer_id+'/isEnabled/'+isEnabled;
    }else{
      return false;
    }
  }

  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';

  var changeOrder = function(order, default_direction){
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

</script>

<h2><?php echo $this->translate("Offers Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render();
  ?>
</div>
<?php endif; ?>

<p>
  <?php echo $this->translate("OFFERS_ADMIN_MANAGE_DESCRIPTION") ?>
</p>
<br />
<div class="offers_admin_manage_menu">
  <span class="offers_admin_manage_menu_items active_item"><?php echo $this->translate('OFFERS_View Offers'); ?></span>
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'create'), 'offer_admin_manage', true);?>"><?php echo $this->translate('OFFERS_Create Offer');?></a>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->filterForm->render($this); ?>
</div>
<br />

<div class='admin_results'>
  <div>
    <?php $offersCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s offer found", "%s offers found", $offersCount), ($offersCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
  </div>
</div>
<br />

<?php if( count($this->paginator) ): ?>
  <div id="admin_offers_list">
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'delete-all'), 'offer_admin_manage', true);?>" onSubmit="return multiDelete();">
      <table class='admin_table'>
        <thead>
        <tr>
          <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
          <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('offer_id', 'DESC');"><?php echo $this->translate('ID')?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("OFFERS_Title") ?></a></th>
          <th><?php echo $this->translate("OFFERS_Owner") ?></th>
          <th><?php echo $this->translate("OFFERS_Type") ?></th>
          <th><?php echo $this->translate("OFFERS_Discount") ?></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('coupons_count', 'ASC');"><?php echo $this->translate("OFFERS_Coupons Count") ?></a></th>
          <th><a href="javascript:void(0);" onclick="javascript:changeOrder('category_id', 'ASC');"><?php echo $this->translate("OFFERS_Category") ?></a></th>
          <th class="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');"><?php echo $this->translate("OFFERS_Creation_date") ?></a></th>
          <th class="center"><?php echo $this->translate("OFFERS_Options") ?></th>
        </tr>
        </thead>
        <tbody>
          <?php foreach($this->paginator as $item):	 ?>
        <tr>
          <td><input type='checkbox' name='delete[]' class='checkbox' value="<?php echo $item->offer_id ?>"/></td>
          <td><?php echo $item->offer_id ?></td>
          <td><?php echo $this->htmlLink($item->getHref(), ($item->getTitle() ? $item->getTitle() : "<i>".$this->translate("OFFERS_Untitled")."</i>" )); ?></td>
          <td><?php echo $this->htmlLink($this->user($item->owner_id)->getHref(), $this->user($item->owner_id)->getTitle()); ?></td>
          <td><?php echo $item->type ?></td>
          <td><?php echo $this->getOfferDiscount($item); ?></td>
          <td><?php echo ($item->coupons_unlimit) ? $this->translate('OFFERS_Unlimit') : $item->coupons_count; ?></td>
          <td><?php echo ($item->category_id != 1 ? $item->category_title : ("<i>".$this->translate("Uncategorized")."</i>")); ?></td>
          <td class="center"><?php echo $item->creation_date ?></td>
          <td class='admin_table_options'>
            <?php $linkName = '';
            $isEnabled = false;
            if($item->enabled == 1) {
              $linkName = 'disable';
              $isEnabled = 1;
            }
            else {
              $linkName = 'enable';
              $isEnabled = 0;
            }
            ?>
            <?php echo $this->htmlLink(array('action' => 'change-status-feature', 'offer_id' => $item->getIdentity()), '', array(
              'class' => 'buttonlink',
              'style' => 'background: url(application/modules/Offers/externals/images/featured'.$item->featured.'.png) no-repeat; margin-right: 5px;',
              'title' => ($item->featured) ? $this->translate('OFFERS_set_usual') : $this->translate('OFFERS_set_featured')));
            ?>
            <?php echo $this->htmlLink('javascript:void(0)', '', array(
              'onClick' => "disableOffer({$item->offer_id}, {$isEnabled})",
              'class' => 'buttonlink',
              'style' => 'background: url(application/modules/Offers/externals/images/'. ($item->isEnable() ? 'enable' : 'disable').'.png) no-repeat; margin-right: 5px;',
              'title' => ($item->isEnable()) ? $this->translate('OFFERS_Disable') : $this->translate('OFFERS_Enable'))) ?>

            <?php echo $this->htmlLink(array('route' => 'offers_specific', 'action' => 'edit', 'offer_id' => $item->offer_id), '', array(
            'class' => 'buttonlink',
            'style' => 'background: url(application/modules/Offers/externals/images/pencil.png) no-repeat; margin-right: 5px;',
            'title' => $this->translate('Edit'))); ?>

            <?php echo $this->htmlLink('javascript:void(0)', '', array('onClick' => "confirmDelete({$item->offer_id})",
              'class' => 'buttonlink',
              'style' => 'background: url(application/modules/Offers/externals/images/delete.png) no-repeat; margin-right: 0px;',
              'title' => $this->translate('OFFERS_Delete')
            )) ?>
          </td>
        </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <br />

      <div class='buttons'>
        <button type='submit'><?php echo $this->translate("OFFERS_Delete_selected") ?></button>
      </div>
    </form>
<?php endif; ?>