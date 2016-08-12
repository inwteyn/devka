<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<div class="headline">
    <?php if (count($this->navigation)): ?>
        <div class='tabs'>
            <?php
                echo $this->navigation()->menu()->setContainer($this->navigation)->render()
            ?>
        </div>
    <?php endif; ?>
</div>