UPDATE `engine4_core_modules` SET `version` = '4.5.1' WHERE `name` = 'hecontest';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`)
VALUES ('suggest_hecontest_photo', 'hecontest', '{item:$subject} has suggested to you a {item:$object:hecontest photo}.', '1', 'suggest.handler.request', '1');