<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _touchNavIcons.tpl 2011-09-26 11:18:13 Ulan $
 * @author     Ulan
 */

?>

<ul class="touch_sub_navigation">
  <?php foreach( $this->container as $link ): ?>
    <li>
      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
        'class' => 'sub_nav_item' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
        'style' => 'background-image: url('.$link->get('icon').');',
        'target' => $link->get('target'),
      )) ?>
    </li>
  <?php endforeach; ?>
</ul>