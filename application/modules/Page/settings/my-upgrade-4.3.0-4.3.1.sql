UPDATE `engine4_core_modules` SET `version` = '4.3.0p1'  WHERE `name` = 'page';
UPDATE `engine4_core_menuitems` SET `plugin`='Hecore_Plugin_Menus::mainMenuActivate' WHERE  `name`='core_main_page';