
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
  `caption` longtext COLLATE utf8_unicode_ci,
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
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `report` longtext COLLATE utf8_unicode_ci,
  `playerList` longtext COLLATE utf8_unicode_ci,
  `extra` longtext COLLATE utf8_unicode_ci,
  `halftime` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trend` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`reportID`),
  UNIQUE KEY (`season`, `gameIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_pregame`;
CREATE TABLE `#__hb_pregame` (
  `pregameID` int(6) NOT NULL AUTO_INCREMENT,
  `gameIdHvw` int(6) NOT NULL,
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `pregame` longtext COLLATE utf8_unicode_ci,
  `meetupLoc` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meetupTime` time DEFAULT NULL,
  PRIMARY KEY (`pregameID`),
  UNIQUE KEY (`season`, `gameIdHvw`)
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
  `updateId` int(10) NOT NULL AUTO_INCREMENT,
  `type` text DEFAULT NULL,
  `teamkey` text DEFAULT NULL,
  `dateTime` datetime DEFAULT NULL,
  `schedule` tinyint(4) DEFAULT NULL,
  `standings` tinyint(4) DEFAULT NULL,
  `standingsDetails` tinyint(4) DEFAULT NULL,
  `error` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`updateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_player`;

CREATE TABLE `#__hb_player` (
  `playerId` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `height` int(5) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `clubs` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`playerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_game_player`;

CREATE TABLE `#__hb_game_player` (
  `season` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gameIdHvw` int(10) NOT NULL,
  `alias` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teamkey` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `number` varchar(10) NOT NULL,
  `goalie` tinyint(1) DEFAULT NULL,
  `goals` int(3) DEFAULT NULL,
  `penalty` int(3) DEFAULT NULL,
  `penaltyGoals` int(3) DEFAULT NULL,
  `yellow` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `suspension1` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `suspension2` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `suspension3` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `red` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `suspensionTeam` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`season`, `gameIdHvw`, `alias`, `number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_team_player`;

CREATE TABLE `#__hb_team_player` (
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `teamkey` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `season` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `number` int(3) DEFAULT NULL,
  `coach` tinyint(1) DEFAULT '0',
  `TW` tinyint(1) DEFAULT '0',
  `LA` tinyint(1) DEFAULT '0',
  `RL` tinyint(1) DEFAULT '0',
  `RM` tinyint(1) DEFAULT '0',
  `RR` tinyint(1) DEFAULT '0',
  `RA` tinyint(1) DEFAULT '0',
  `KM` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`season`, `teamkey`, `alias`, `number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_gamereport_details`;

CREATE TABLE `#__hb_gamereport_details` (
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gameIdHvw` int(6) NOT NULL DEFAULT '0',
  `actionIndex` int(3) NOT NULL,
  `timeString` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` int(5) DEFAULT NULL,
  `scoreChange` tinyint(1) DEFAULT NULL,
  `scoreHome` int(3) DEFAULT NULL,
  `scoreAway` int(3) DEFAULT NULL,
  `scoreDiff` int(2) DEFAULT NULL,
  `text` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` int(3) DEFAULT NULL,
  `playerName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `teamFlag` int(1) DEFAULT NULL,
  `category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stats_goals` int(3) DEFAULT NULL,
  `stats_yellow` int(1) DEFAULT NULL,
  `stats_suspension` int(1) DEFAULT NULL,
  `stats_red` int(1) DEFAULT NULL,
  PRIMARY KEY (`season`, `gameIdHvw`, `actionIndex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_training`;

CREATE TABLE `#__hb_training` (
  `trainingID` int(3) NOT NULL,
  `teamkey` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `day` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `training_comment` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gymId` int(7) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`trainingID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_team_coach`;

CREATE TABLE `#__hb_team_coach` (
  `coachID` int(11) NOT NULL,
  `teamkey` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rank` int(2) DEFAULT NULL,
  PRIMARY KEY (`coachID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

