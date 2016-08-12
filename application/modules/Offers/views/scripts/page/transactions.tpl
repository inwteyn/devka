<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: transactions.tpl  25.09.12 18:41 TeaJay $
 * @author     Taalay
 */
?>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline offers">
  <h2><?php echo $this->translate('Manage');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<div class="layout_middle" style="clear: none;">
  <p>
    <?php echo $this->translate("PAYMENT_VIEWS_ADMIN_INDEX_INDEX_DESCRIPTION") ?>
  </p>

  <br />


  <?php if( !empty($this->error) ): ?>
    <ul class="form-errors">
      <li>
        <?php echo $this->error ?>
      </li>
    </ul>

    <br />
  <?php return; endif; ?>


  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <div>
      <?php echo $this->formFilter->render($this) ?>
    </div>

    <br />
  <?php endif; ?>
</div>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->filterValues['order'] ?>';
  var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<div class="layout_middle">
  <div class='offers-list-result'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query' => $this->filterValues,
        'pageAsQuery' => true,
      )); ?>
    </div>
  </div>

  <br />


  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <table class='table offers-list'>
      <thead>
        <tr>
          <th style='width: 1%;' class="table_short">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
              <?php echo $this->translate("ID") ?>
            </a>
          </th>
          <th style='width: 1%;' class="table_short">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
              <?php echo $this->translate("Member") ?>
            </a>
          </th>
          <th style='width: 1%;' class='table_short'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'ASC');">
              <?php echo $this->translate("Gateway") ?>
            </a>
          </th>
          <th style='width: 1%;' class='table_short'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('offer_id', 'ASC');">
              <?php echo $this->translate("Offer") ?>
            </a>
          </th>
          <th style='width: 1%;' class='table_short'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');">
              <?php echo $this->translate("State") ?>
            </a>
          </th>
          <th style='width: 1%;' class='table_short'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');">
              <?php echo $this->translate("Amount") ?>
            </a>
          </th>
          <th style='width: 1%;' class='table_short'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
              <?php echo $this->translate("Date") ?>
            </a>
          </th>
          <th style='width: 1%;' class='table_short'>
            <?php echo $this->translate("Options") ?>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach( $this->paginator as $item ):
          $user = @$this->users[$item->user_id];
          $order = @$this->orders[$item->order_id];
          $offer = @$this->offers[$item->offer_id];
          $gateway = @$this->gateways[$item->gateway_id];
          ?>
          <tr>
            <td><?php echo $item->transaction_id ?></td>
            <td>
              <?php echo ( $user ? $user->__toString() : '<i>' . $this->translate('Deleted or Unknown Member') . '</i>' ) ?>
            </td>
            <td>
              <?php echo ($gateway ? $gateway->title : $this->translate('Credits')) ?>
            </td>
            <td>
              <?php echo $this->htmlLink($offer->getHref(), $offer->getTitle(), array()); ?>
            </td>
            <td>
              <?php echo $this->translate(ucfirst($item->state)) ?>
            </td>
            <td>
              <?php if ($item->gateway_id) : ?>
                <?php echo @$this->locale()->toCurrency($item->amount, $item->currency) ?>
                <?php echo $this->translate('(%s)', $item->currency) ?>
              <?php else : ?>
                <?php echo Engine_Api::_()->offers()->getCredits($item->amount); ?>
                <?php echo $this->translate('(%s)', $this->translate('Credits')) ?>
              <?php endif; ?>
            </td>
            <td>
              <?php echo $this->locale()->toDateTime($item->timestamp) ?>
            </td>
            <td>
              <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $item->transaction_id));?>'>
                <?php echo $this->translate("details") ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>