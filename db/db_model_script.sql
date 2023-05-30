-- Exported from QuickDBD: https://www.quickdatabasediagrams.com/
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


-- Modify this code to update the DB schema diagram.
-- To reset the sample schema, replace everything with
-- two dots ('..' - without quotes).
CREATE TABLE `user` (
                        `user_id` int  NOT NULL AUTO_INCREMENT ,
                        `name` varchar(200) COLLATE utf8_czech_ci NOT NULL ,
                         `role` varchar(20) COLLATE utf8_czech_ci NOT NULL,
                         `active` tinyint(1) NOT NULL,
                        PRIMARY KEY (
                                     `user_id`
                            )
);

CREATE TABLE `plant` (
                         `plant_id` int  NOT NULL AUTO_INCREMENT,
                         `name` varchar(200) COLLATE utf8_czech_ci NOT NULL ,
                         `latin_name` varchar(200) COLLATE utf8_czech_ci NULL ,
                         `description` text COLLATE utf8_czech_ci  NULL ,
                         `owner` int  NOT NULL ,
                         `bought_date` datetime  NULL ,
                         `water_frequency` int  NULL ,
                         `temperature` int  NULL ,
                         `lighting` enum('direct','indirect')  NULL ,
                         `origin` enum('Europe','Asia','South America','North America','Australia')  NULL ,
                         `humidity` boolean  NULL ,
                         `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          `image` longblob null,                       
                          `image_type` nvarchar(30) null
                         PRIMARY KEY (
                                      `plant_id`
                             )
);

CREATE TABLE `category` (
                            `category_id` int  NOT NULL AUTO_INCREMENT ,
                            `name` varchar(200) COLLATE utf8_czech_ci  NOT NULL ,
                            PRIMARY KEY (
                                         `category_id`
                                )
);

INSERT INTO `category` (`category_id`, `name`) VALUES
(1, 'Pro začátečníky'),
(2, 'Pro pokročilé'),
(3, 'K mazlíčkům'),
(4, 'Čističky vzduchu'),
(5, 'Moje květinky'),
(6, 'Oblíbené květiny');

CREATE TABLE `plant_category_map` (
                                      `plant_category_map_id` int  NOT NULL AUTO_INCREMENT,
                                      `plant_id` int  NOT NULL ,
                                      `category_id` int  NOT NULL ,
                                      PRIMARY KEY (
                                                   `plant_category_map_id`
                                          )
);

CREATE TABLE `plant_user_like_map` (
                                       `plant_user_like_map_id` int  NOT NULL AUTO_INCREMENT,
                                       `plant_id` int  NOT NULL ,
                                       `user_id` int  NOT NULL ,
                                       PRIMARY KEY (
                                                    `plant_user_like_map_id`
                                           )
);

ALTER TABLE `plant` ADD CONSTRAINT `fk_plant_owner` FOREIGN KEY(`owner`)
    REFERENCES `user` (`user_id`);

ALTER TABLE `plant_category_map` ADD CONSTRAINT `fk_plant_category_map_plant_id` FOREIGN KEY(`plant_id`)
    REFERENCES `plant` (`plant_id`);

ALTER TABLE `plant_category_map` ADD CONSTRAINT `fk_plant_category_map_category_id` FOREIGN KEY(`category_id`)
    REFERENCES `category` (`category_id`);

ALTER TABLE `plant_user_like_map` ADD CONSTRAINT `fk_plant_user_like_map_plant_id` FOREIGN KEY(`plant_id`)
    REFERENCES `plant` (`plant_id`);

ALTER TABLE `plant_user_like_map` ADD CONSTRAINT `fk_plant_user_like_map_user_id` FOREIGN KEY(`user_id`)
    REFERENCES `user` (`user_id`);

CREATE INDEX `idx_user_name`
    ON `user` (`name`);

CREATE INDEX `idx_plant_name`
    ON `plant` (`name`);

CREATE TABLE IF NOT EXISTS `resources` (
  `role` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `resource` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `action` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`role`,`resource`,`action`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka obsahující přehled oprávnění přístupu ke zdrojům';

INSERT INTO `resources` (`role`, `resource`, `action`) VALUES
('admin', 'category', ''),
('admin', 'user', ''),
('editor', 'plant', ''),
('editor', 'comment', ''),
('guest', 'plant', 'list'),
('guest', 'plant', 'show'),
('guest', 'homepage', ''),
('guest', 'user', 'login'),
('guest', 'user', 'register'),
('registered', 'comment', 'new'),
('registered', 'user', 'logout');

-- --------------------------------------------------------

--
-- Struktura tabulky `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `parent_id` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_role` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='Tabulka obsahující přehled podporovaných rolí';

--
-- Vypisuji data pro tabulku `roles`
--

INSERT INTO `roles` (`id`, `parent_id`) VALUES
('guest', NULL),
('admin', 'registered'),
('registered', 'guest'),

ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Omezení pro tabulku `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `parentRole` FOREIGN KEY (`parent_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Omezení pro tabulku `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `userRule` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
