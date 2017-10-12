
DROP TABLE IF EXISTS `#__hb_gym`;
CREATE TABLE `#__hb_gym` (
  `gymId` int(6) NOT NULL DEFAULT '0',
  `shortName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gymName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` int(6) DEFAULT NULL,
  `town` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `districtId` int(6) DEFAULT NULL,
  `district` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `clearanceAssociation` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `clearanceDistrict` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adhesive` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastChange` datetime DEFAULT NULL,
  PRIMARY KEY (`gymId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_team`;
CREATE TABLE `zhkog_hb_team` (
  `teamId` int(11) NOT NULL,
  `teamkey` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(3) DEFAULT NULL,
  `team` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortName` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `leagueKey` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `league` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `leagueIdHvw` int(10) DEFAULT NULL,
  `sex` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `youth` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `update` datetime DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
  PRIMARY KEY (`teamId`),
  UNIQUE(`teamkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_team_details`;
CREATE TABLE `#__hb_team_details` (
  `teamkey` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `standingsGraph` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`teamkey`,`season`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_team_picture`;
CREATE TABLE `#__hb_team_picture` (
  `picId` int(10) NOT NULL AUTO_INCREMENT,
  `season` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teamkey` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `caption_dt1` longtext COLLATE utf8_unicode_ci,
  `caption_dd1` longtext COLLATE utf8_unicode_ci,
  `caption_dt2` longtext COLLATE utf8_unicode_ci,
  `caption_dd2` longtext COLLATE utf8_unicode_ci,
  `caption_dt3` longtext COLLATE utf8_unicode_ci,
  `caption_dd3` longtext COLLATE utf8_unicode_ci,
  `caption_dt4` longtext COLLATE utf8_unicode_ci,
  `caption_dd4` longtext COLLATE utf8_unicode_ci,
  `comment` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`picId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_game`;
CREATE TABLE `#__hb_game` (
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `teamkey` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `leagueKey` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gameIdHvw` int(7) NOT NULL,
  `gymId` int(6) DEFAULT NULL,
  `dateTime` datetime DEFAULT NULL,
  `home` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `away` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `goalsHome` int(3) DEFAULT NULL,
  `goalsAway` int(3) DEFAULT NULL,
  `goalsHome1` int(3) DEFAULT NULL,
  `goalsAway1` int(3) DEFAULT NULL,
  `comment` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pointsHome` int(1) DEFAULT NULL,
  `pointsAway` int(1) DEFAULT NULL,
  `ownClub` tinyint(1) DEFAULT NULL,
  `reportHvwId` int(10) DEFAULT NULL,
  PRIMARY KEY (`season`, `gameIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_gamereport`;
CREATE TABLE `#__hb_gamereport` (
  `reportID` int(6) NOT NULL AUTO_INCREMENT,
  `gameIdHvw` int(6) DEFAULT NULL,
  `report` longtext COLLATE utf8_unicode_ci,
  `playerList` longtext COLLATE utf8_unicode_ci,
  `extra` longtext COLLATE utf8_unicode_ci,
  `halftime` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trend` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`reportID`),
  UNIQUE KEY `gameIdHvw` (`gameIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_pregame`;
CREATE TABLE `#__hb_pregame` (
  `pregameID` int(6) NOT NULL AUTO_INCREMENT,
  `gameIdHvw` int(6) DEFAULT NULL,
  `pregame` longtext COLLATE utf8_unicode_ci,
  `meetupLoc` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meetupTime` time DEFAULT NULL,
  PRIMARY KEY (`pregameID`),
  UNIQUE KEY `gameIdHvw` (`gameIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_division`;
CREATE TABLE `#__hb_division` (
  `division` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `divisionName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `youth` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `season` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teamStandings` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teamSchedule` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `division` (`division`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_standings`;
CREATE TABLE `#__hb_standings` (
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `teamkey` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rank` tinyint(2) DEFAULT NULL,
  `team` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `games` tinyint(2) DEFAULT NULL,
  `wins` tinyint(2) DEFAULT NULL,
  `ties` tinyint(2) DEFAULT NULL,
  `losses` tinyint(2) DEFAULT NULL,
  `goalsPos` mediumint(4) DEFAULT NULL,
  `goalsNeg` mediumint(4) DEFAULT NULL,
  `goalsDiff` mediumint(4) DEFAULT NULL,
  `pointsPos` tinyint(2) DEFAULT NULL,
  `pointsNeg` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`season`, `teamkey`, `team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_standings_details`;
CREATE TABLE `#__hb_standings_details` (
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `teamkey` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rank` tinyint(2) DEFAULT NULL,
  `team` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `games` tinyint(2) DEFAULT NULL,
  `gamesH` tinyint(2) DEFAULT NULL,
  `gamesA` tinyint(2) DEFAULT NULL,
  `wins` tinyint(2) DEFAULT NULL,
  `winsHome` tinyint(2) DEFAULT NULL,
  `winsAway` tinyint(2) DEFAULT NULL,
  `ties` tinyint(2) DEFAULT NULL,
  `tiesHome` tinyint(2) DEFAULT NULL,
  `tiesAway` tinyint(2) DEFAULT NULL,
  `losses` tinyint(2) DEFAULT NULL,
  `lossesHome` tinyint(2) DEFAULT NULL,
  `lossesAway` tinyint(2) DEFAULT NULL,
  `goalsPos` mediumint(4) DEFAULT NULL,
  `goalsPosHome` mediumint(4) DEFAULT NULL,
  `goalsPosAway` mediumint(4) DEFAULT NULL,
  `goalsNeg` mediumint(4) DEFAULT NULL,
  `goalsNegHome` mediumint(4) DEFAULT NULL,
  `goalsNegAway` mediumint(4) DEFAULT NULL,
  `goalsDiff` mediumint(4) DEFAULT NULL,
  `goalsDiffHome` mediumint(4) DEFAULT NULL,
  `goalsDiffAway` mediumint(4) DEFAULT NULL,
  `pointsPos` tinyint(2) DEFAULT NULL,
  `pointsPosHome` tinyint(2) DEFAULT NULL,
  `pointsPosAway` tinyint(2) DEFAULT NULL,
  `pointsNeg` tinyint(2) DEFAULT NULL,
  `pointsNegHome` tinyint(2) DEFAULT NULL,
  `pointsNegAway` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`season`, `teamkey`, `team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_updatelog`;

CREATE TABLE `hkog_hb_updatelog` (
  `updateId` int(10) NOT NULL AUTO_INCREMENT,,
  `type` text DEFAULT NULL,
  `teamkey` text DEFAULT NULL,
  `dateTime` datetime DEFAULT NULL,
  `schedule` tinyint(4) DEFAULT NULL,
  `standings` tinyint(4) DEFAULT NULL,
  `standingsDetails` tinyint(4) DEFAULT NULL,
  `error` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`updateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

