--
-- Update module Newsletter Updates
--

UPDATE `engine4_core_modules` SET `version` = '4.0.3'  WHERE `name` = 'updates';

DROP TABLE IF EXISTS `engine4_updates_actiontypes`;

CREATE TABLE IF NOT EXISTS `engine4_updates_campaigns` (
	`campaign_id` INT(11) NOT NULL AUTO_INCREMENT,
	`type` ENUM('instant','schedule') NOT NULL DEFAULT 'instant',
	`recievers` TEXT NOT NULL,
	`template_id` INT(11) NOT NULL,
	`creation_date` DATETIME NOT NULL,
	`sent` INT(11) NOT NULL DEFAULT '0',
	`viewed` INT(11) NOT NULL DEFAULT '0',
	`finished` TINYINT(4) NOT NULL DEFAULT '0',
	`planned_date` DATETIME NOT NULL,
	PRIMARY KEY (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `engine4_updates_content`;

CREATE TABLE IF NOT EXISTS `engine4_updates_content` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(20) NOT NULL,
	`type` varchar(20) NOT NULL default 'widget',
	`widget_id` INT(11) NULL DEFAULT NULL,
	`parent_id` INT(11) UNSIGNED NULL DEFAULT NULL,
	`order` INT(11) NOT NULL DEFAULT '1',
	`params` TEXT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `engine4_updates_widgets` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `title` varchar(20) default NULL,
  `module` varchar(20) NOT NULL default 'updates',
  `last_sent_id` int(11) NOT NULL default '0',
  `description` text,
  `params` text,
  `structure` varchar(20) default 'table',
  `blacklist` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `engine4_updates_templates` (
	`template_id` INT(11) NOT NULL AUTO_INCREMENT,
	`subject` VARCHAR(255) NOT NULL,
	`message` TEXT NOT NULL,
	`creation_date` DATETIME NOT NULL,
	`description` TEXT NULL,
	PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_updates_content` (`id`, `type`, `parent_id`, `order`, `widget_id`, `name`, `params`) VALUES
 (1, 'container', NULL, 1, NULL, 'top', NULL),
 (2, 'container', NULL, 2, NULL, 'main', NULL),
 (3, 'container', NULL, 3, NULL, 'bottom', NULL),
 (4, 'container', 1, 1, NULL, 'middle', NULL),
 (5, 'container', 2, 1, NULL, 'middle', NULL),
 (6, 'container', 2, 2, NULL, 'right', NULL),
 (7, 'container', 3, 1, NULL, 'middle', NULL),
 (8, 'widget', 4, 2, 19, 'featured_members', '{"title":"Featured Members","count":"10","name":"featured_members"}'),
 (9, 'widget', 5, 1, 1, 'htmlblock', '{"title":"HTML Block","html":"<div style=\\"padding: 10px;\\">\\r\\n<div style=\\"font-size: 13pt; font-weight: bold;\\">Hello [displayname],<\\/div>\\r\\n<br>\\r\\n<div style=\\"border: 1px solid #b2b2b2; padding: 10px; background: none repeat scroll 0% 0%  #0000sd;\\">\\r\\n<p>You have received an updates from Social Network<\\/p>\\r\\n<p>Best Regards, Social Network Administration.<\\/p>\\r\\n<a href=\\"http:\\/\\/upd\\/admin\\/updates\\/uplayout\\">test<\\/a><\\/div>\\r\\n<\\/div>","name":"htmlblock"}'),
 (10, 'widget', 5, 2, 4, 'new_actions', '{"title":"Latest Actions","count":"4"}'),
 (11, 'widget', 5, 3, 9, 'new_groups', '{"title":"New Groups","count":"2","name":"new_groups"}'),
 (12, 'widget', 5, 4, 7, 'new_classifieds', '{"title":"New Classifieds","count":"2","name":"new_classifieds"}'),
 (13, 'widget', 5, 5, 11, 'new_polls', '{"title":"New Polls","count":"2","name":"new_polls"}'),
 (14, 'widget', 5, 6, 5, 'new_albums', '{"title":"New Albums","count":"3","name":"new_albums"}'),
 (15, 'widget', 5, 7, 10, 'new_playlists', '{"title":"New Playlists","count":"3","name":"new_playlists"}'),
 (16, 'widget', 6, 1, 2, 'notifications', '{"title":"Your Notifications"}'),
 (17, 'widget', 6, 2, 8, 'new_events', '{"title":"New Events","count":"2","name":"new_events"}'),
 (18, 'widget', 6, 3, 6, 'new_blogs', '{"title":"New Blogs","count":"2","name":"new_blogs"}'),
 (19, 'widget', 6, 4, 13, 'new_forums', '{"title":"New Forums","count":"2","name":"new_forums"}'),
 (20, 'widget', 6, 5, 14, 'new_forum_topics', '{"title":"New Forum Topics","count":"2","name":"new_forum_topics"}'),
 (21, 'widget', 6, 6, 12, 'new_videos', '{"title":"New Videos","count":"3","name":"new_videos"}'),
 (22, 'widget', 7, 1, 3, 'new_members', '{"title":"New Memebers","count":"10"}'),
 (23, 'widget', 7, 2, 18, 'popular_members', '{"title":"Popular Members","count":"10"}');

INSERT IGNORE INTO `engine4_updates_widgets` (`id`, `name`, `title`, `module`, `last_sent_id`, `description`, `params`, `structure`, `blacklist`) VALUES
(1, 'htmlblock', 'HTML Block', 'updates', 0, 'HTML block which allows you to create own widget with any wished html code', '{"title":"HTML Block","html":""}', NULL, NULL),
(2, 'notifications', 'Your Notifications', 'updates', 0, 'Displays member''s notifications. Note: widget is not shown for subscribers(not registered members)', '{"title":"Your Notifications"}', NULL, NULL),
(3, 'new_members', 'New Memebers', 'user', 0, 'Displays recently signed up members', '{"title":"New Memebers","count":"6"}', 'list_icon', NULL),
(4, 'new_actions', 'Latest Actions', 'activity', 0, 'Displays your site recent activity. Please note it shows only Social Engine Core activity(new friendship, wall posts, etc)', '{"title":"Latest Actions","count":"4"}', 'table', NULL),
(5, 'new_albums', 'New Albums', 'album', 0, 'Displays recently uploaded photo albums', '{"title":"New Albums","count":"3"}', 'list_normal', NULL),
(6, 'new_blogs', 'New Blogs', 'blog', 0, 'Displays recently created blogs', '{"title":"New Blogs","count":"4"}', 'table', NULL),
(7, 'new_classifieds', 'New Classifieds', 'classified', 0, 'Displays recently posted classifieds', '{"title":"New Classifieds","count":"4"}', 'table', NULL),
(8, 'new_events', 'New Events', 'event', 0, 'Displays recently created events', '{"title":"New Events","count":"4"}', 'table', NULL),
(9, 'new_groups', 'New Groups', 'group', 0, 'Displays recently created groups', '{"title":"New Groups","count":"4"}', 'table', NULL),
(10, 'new_playlists', 'New Playlists', 'music', 0, 'Displays recently uploaded music playlists', '{"title":"New Playlists","count":"3"}', 'list_normal', NULL),
(11, 'new_polls', 'New Polls', 'poll', 0, 'Displays recently created polls', '{"title":"New Polls","count":"4"}', 'table', NULL),
(12, 'new_videos', 'New Videos', 'video', 0, 'Displays recently uploaded videos', '{"title":"New Videos","count":"3"}', 'list_normal', NULL),
(14, 'new_forum_topics', 'New Forum Topics', 'forum', 0, 'Displays recently posted forum topics', '{"title":"New Forum Topics","count":"4"}', 'table', NULL),
(13, 'new_forums', 'New Forums', 'forum', 0, 'Displays recently created forums', '{"title":"New Forums","count":"4"}', 'table', NULL),
(15, 'new_pages', 'New Pages', 'page', 0, 'Displays recently created pages. It requires Pages plugin from Hire-Experts.com which allows to create pages for businesses, brands, hotels, musicians, models, etc', '{"title":"New Pages","count":"4"}', 'table', NULL),
(16, 'new_quizzes', 'New Quizzes', 'quiz', 0, 'Displays recently created quizzes. It requires Quiz plugin from Hire-Experts', '{"title":"New Quizzes","count":"4"}', 'table', NULL),
(17, 'friend_suggests', 'People You May Know', 'inviter', 0, 'Displays peoples member may know based on mutual friendship. It requires Friends Inviter plugin from Hire-Experts.com which allows to import contacts from mailbox, social network, find people likely know,etc', '{"title":"People You May Know","count":"6"}', 'list_icon', NULL),
(18, 'popular_members', 'Popular Members', 'user', 0, 'Displays popular members', '{"title":"Popular Members","count":"6"}', 'list_icon', NULL),
(19, 'featured_members', 'Featured Members', 'hecore', 0, 'Displays featured members, please select featured members using free Hire-Experts Core plugin', '{"title":"Featured Members","count":"6"}', 'list_icon', NULL),
(20, 'new_questions', 'Latest Questions', 'question', 0, 'Displays recently posted questions. It requires Questions plugin from Webhive Team.', '{"title":"New Questions","count":"4"}', 'table', NULL),
(21, 'most_liked_pages', 'Most Liked Pages', 'like', 0, 'Displays most liked pages. It requires Pages and Likes plugins from Hire-Experts.com', '{"title":"Most Liked Pages","count":"4"}', 'table', NULL),
(22, 'most_liked_members', 'Most Liked Members', 'like', 0, 'Displays most liked members. It requires Likes plugin from Hire-Experts.com', '{"title":"Most Liked Members","count":"6"}', 'list_icon', NULL),
(23, 'new_articles', 'Latest Articles', 'article', 0, 'Displays recently created articles. It requires Articles plugin from Radcodes', '{"title":"Latest Articles","count":"4"}', 'table', '4'),
(24, 'featured_articles', 'Featured Articles', 'article', 0, 'Displays featured articles. It requires Articles plugin from Radcodes', '{"title":"Featured Articles","count":"4"}', 'table', NULL);

INSERT IGNORE INTO `engine4_updates_templates` (`subject`, `message`, `creation_date`, `description`) VALUES
('Marry Christmas Blue!!!', '<div style="background: #020013; width: 650px; pisition: relative;">\r\n<table style="width: 650px;" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td><a style="text-decoration: none;" href="[site_url]"> <img style="width: 650px;" title="Christmas Eve" src="[site_url]/application/modules/Updates/externals/images/templates/christmas_blue.png" border="0" alt=""></a></td>\r\n</tr>\r\n<tr>\r\n<td style="background: #0C3669; width: 100%; padding: 2px; font-size: 12px;">\r\n<div style="clear: both; width: 100%;">\r\n<div style="float: left; padding: 5px; color: #ffffff; font-weight: bold; margin-left: 25px;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[site_url]">Home</a></div>\r\n<div style="float: left; border-right: 1px solid #4AB0DB; padding: 5px 0px;">&nbsp;</div>\r\n<div style="float: left; padding: 5px; color: #ffffff; font-weight: bold;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[site_url]/members">Members</a></div>\r\n<div style="float: left; border-right: 1px solid #4AB0DB; padding: 5px 0px;">&nbsp;</div>\r\n<div style="float: left; padding: 5px;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[profile_url]">My Profile</a></div>\r\n</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="text-align: center; width: 650px;" align="center" valign="top">\r\n<div style="width: 600px; background: #ffffff; margin: 25px; text-align: left;">\r\n<table cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 200px; padding: 5px;" valign="top">\r\n<div style="color: #000000;"><span style="font-family: comic sans ms,sans-serif;">[new_members title=\'New Memebers\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="font-family: comic sans ms,sans-serif;">[featured_members title=\'Featured Members\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;">[popular_members title=\'Popular Members\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;">[new_videos title=\'New Videos\' count=\'3\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;"><br></span></div>\r\n</td>\r\n<td style="width: 400px; padding: 5px;" valign="top">\r\n<div>\r\n<div style="margin: 5px; padding: 5px; background: none repeat scroll 0% 0% #B8E4F7; border: 1px solid #020013;"><address><span style="color: #032d62; font-size: small;"><em><span style="font-family: comic sans ms,sans-serif;">Hi, [displayname]!</span></em></span></address> <address><span style="color: #032d62; font-size: small;"><em><span style="font-family: comic sans ms,sans-serif;">May Christ bless you with all the happiness and  success you deserve! Merry Xmas!</span></em></span></address></div>\r\n<div style="color: #000000;">[new_actions title=\'Latest Actions\' count=\'4\']</div>\r\n<div style="color: #000000;">[new_events title=\'New Events\' count=\'2\']</div>\r\n<div style="color: #000000;"><span style="color: #1b5213;">[new_albums title=\'New Albums\' count=\'4\']</span></div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="background: #0C3669; width: 100%;">\r\n<div style="clear: both; width: 100%;">\r\n<div style="float: left; padding: 5px; color: #ffffff; margin-left: 25px; font-size: 11px;">Message has been sent to [email].&nbsp;|&nbsp; <a style="font-size: 11px; text-decoration: none; color: #ffffff; font-weight: bold;" href="[unsubscribe_url]">Unsubscribe</a>&nbsp;|&nbsp; <a style="font-size: 11px; text-decoration: none; color: #ffffff; font-weight: bold;" href="[contact_url]">Contact</a></div>\r\n</div>\r\n<div style="clear: both; width: 100%; height: 2px;">&nbsp;</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '2010-12-24 14:22:36', NULL),
('Marry Christmas Green!!!', '<div style="background: #4BB419; width: 650px; pisition: relative; padding: 0px; margin: 0px;">\r\n<table style="width: 650px;" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td style="padding: 0px; margin: 0px;"><a style="text-decoration: none;" href="[site_url]"> <img style="width: 650px;" title="Christmas Eve" src="[site_url]/application/modules/Updates/externals/images/templates/christmas_green.png" border="0" alt=""></a></td>\r\n</tr>\r\n<tr>\r\n<td style="background: #2C6F0B; width: 100%; padding: 2px; font-size: 12px;">\r\n<div style="clear: both; width: 100%;">\r\n<div style="float: left; padding: 5px; color: #ffffff; font-weight: bold; margin-left: 25px;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[site_url]">Home</a></div>\r\n<div style="float: left; border-right: 1px solid #4BB419; padding: 5px 0px;">&nbsp;</div>\r\n<div style="float: left; padding: 5px; color: #ffffff; font-weight: bold;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[site_url]/members">Members</a></div>\r\n<div style="float: left; border-right: 1px solid #4BB419; padding: 5px 0px;">&nbsp;</div>\r\n<div style="float: left; padding: 5px;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[profile_url]">My Profile</a></div>\r\n</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="text-align: center; width: 650px;" align="center" valign="top">\r\n<div style="width: 600px; background: #ffffff; margin: 25px; text-align: left;">\r\n<table cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 200px; padding: 5px;" valign="top">\r\n<div style="color: #000000;"><span style="font-family: comic sans ms,sans-serif;">[new_members title=\'New Memebers\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="font-family: comic sans ms,sans-serif;">[featured_members title=\'Featured Members\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;">[popular_members title=\'Popular Members\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;">[new_videos title=\'New Videos\' count=\'3\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;"><br></span></div>\r\n</td>\r\n<td style="width: 400px; padding: 5px;" valign="top">\r\n<div>\r\n<div style="margin: 5px; padding: 5px; background: none repeat scroll 0% 0% #BFEDA9; border: 1px solid #1C4808;"><address><span style="color: #008000; font-size: small;"><em><span style="font-family: comic sans ms,sans-serif;">Hi, [displayname]!</span></em></span></address> <address><span style="color: #008000; font-size: small;"><em><span style="font-family: comic sans ms,sans-serif;">May Christ bless you with all the happiness and  success you deserve! Merry Xmas!</span></em></span></address></div>\r\n<div style="color: #000000;">[new_actions title=\'Latest Actions\' count=\'4\']</div>\r\n<div style="color: #000000;">[new_events title=\'New Events\' count=\'2\']</div>\r\n<div style="color: #000000;"><span style="color: #1b5213;">[new_albums title=\'New Albums\' count=\'4\']</span></div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="background: #2C6F0B; width: 100%;">\r\n<div style="clear: both; width: 100%;">\r\n<div style="float: left; padding: 5px; color: #ffffff; margin-left: 25px; font-size: 11px;">Message has been sent to [email].&nbsp;|&nbsp; <a style="font-size: 11px; text-decoration: none; color: #ffffff; font-weight: bold;" href="[unsubscribe_url]">Unsubscribe</a>&nbsp;|&nbsp; <a style="font-size: 11px; text-decoration: none; color: #ffffff; font-weight: bold;" href="[contact_url]">Contact</a></div>\r\n</div>\r\n<div style="clear: both; width: 100%; height: 2px;">&nbsp;</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '2010-12-24 14:12:55', NULL),
('Marry Christmas Red!!!', '<div style="background: #A91422; width: 650px; pisition: relative;">\r\n<table style="width: 650px;" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p><a style="text-decoration: none;" href="[site_url]"> <img style="width: 650px;" title="Christmas Eve" src="[site_url]/application/modules/Updates/externals/images/templates/christmas_red.png" border="0" alt=""></a></p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="background: #7A0213; width: 100%; padding: 2px; font-size: 12px;">\r\n<div style="clear: both; width: 100%;">\r\n<div style="float: left; padding: 5px; color: #ffffff; font-weight: bold; margin-left: 25px;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[site_url]">Home</a></div>\r\n<div style="float: left; border-right: 1px solid #D2001D; padding: 5px 0px;">&nbsp;</div>\r\n<div style="float: left; padding: 5px; color: #ffffff; font-weight: bold;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[site_url]/members">Members</a></div>\r\n<div style="float: left; border-right: 1px solid #D2001D; padding: 5px 0px;">&nbsp;</div>\r\n<div style="float: left; padding: 5px;"><a style="text-decoration: none; color: #ffffff; font-weight: bold;" href="[profile_url]">My Profile</a></div>\r\n</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="text-align: center; width: 650px;" align="center" valign="top">\r\n<div style="width: 600px; background: #ffffff; margin: 25px; text-align: left;">\r\n<table cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 200px; padding: 5px;" valign="top">\r\n<div style="color: #000000;"><span style="font-family: comic sans ms,sans-serif;">[new_members title=\'New Memebers\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="font-family: comic sans ms,sans-serif;">[featured_members title=\'Featured Members\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;">[popular_members title=\'Popular Members\' count=\'6\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;">[new_videos title=\'New Videos\' count=\'3\']</span></div>\r\n<div style="color: #000000;"><span style="color: #1b5213; font-family: comic sans ms,sans-serif;"><br></span></div>\r\n</td>\r\n<td style="width: 400px; padding: 5px;" valign="top">\r\n<div>\r\n<div style="margin: 5px; padding: 5px; background: none repeat scroll 0% 0% #fbe4e6; border: 1px solid #5a292e;"><address><span style="color: #800000; font-size: small;"><em><span style="font-family: comic sans ms,sans-serif;">Hi, [displayname]!</span></em></span></address> <address><span style="color: #800000; font-size: small;"><em><span style="font-family: comic sans ms,sans-serif;">May Christ bless you with all the happiness and  success you deserve! Merry Xmas!</span></em></span></address></div>\r\n<div style="color: #000000;">[new_actions title=\'Latest Actions\' count=\'4\']</div>\r\n<div style="color: #000000;">[new_events title=\'New Events\' count=\'2\']</div>\r\n<div style="color: #000000;"><span style="color: #1b5213;">[new_albums title=\'New Albums\' count=\'4\']</span></div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="background: #7A0213; width: 100%;">\r\n<div style="clear: both; width: 100%;">\r\n<div style="float: left; padding: 5px; color: #ffffff; margin-left: 25px; font-size: 11px;">Message has been sent to [email].&nbsp;|&nbsp; <a style="font-size: 11px; text-decoration: none; color: #ffffff; font-weight: bold;" href="[unsubscribe_url]">Unsubscribe</a>&nbsp;|&nbsp; <a style="font-size: 11px; text-decoration: none; color: #ffffff; font-weight: bold;" href="[contact_url]">Contact</a></div>\r\n</div>\r\n<div style="clear: both; width: 100%; height: 2px;">&nbsp;</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '2010-12-24 13:21:12', NULL);

DELETE FROM `engine4_core_mailtemplates` WHERE `type` = 'updates' LIMIT 1;
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('updates', 'updates', '[updates]'),
('campaign', 'updates', '[subject][message]');

DELETE FROM `engine4_core_menuitems` WHERE `module` = 'updates' LIMIT 8;
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('user_settings_updates', 'updates', 'Newsletter Updates', 'Updates_Plugin_Core', '{"route":"upadtes","module":"updates","controller":"settings"}', 'user_settings', '', 1, 0, 3),
('core_admin_main_plugins_updates', 'updates', 'Newsletter Updates', '', '{"route":"admin_default","module":"updates","controller":"index"}', 'core_admin_main_plugins', '', 1, 0, 999),
('updates_admin_main_general', 'updates', 'Dashboard', '', '{"route":"admin_default","module":"updates","controller":"index"}', 'updates_admin_main', '', 1, 0, 994),
('updates_admin_main_campaign', 'updates', 'Campaign Manager', NULL, '{"route":"admin_default","module":"updates","controller":"campaign"}', 'updates_admin_main', '', 1, 0, 995),
('updates_admin_main_layout', 'updates', 'Updates Manager', NULL, '{"route":"admin_default","module":"updates","controller":"layout"}', 'updates_admin_main', '', 1, 0, 996),
('updates_admin_main_subscriber', 'updates', 'Subscribers Manager', '', '{"route":"admin_default","module":"updates","controller":"subscribers"}', 'updates_admin_main', '', 1, 0, 997),
('updates_admin_main_stats', 'updates', 'View Stats', '', '{"uri":"javascript:void(0);this.blur();"}', 'updates_admin_main', '', 1, 0, 998),
('updates_admin_main_settings', 'updates', 'Settings', '', '{"uri":"javascript:void(0);this.blur();"}', 'updates_admin_main', '', 1, 0, 999);

DELETE FROM `engine4_core_tasks` WHERE `module` = 'updates' LIMIT 1;
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `category`, `module`,  `plugin`, `timeout`, `type`, `enabled`, `priority`) VALUES
('Background Newsletter Campaign Sender', 'system', 'updates', 'Updates_Plugin_Task_Campaign', 60, 'automatic', 1, 50),
('Background Newsletter Updates Sender', 'system', 'updates', 'Updates_Plugin_Task_Updates', 60, 'automatic', 1, 50);

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('updates.users.disabled', '0'),
('updates.subscribers.disabled', '0'),
('updates.perminut.itemnumber', '100');

ALTER IGNORE TABLE `engine4_updates_updates` CHANGE `update_id` `update_id` INT(11) AUTO_INCREMENT;

DELETE FROM `engine4_updates_links`;
ALTER IGNORE TABLE `engine4_updates_links` MODIFY `referred_date` DATETIME;
ALTER IGNORE TABLE `engine4_updates_links` ADD COLUMN `id` INT(11) NOT NULL;
ALTER IGNORE TABLE `engine4_updates_links` ADD COLUMN `module` VARCHAR(20) default NULL;
ALTER IGNORE TABLE `engine4_updates_links` ADD COLUMN `type` ENUM('updates','campaign') NOT NULL DEFAULT 'updates';

DROP TABLE IF EXISTS `engine4_updates_moduleupdates`;

ALTER IGNORE TABLE `engine4_updates_subscribers` MODIFY `datecreated` VARCHAR(50);
UPDATE `engine4_updates_subscribers` SET `datecreated` = FROM_UNIXTIME(`datecreated`);
ALTER IGNORE TABLE `engine4_updates_subscribers` CHANGE `datecreated` `creation_date` DATETIME;
ALTER IGNORE TABLE `engine4_updates_subscribers` ADD COLUMN `update_id` INT(11) NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `engine4_updates_subscribers` ADD COLUMN `campaign_id` INT(11) NOT NULL DEFAULT '0';

ALTER IGNORE TABLE `engine4_updates_updates` MODIFY `update_sent_date` VARCHAR(50);
UPDATE `engine4_updates_updates` SET `update_sent_date` = FROM_UNIXTIME(`update_sent_date`);
ALTER IGNORE TABLE `engine4_updates_updates` CHANGE `update_sent_date` `creation_date` DATETIME;
ALTER IGNORE TABLE `engine4_updates_updates` DROP COLUMN `referred`;
ALTER IGNORE TABLE `engine4_updates_updates` ADD COLUMN `message` TEXT NOT NULL;
ALTER IGNORE TABLE `engine4_updates_updates` ADD COLUMN `sending_finished` TINYINT(4) NOT NULL DEFAULT '0';

ALTER IGNORE TABLE `engine4_users` ADD COLUMN `updates_update_id` INT(11) NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `engine4_users` ADD COLUMN `updates_campaign_id` INT(11) NOT NULL DEFAULT '0';