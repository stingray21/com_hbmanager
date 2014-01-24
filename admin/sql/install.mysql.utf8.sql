CREATE TABLE IF NOT EXISTS `hb_teams` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teamkey` varchar(10) DEFAULT NULL,
  `team` varchar(40) DEFAULT NULL,
  `teamName` varchar(50) DEFAULT NULL,
  `league` varchar(10) DEFAULT NULL,
  `completeLeague` varchar(40) DEFAULT NULL,
  `sex` varchar(3) DEFAULT NULL,
  `youth` varchar(10) DEFAULT NULL,
  `HVWlink` varchar(200) DEFAULT NULL,
  `updatedStandings` datetime DEFAULT NULL,
  `updatedSchedule` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `teamkey` (`teamkey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;