
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('register', 'Register', 'Register', '4.2.1', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_register', 'register', 'Register', '', '{"route":"register_url","module":"register","controller":"index","action":"index"}', 'core_main', '', 99);