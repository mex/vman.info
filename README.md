vman.info
=============

Please be aware that this software has not been maintained for a longer period of time and have some quirks. There might be a problem with saving visitor stats, but the more pressing issue (with the original database) is the amount of queries run in `cron/players.php` as this is growing alongside the database.

## Install
Update database credentials in `lib/config.php`, `cron/players.php` and `cron/stats.php`.

Set hourly cron jobs for `cron/players.php` and `cron/stats.php`.

Insert database scheme:
```
CREATE TABLE `v_clubs` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `c_id` int(16) NOT NULL,
  `c_name` varchar(64) NOT NULL,
  `c_vifa` int(16) NOT NULL,
  `c_supporters` int(16) NOT NULL,
  `c_training_facility` tinyint(2) NOT NULL,
  `c_physio` tinyint(2) NOT NULL,
  `c_stadium` varchar(64) NOT NULL,
  `c_capacity` int(16) NOT NULL,
  `c_ticket_price` varchar(1) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `c_id` (`c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `v_cron` (
  `c_id` int(16) NOT NULL AUTO_INCREMENT,
  `c_file` varchar(255) NOT NULL,
  `c_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `v_players` (
  `id` bigint(32) NOT NULL AUTO_INCREMENT,
  `c_id` int(16) NOT NULL,
  `p_id` bigint(32) NOT NULL,
  `p_age` smallint(4) NOT NULL,
  `p_leg` varchar(10) NOT NULL,
  `p_country` smallint(4) NOT NULL,
  `p_height` smallint(4) NOT NULL,
  `p_weight` smallint(4) NOT NULL,
  `p_value` int(16) NOT NULL,
  `p_birthday` varchar(10) NOT NULL,
  `p_position` varchar(2) NOT NULL,
  `p_description` varchar(255) NOT NULL,
  `p_auction_bid` int(16) NOT NULL,
  `p_contract_expiry` int(16) NOT NULL,
  `p_wage` int(16) NOT NULL,
  `p_energy` smallint(4) NOT NULL,
  `p_finishing` smallint(4) NOT NULL,
  `p_dribling` smallint(4) NOT NULL,
  `p_passing` smallint(4) NOT NULL,
  `p_tackling` smallint(4) NOT NULL,
  `p_marking` smallint(4) NOT NULL,
  `p_penalty_taking` smallint(4) NOT NULL,
  `p_bravery` smallint(4) NOT NULL,
  `p_creativity` smallint(4) NOT NULL,
  `p_determination` smallint(4) NOT NULL,
  `p_influence` smallint(4) NOT NULL,
  `p_morale` smallint(4) NOT NULL,
  `p_off_the_ball` smallint(4) NOT NULL,
  `p_acceleration` smallint(4) NOT NULL,
  `p_balance` smallint(4) NOT NULL,
  `p_fitness` smallint(4) NOT NULL,
  `p_jump` smallint(4) NOT NULL,
  `p_strength` smallint(4) NOT NULL,
  `p_stamina` smallint(4) NOT NULL,
  `p_name` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `c_id` (`c_id`),
  KEY `p_id` (`p_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `v_players_first` (
  `id` bigint(32) NOT NULL AUTO_INCREMENT,
  `p_id` bigint(32) NOT NULL,
  `p_stats` mediumint(8) NOT NULL,
  `timestamp` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `p_id` (`p_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `v_settings` (
  `s_id` bigint(32) NOT NULL AUTO_INCREMENT,
  `s_cookie_id` bigint(32) NOT NULL,
  `s_future1` smallint(3) NOT NULL DEFAULT '25',
  `s_future2` smallint(3) NOT NULL DEFAULT '30',
  `s_sponsor` int(32) NOT NULL,
  `s_employees` int(32) NOT NULL,
  `s_stadium_degradation` decimal(5,3) NOT NULL DEFAULT '7.000',
  `s_stadium_average` int(32) NOT NULL,
  `s_notes` text NOT NULL,
  PRIMARY KEY (`s_id`),
  UNIQUE KEY `s_cookie_id` (`s_cookie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `v_stats` (
  `v_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `v_option` varchar(32) NOT NULL,
  `v_value` bigint(32) NOT NULL,
  PRIMARY KEY (`v_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `v_stats` (`v_id`, `v_option`, `v_value`)
VALUES
	(1, 'clubs processed', 0),
	(2, 'players processed', 0),
	(3, 'unique visitors', 0),
	(4, 'views', 0);

CREATE TABLE `v_views` (
  `v_id` bigint(32) NOT NULL AUTO_INCREMENT,
  `v_ip` varchar(15) NOT NULL,
  `v_url` varchar(128) NOT NULL,
  `v_user_agent` varchar(255) NOT NULL,
  `v_referer` varchar(255) NOT NULL,
  `v_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`v_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
```