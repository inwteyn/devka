<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<?php

$check_modules = array(
  "friend" => "core",
  "comment" => "core",
  "login" => "core",
  "status" => "core",
  "photo" => "album",
  "blog" => "blog",
  "event" => "event",
  "group" => "group",
  "forum" => "forum",
  "classified" => "classified",
  "invite" => "invite",
  "referral" => "invite",
  "poll" => "poll",
  "pollpassed" => "poll",
  "music" => "music",
  "video" => "video",
  "checkin" => "checkin",
  "like" => "like",
  "likeme" => "like",
  "quiz" => "quiz",
  "quizpassed" => "quiz",
  "rate" => 'rate',
  "review" => "rate",
  "store" => "store",
  "storeorder" => "store",
  "suggest" => "suggest"
);

?>

<ul>
<?php foreach ($this->info as $key => $value):?>
  <?php
    if (!in_array($key,array("friend", "comment", "login", "status", "photo", "blog", "event", "group", "forum", "classified", "invite", "referral", "poll", "pollpassed", "music", "video", "checkin", "like", "likeme", "quiz", "quizpassed", "rate", "review", "store", "storeorder", "suggest"))){
      continue;
    }
    if (!empty($check_modules[$key]) && !Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($check_modules[$key])){
      continue ;
    }
  ?>
  <li>
    <ul>
      <li><?php echo $this->translate('HEBADGE_INFO_' . strtoupper($key));?></li>
      <li><?php echo $this->translate('HEBADGE_INFO_'.strtoupper($key).'_VALUE', $value)?></li>
    </ul>
  </li>
<?php endforeach;?>
</ul>

