<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 vadim $
 * @author     Vadim
 */
?>

<?php
  $this->headLink()->appendStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Usernotes/externals/styles/main.css');
  $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Usernotes/externals/scripts/core.js');
  $this->langvars = Zend_Json::encode(array(
    'your_note_saved'=>Zend_Registry::get('Zend_Translate')->_('Your note saved!')
  ));
?>


<script type="text/javascript">
en4.core.runonce.add('domready', function() {

  he_usernotes.construct (
    0, 0, 0, '', 'edit', <?php echo $this->urls_js; ?>, <?php echo $this->langvars; ?>
  );

});

</script>



<h2><?php echo $this->translate('Welcome Usernotes Plugin'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p><?php echo $this->translate('Browse your site users and leave notes on wished users.'); ?></p>

<br />

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
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $memberCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s member found", "%s members found", $memberCount), ($memberCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />

<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 150px;'><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th style='width: 90px;'><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 130px;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
        <th style='width: 90px;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a></th>
        <th class='admin_table_centered'><?php echo $this->translate('Note'); ?></th>
        <th class='admin_table_centered'><?php echo $this->translate('Options'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td style="white-space:normal;"><?php echo $item->user_id ?></td>
            <td style="white-space:normal;" class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->getTitle(), array('target' => '_blank')) ?></td>
            <td style="white-space:normal;" class='admin_table_bold'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td style="white-space:normal;">
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td class='admin_table_centered'>
              <a href='<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $item->level_id)) ?>'>
                <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
              </a>
            </td>
            <td style="white-space:normal;" class='admin_table_centered'>
                <?php echo $item->note ?>
            </td>
            <td class='admin_table_centered'>
                <?php if ( $item->note ) { ?>
                    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'save', 'usernote_id' => $item->usernote_id));?>'><?php echo $this->translate('Edit'); ?></a> |
                    <a href="javascript://" onclick="he_usernotes.usernote_id = <?php echo $item->usernote_id; ?>; Smoothbox.open($('delete_note_confrim'), {mode: 'Inline', width: 350, height: 100});"><?php echo $this->translate('Delete'); ?></a>
                <?php } else { ?>
                    <a class='smoothbox' href='<?php echo $this->url(array('action' => 'save', 'user_id' => $item->user_id));?>'><?php echo $this->translate('Leave Note'); ?></a>
                <?php } ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</form>


<div style="display: none;">
  <div id="delete_note_confrim">
    <div class="title" style="font-weight: bold; font-size: 11pt; margin-bottom: 10px;"><?php echo $this->translate('Delete Note'); ?></div>
    <div>
      <?php echo $this->translate('Are you sure you want to delete this note?'); ?><br /><br />
    </div>
    <div align="center">
      <button type="button" onclick="he_usernotes.delete_admin();"><?php echo $this->translate('Delete'); ?></button>
    <button type="button" onclick="Smoothbox.close()"><?php echo $this->translate('Cancel'); ?></button>
    </div>
  </div>
</div>