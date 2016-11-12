--
-- Table structure for table `hb_updatelog`
--

CREATE TABLE IF NOT EXISTS `hb_updatelog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `typ` text NOT NULL,
  `kuerzel` text NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
