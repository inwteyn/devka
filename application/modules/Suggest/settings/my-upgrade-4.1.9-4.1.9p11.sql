UPDATE `engine4_core_modules` SET `version` = '4.1.9p11'  WHERE `name` = 'suggest';
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` ,`module` ,`body` ,`is_request` ,`handler`) VALUES
('suggest_hecontest_photo',  'hecontest',  '{item:$subject} has suggested to you a {item:$object:hecontest photo}.',  '1',  'suggest.handler.request');