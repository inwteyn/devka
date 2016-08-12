<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8758 2011-03-30 23:50:30Z john $
 * @author     John
 */
?>

<h3 class="sep">
  <span>
    <?php echo $this->translate('Plugin Usage Stats') ?>
  </span>
</h3>
<?php if($this->statistics){ ?>
  <table class='admin_home_stats'>
    <thead>
      <tr>
        <th colspan='3' align="left"><?php echo $this->translate('Plugin Information') ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo $this->translate('Version') ?></td>
        <td colspan='2'><?php echo $this->touchVersion ?></td>
      </tr>
    </tbody>
  </table>

  <table class='admin_home_stats'>
    <thead>
      <tr>
        <th align="left"><?php echo $this->translate('Statistics') ?></th>
        <th align="left"><?php echo $this->translate('Via Touch Mobile') ?></th>
        <th align="left"><?php echo $this->translate('Total') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->statistics as $statistic ): ?>
        <tr>
          <td>
            <?php echo $this->translate($statistic['label']) ?>
          </td>
          <td>
            <?php echo $this->locale()->toNumber($statistic['touch']) ?>
          </td>
          <td>
            <?php echo $this->locale()->toNumber($statistic['total']) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php } else {
  echo $this->content()->renderWidget('touch.admin-chart', array('chart_params'=>$this->chart_params));
} ?>
