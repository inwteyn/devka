UPDATE `engine4_core_modules` SET `version` = '4.2.1'  WHERE `name` = 'offers';

ALTER TABLE `engine4_offers_offers`  CHANGE COLUMN `price_offer` `price_offer` DOUBLE(16,2) UNSIGNED NULL DEFAULT '0' AFTER `type`,  CHANGE COLUMN `price_item` `price_item` DOUBLE(16,2) UNSIGNED NULL DEFAULT NULL AFTER `price_offer`;