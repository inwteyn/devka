<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _contentNavIcons.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<ul class="page_content_navigation">
  <?php foreach( $this->container as $link ): ?>
    <li>
      <?php echo $this->htmlLink('javascript:void(0)', $this->translate($link->getLabel()), array(
        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
        'style' => 'background-image: url('.$link->get('icon').');',
				'onClick' => $link->onClick
      )) ?>
    </li>
  <?php endforeach; ?>
</ul>