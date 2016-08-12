<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>
<h2><?php echo $this->htmlLink($this->subject->getHref(), $this->truncate($this->subject->getTitle(), 30))?>
  <?php if ($this->verified){ ?>
        <img class="irc_mi" style="margin-bottom: -3px;cursor: pointer;" src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_verified.png" width="24" height="24" title="verified">
  <?php } ?></h2>