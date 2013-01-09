DROP TABLE IF EXISTS `#__maps`;
CREATE TABLE `#__maps` (
	`maps_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`maps_alias` VARCHAR(96) DEFAULT NULL,
	`maps_name` VARCHAR(96) DEFAULT NULL,
	`maps_description` TEXT DEFAULT NULL,
	`attribs` TEXT DEFAULT NULL,
	`meta_description` TEXT DEFAULT NULL,
	`meta_keywords` TEXT DEFAULT NULL,
	`ordering` INT(11) UNSIGNED DEFAULT NULL,
	`published` tinyINT(1) UNSIGNED DEFAULT 0,
	`checked_out` INT(11) UNSIGNED DEFAULT 0,
	`checked_out_time` INT(11) UNSIGNED DEFAULT 0,
	`access` TINYINT(3) DEFAULT 0,
	`user_id` INT(11) UNSIGNED DEFAULT 0,
	PRIMARY KEY (`maps_id`),
	KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__maps_marker`;
CREATE TABLE `#__maps_marker` (
	`marker_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`marker_name` VARCHAR(64) DEFAULT NULL,
	`marker_alias` VARCHAR(64) DEFAULT NULL,
	`marker_lat` FLOAT DEFAULT 0,
	`marker_lng` FLOAT DEFAULT 0,
	`marker_description` TEXT DEFAULT NULL,
	`attribs` TEXT DEFAULT NULL,
	`ordering` INT(11) UNSIGNED DEFAULT NULL,
	`published` tinyINT(1) UNSIGNED DEFAULT 0,
	`checked_out` INT(11) UNSIGNED DEFAULT 0,
	`checked_out_time` INT(11) UNSIGNED DEFAULT 0,
	`access` TINYINT(3) DEFAULT 0,
	`maps_id` INT(11) UNSIGNED DEFAULT NULL,
	PRIMARY KEY (`marker_id`),
	KEY `maps_id` (`maps_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;