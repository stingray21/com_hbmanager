-- --------------------------------------------------------

--
-- Table structure for table `hb_halle`
--

CREATE TABLE IF NOT EXISTS `hb_halle` (
  `halleID` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `hallenNummer` int(6) DEFAULT NULL,
  `kurzname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `land` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plz` int(6) DEFAULT NULL,
  `stadt` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `strasse` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefon` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bezirkNummer` int(6) DEFAULT NULL,
  `bezirk` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `freigabeVerband` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `freigabeBezirk` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `haftmittel` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `letzteAenderung` datetime DEFAULT NULL,
  PRIMARY KEY (`halleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_mannschaft`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaft` (
  `mannschaftID` int(3) NOT NULL AUTO_INCREMENT,
  `reihenfolge` int(3) DEFAULT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mannschaft` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nameKurz` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ligaKuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `liga` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geschlecht` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jugend` tinyint(1) DEFAULT NULL,
  `hvwLink` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updateTabelle` datetime DEFAULT NULL,
  `updateSpielplan` datetime DEFAULT NULL,
  PRIMARY KEY (`mannschaftID`),
  UNIQUE KEY `kuerzel` (`kuerzel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_mannschaftsfoto`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaftsfoto` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kuerzel` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dateiname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `saison` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `untertitel_dt1` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd1` longtext COLLATE utf8_unicode_ci,
  `untertitel_dt2` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd2` longtext COLLATE utf8_unicode_ci,
  `untertitel_dt3` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd3` longtext COLLATE utf8_unicode_ci,
  `untertitel_dt4` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd4` longtext COLLATE utf8_unicode_ci,
  `kommentar` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_mannschaft_spieler`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaft_spieler` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `mannschaftID` int(6) DEFAULT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spielerID` int(6) DEFAULT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_mannschaft_trainer`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaft_trainer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rangfolge` int(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_mannschaft_training`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaft_training` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trainingID` int(6) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spiel`
--

CREATE TABLE IF NOT EXISTS `hb_spiel` (
  `spielID` int(6) NOT NULL AUTO_INCREMENT,
  `spielIDhvw` int(6) DEFAULT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hallenNummer` int(6) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `uhrzeit` time DEFAULT NULL,
  `heim` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gast` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toreHeim` int(3) DEFAULT NULL,
  `toreGast` int(3) DEFAULT NULL,
  `bemerkung` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`spielID`),
  UNIQUE KEY `spielIDhvw` (`spielIDhvw`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spielbericht`
--

CREATE TABLE IF NOT EXISTS `hb_spielbericht` (
  `spielberichtID` int(6) NOT NULL AUTO_INCREMENT,
  `spielIDhvw` int(6) DEFAULT NULL,
  `bericht` longtext COLLATE utf8_unicode_ci,
  `spielerliste` longtext COLLATE utf8_unicode_ci,
  `zusatz` longtext COLLATE utf8_unicode_ci,
  `halbzeitstand` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spielverlauf` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`spielberichtID`),
  UNIQUE KEY `spielIDhvw` (`spielIDhvw`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spielvorschau`
--

CREATE TABLE IF NOT EXISTS `hb_spielvorschau` (
  `spielvorschauID` int(6) NOT NULL AUTO_INCREMENT,
  `spielIDhvw` int(6) DEFAULT NULL,
  `vorschau` longtext COLLATE utf8_unicode_ci,
  `treffOrt` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `treffZeit` time DEFAULT NULL,
  PRIMARY KEY (`spielvorschauID`),
  UNIQUE KEY `spielIDhvw` (`spielIDhvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `hb_tabelle`
--

CREATE TABLE IF NOT EXISTS `hb_tabelle` (
  `ID` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `platz` tinyint(2) DEFAULT NULL,
  `verein` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spiele` tinyint(2) DEFAULT NULL,
  `siege` tinyint(2) DEFAULT NULL,
  `unentschieden` tinyint(2) DEFAULT NULL,
  `niederlagen` tinyint(2) DEFAULT NULL,
  `plustore` mediumint(4) DEFAULT NULL,
  `minustore` mediumint(4) DEFAULT NULL,
  `torDifferenz` mediumint(4) DEFAULT NULL,
  `pluspunkte` tinyint(2) DEFAULT NULL,
  `minuspunkte` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_training`
--

CREATE TABLE IF NOT EXISTS `hb_training` (
  `trainingID` int(3) NOT NULL AUTO_INCREMENT,
  `tag` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `beginn` time NOT NULL,
  `ende` time NOT NULL,
  `bemerkung` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hallenNr` int(7) DEFAULT NULL,
  `sichtbar` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`trainingID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


--
-- Table structure for table `hb_mannschaft_spieler`
--

CREATE TABLE IF NOT EXISTS `hb_mannschaft_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `trikotNr` int(3) DEFAULT NULL,
  `trainer` tinyint(1) DEFAULT '0',
  `TW` tinyint(1) DEFAULT '0',
  `LA` tinyint(1) DEFAULT '0',
  `RL` tinyint(1) DEFAULT '0',
  `RM` tinyint(1) DEFAULT '0',
  `RR` tinyint(1) DEFAULT '0',
  `RA` tinyint(1) DEFAULT '0',
  `KM` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Table structure for table `hb_spieler`
--

CREATE TABLE IF NOT EXISTS `hb_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `groesse` int(5) DEFAULT NULL,
  `geburtstag` date DEFAULT NULL,
  `vereine` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `hb_spiel_spieler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spielIdHvw` int(10) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `kuerzel` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `saison` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tw` tinyint(1) DEFAULT NULL,
  `tore` int(3) DEFAULT NULL,
  `davon7m` int(3) DEFAULT NULL,
  `gelbeKarte` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `roteKarte` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `2min1` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `2min2` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `2min3` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `kommentar` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
