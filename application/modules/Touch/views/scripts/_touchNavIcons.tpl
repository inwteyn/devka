<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _touchNavIcons.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<ul>
  <?php foreach( $this->container as $link ): ?>
    <li>
      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
        'style' => 'background-image: url('.$link->get('icon').');',
        'target' => $link->get('target'),
      )) ?>
    </li>
  <?php endforeach; ?>
</ul>