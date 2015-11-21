<?php
$history = array(
    '2.2.0.0' => array(
        'UPDATE `'.fsConfig::GetInstance('db_prefix').'controller_settings` SET `value` = "2.2.0.0" WHERE `controller` = "Panel" AND `name` = "version";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'user_fields` ADD `expression` VARCHAR(255);',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'user_fields` ADD `type` VARCHAR(25);',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'user_fields` ADD `position` TINYINT NOT NULL DEFAULT "0";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'user_fields` ADD `duty` ENUM("0", "1") NOT NULL DEFAULT "0";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'user_fields` ADD `special_type` INT DEFAULT NULL;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'admin_menu` CHANGE `order` `position` TINYINT(4) NOT NULL DEFAULT "0";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'menu_items` CHANGE `order` `position` TINYINT(4) NOT NULL DEFAULT "0";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts` CHANGE `order` `position` SMALLINT(6) NOT NULL DEFAULT "0";',
        'CREATE TABLE IF NOT EXISTS `'.fsConfig::GetInstance('db_prefix').'languages` (`id` smallint(6) NOT NULL, `name` varchar(6) NOT NULL, `active` enum("0","1") NOT NULL DEFAULT "0", PRIMARY KEY (`id`), UNIQUE KEY `name` (`name`)) ENGINE=MyISAM DEFAULT CHARSET='.fsConfig::GetInstance('db_codepage').';',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'languages` (`id`, `name`) VALUES (1, "ru", 1), (2, "en", 1), (3, "ua", 0);',
        'CREATE TABLE IF NOT EXISTS `'.fsConfig::GetInstance('db_prefix').'pages_info` (`id_page` int(11) NOT NULL, `id_language` tinyint(4) NOT NULL, `title` varchar(255) NOT NULL, `alt` varchar(255) NOT NULL, `html` text NOT NULL, `keywords` varchar(500) NOT NULL, `description` varchar(500) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET='.fsConfig::GetInstance('db_codepage').';',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'pages_info` ADD UNIQUE( `id_page`, `id_language`);',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'pages_info` (`id_page`, `id_language`, `title`, `alt`, `html`, `keywords`, `description`) SELECT `id`, 1, `title`, `alt`, `html`, `keywords`, `description` FROM `'.fsConfig::GetInstance('db_prefix').'pages`;',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'pages_info` (`id_page`, `id_language`, `title`, `alt`, `html`, `keywords`, `description`) SELECT `id`, 2, `title`, `alt`, `html`, `keywords`, `description` FROM `'.fsConfig::GetInstance('db_prefix').'pages`;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'menu_items` ADD `target` ENUM("_self","_blank","_top","_parent") NOT NULL DEFAULT "_self";',
        'CREATE TABLE IF NOT EXISTS `'.fsConfig::GetInstance('db_prefix').'posts_category_info` (`id_category` int(11) NOT NULL, `id_language` int(11) NOT NULL, `title` varchar(255) NOT NULL,`alt` varchar(255) NOT NULL, `meta_description` varchar(500) NOT NULL, `meta_keywords` varchar(500) NOT NULL, UNIQUE KEY `id_category` (`id_category`,`id_language`)) ENGINE=MyISAM DEFAULT CHARSET='.fsConfig::GetInstance('db_codepage').';',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'posts_category_info` (`id_category`, `id_language`, `title`, `alt`, `meta_keywords`, `meta_description`) SELECT `id`, 1, `title`, `alt`, `meta_keywords`, `meta_description` FROM `'.fsConfig::GetInstance('db_prefix').'posts_category`;',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'posts_category_info` (`id_category`, `id_language`, `title`, `alt`, `meta_keywords`, `meta_description`) SELECT `id`, 2, `title`, `alt`, `meta_keywords`, `meta_description` FROM `'.fsConfig::GetInstance('db_prefix').'posts_category`;',
        'CREATE TABLE IF NOT EXISTS `'.fsConfig::GetInstance('db_prefix').'posts_info`(`id_post` int(11) NOT NULL, `id_language` int(11) NOT NULL, `title` varchar(100) NOT NULL, `alt` varchar(100) NOT NULL, `html_short` text NOT NULL, `html_full` text NOT NULL, `meta_description` varchar(500) NOT NULL, `meta_keywords` varchar(500) NOT NULL, UNIQUE KEY `id_post` (`id_post`,`id_language`)) ENGINE=MyISAM DEFAULT CHARSET='.fsConfig::GetInstance('db_codepage').';',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'posts_info` (`id_post`, `id_language`, `title`, `alt`, `html_short`, `html_full`, `meta_keywords`, `meta_description`) SELECT `id`, 1, `title`, `alt`, `html_short`, `html_full`, `meta_keywords`, `meta_description` FROM `'.fsConfig::GetInstance('db_prefix').'posts`;',
        'INSERT INTO `'.fsConfig::GetInstance('db_prefix').'posts_info` (`id_post`, `id_language`, `title`, `alt`, `html_short`, `html_full`, `meta_keywords`, `meta_description`) SELECT `id`, 2, `title`, `alt`, `html_short`, `html_full`, `meta_keywords`, `meta_description` FROM `'.fsConfig::GetInstance('db_prefix').'posts`;',
        'UPDATE `'.fsConfig::GetInstance('db_prefix').'search` SET `table_name` = "posts_info" WHERE `table_name` = "posts"',
        'UPDATE `'.fsConfig::GetInstance('db_prefix').'search` SET `table_name` = "pages_info" WHERE `table_name` = "pages"',
        //IMPORTANT!!! USE SHOULD EXECUTE THIS QUERIES BUT AFTER YOU CHECKED THAT ALL DATA WAS TRANSFERED INTO NEW TABLES
        /*
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts_category` DROP `name`, DROP `alt`, DROP `meta_description`, DROP `meta_keywords`;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'pages` DROP `title`, DROP `alt`, DROP `html`, DROP `description`, DROP `keywords`;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts` DROP `title`, DROP `alt`, DROP `html_short`, DROP `html_full`, DROP `meta_description`, DROP `meta_keywords`;',
        */
    ),
    '2.2.1.0' => array(
        'UPDATE `'.fsConfig::GetInstance('db_prefix').'controller_settings` SET `value` = "2.2.1.0" WHERE `controller` = "Panel" AND `name` = "version";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts` ADD `image` TEXT NULL AFTER `position`;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts_category` ADD `image` VARCHAR(255) NOT NULL AFTER `id_parent`;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'users` ADD `date_last_auth` TIMESTAMP NOT NULL;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'users` ADD `auth_count` INT NOT NULL DEFAULT "0";',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts_category_info` ADD `description` TEXT NOT NULL;',
    ),
    '2.2.2.0' => array(
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'posts` ADD `date_modify` BIGINT NOT NULL AFTER `date`;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'pages` ADD `date_modify` BIGINT NOT NULL;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'pages` ADD `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;',
        'ALTER TABLE `'.fsConfig::GetInstance('db_prefix').'menu_items` ADD `target` varchar(10) NOT NULL DEFAULT "_self";',
    ),
);                                                                 