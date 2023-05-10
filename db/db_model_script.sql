-- Exported from QuickDBD: https://www.quickdatabasediagrams.com/
-- NOTE! If you have used non-SQL datatypes in your design, you will have to change these here.


-- Modify this code to update the DB schema diagram.
-- To reset the sample schema, replace everything with
-- two dots ('..' - without quotes).
CREATE TABLE `user` (
                        `user_id` int  NOT NULL ,
                        `name` varchar(200)  NOT NULL ,
                        PRIMARY KEY (
                                     `user_id`
                            )
);

CREATE TABLE `plant` (
                         `plant_id` int  NOT NULL ,
                         `name` varchar(200)  NOT NULL ,
                         `latin_name` varchar(200)  NULL ,
                         `description` text  NULL ,
                         `owner` int  NOT NULL ,
                         `bought_date` datetime  NULL ,
                         `water_frequency` int  NULL ,
                         `temperature` int  NULL ,
                         `lighting` enum('direct','indirect')  NULL ,
                         `origin` enum('Europe','Asia','South America','North America','Australia')  NULL ,
                         `humidity` boolean  NULL ,
                         PRIMARY KEY (
                                      `plant_id`
                             )
);

CREATE TABLE `category` (
                            `category_id` int  NOT NULL ,
                            `name` varchar(200)  NOT NULL ,
                            PRIMARY KEY (
                                         `category_id`
                                )
);

CREATE TABLE `plant_category_map` (
                                      `plant_category_map_id` int  NOT NULL ,
                                      `plant_id` int  NOT NULL ,
                                      `category_id` int  NOT NULL ,
                                      PRIMARY KEY (
                                                   `plant_category_map_id`
                                          )
);

CREATE TABLE `plant_user_like_map` (
                                       `plant_user_like_map_id` int  NOT NULL ,
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

