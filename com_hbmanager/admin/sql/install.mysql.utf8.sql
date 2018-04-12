
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
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
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

CREATE TABLE `#__hb_updatelog` (
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
  `number` int(3) NOT NULL,
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


-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_spritesheets`;

CREATE TABLE `#__hb_spritesheets` (
  `spriteID` int(11) NOT NULL,
  `teamkey` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `season` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spritesheet` text COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`spriteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__hb_clubteams`;

CREATE TABLE `#__hb_clubteams` (
  `clubteamId` int(11) NOT NULL AUTO_INCREMENT,
  `teamname_long` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teamname_short` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teamname_abbr` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`clubteamId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--  data for table `#__hb_clubteams`
INSERT INTO `#__hb_clubteams` (`clubteamId`, `teamname_long`, `teamname_short`, `teamname_abbr`) VALUES
(1, 'HK Ostdorf/Geislingen', 'HK Ostd/Geisl', 'HKOG'),
(2, 'HK Ostdorf/Geislingen 2', 'HK Ostd/Geisl 2', 'HKOG 2'),
(3, 'HK Ostdorf/Geislingen 3', 'HK Ostd/Geisl 3', 'HKOG 3'),
(4, 'HSG Albstadt', 'HSG Albstadt', 'Albstadt'),
(5, 'HSG Albstadt 2', 'HSG Albstadt 2', 'Albstadt 2'),
(6, 'HSG Baar', 'HSG Baar', 'Baar'),
(7, 'HSG Baar 2', 'HSG Baar 2', 'Baar 2'),
(8, 'HSG Fridingen/Mühlheim', 'HSG Frid/Mühl', 'Frid/Mühl'),
(9, 'HSG Fridingen/Mühlheim 2', 'HSG Frid/Mühl 2', 'Frid/Mühl 2'),
(10, 'HSG Fridingen/Mühlheim 3', 'HSG Frid/Mühl 3', 'Frid/Mühl 3'),
(11, 'HSG Frittlingen-Neufra', 'HSG Fritt-Neuf', 'Fritt-Neuf'),
(12, 'HSG Frittlingen-Neufra 2', 'HSG Fritt-Neuf 2', 'Fritt-Neuf 2'),
(13, 'HSG Hossingen-Meßstetten', 'HSG Hoss-Meß', 'Hoss-Meß'),
(14, 'HSG Hossingen-Meßstetten 2', 'HSG Hoss-Meß 2', 'Hoss-Meß 2'),
(15, 'HSG Neckartal', 'HSG Neckartal', 'Neckartal'),
(16, 'HSG Neckartal 2', 'HSG Neckartal 2', 'Neckartal 2'),
(17, 'HSG Nendingen/Tuttlingen/Wurmlingen 2', 'HSG NTW 2', 'NTW 2'),
(18, 'HSG Nendingen/Tuttlingen/Wurmlingen 3', 'HSG NTW 3', 'NTW 3'),
(19, 'HSG Rietheim-Weilheim', 'HSG Riet-Weil', 'Riet-Weil'),
(20, 'HSG Rietheim-Weilheim 2', 'HSG Riet-Weil 2', 'Riet-Weil 2'),
(21, 'HSG Rottweil', 'HSG Rottweil', 'Rottweil'),
(22, 'HSG Rottweil 2', 'HSG Rottweil 2', 'Rottweil 2'),
(23, 'HSG Rottweil 3', 'HSG Rottweil 3', 'Rottweil 3'),
(24, 'HSG Rottweil 4', 'HSG Rottweil 4', 'Rottweil 4'),
(25, 'HWB Handball Winterlingen-Bitz', 'HWB Wint-Bitz', 'Wint-Bitz'),
(26, 'JGW Frommern-Streichen', 'JGW From-Strei', 'From-Strei'),
(27, 'JSG Balingen-Weilstetten', 'JSG Bal-Weilst', 'Bal-Weilst'),
(28, 'JSG Balingen-Weilstetten 2', 'JSG Bal-Weilst 2', 'Bal-Weilst 2'),
(29, 'TG Schömberg', 'TG Schömberg', 'Schömb.'),
(30, 'TG Schömberg 2', 'TG Schömberg 2', 'Schömb. 2'),
(31, 'TG Schömberg 3', 'TG Schömberg 3', 'Schömb. 3'),
(32, 'TG 1859 Schwenningen', 'TG Schwenn.', 'TG Schw.'),
(33, 'TG 1859 Schwenningen 2', 'TG Schwenn. 2', 'TG Schw. 2'),
(34, 'TSV Dunningen', 'TSV Dunningen', 'Dunningen'),
(35, 'TSV Stetten a.k.M. 1', 'TSV Stett./akM 1', 'Stett. akM 1'),
(36, 'TSV Stetten a.k.M. 2', 'TSV Stett./akM 2', 'Stett. akM 2'),
(37, 'TV Aixheim', 'TV Aixheim', 'Aixheim'),
(38, 'TV Aixheim 2', 'TV Aixheim 2', 'Aixheim 2'),
(39, 'TV Hechingen', 'TV Hechingen', 'Hechingen'),
(40, 'TV Onstmettingen', 'TV Onstmett.', 'Onstmett.'),
(41, 'TV Onstmettingen 2', 'TV Onstmett. 2', 'Onstmett. 2'),
(42, 'TV Spaichingen', 'TV Spaichingen', 'Spaich.'),
(43, 'TV Spaichingen 2', 'TV Spaichingen 2', 'Spaich. 2'),
(44, 'TV 1905 Streichen', 'TV Streichen', 'Streichen'),
(45, 'TV Weilstetten 2', 'TV Weilstetten 2', 'Weilstet. 2'),
(46, 'VfH 87 Schwenningen', 'VfH Schwenn.', 'VfH Schw.'),
(47, 'VfH 87 Schwenningen 2', 'VfH Schwenn. 2', 'VfH Schw. 2');
