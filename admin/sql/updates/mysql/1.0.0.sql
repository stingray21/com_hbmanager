--
-- Table structure for table `hb_mannschaft`
--
DROP TABLE IF EXISTS `hb_mannschaft`;

CREATE TABLE IF NOT EXISTS `hb_mannschaft` (
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reihenfolge` int(3) DEFAULT NULL,
  `mannschaft` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nameKurz` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ligaKuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `liga` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geschlecht` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jugend` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hvwLink` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `update` datetime DEFAULT NULL,
  PRIMARY KEY (`kuerzel`),
  UNIQUE KEY `kuerzel` (`kuerzel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_mannschaftsfoto`
--
DROP TABLE IF EXISTS `hb_mannschaftsfoto`;

CREATE TABLE IF NOT EXISTS `hb_mannschaftsfoto` (
  `kuerzel` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dateiname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `saison` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `untertitel_dt1` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd1` longtext COLLATE utf8_unicode_ci,
  `untertitel_dt2` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd2` longtext COLLATE utf8_unicode_ci,
  `untertitel_dt3` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd3` longtext COLLATE utf8_unicode_ci,
  `untertitel_dt4` longtext COLLATE utf8_unicode_ci,
  `untertitel_dd4` longtext COLLATE utf8_unicode_ci,
  `kommentar` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`kuerzel`,`saison`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spiel`
--
DROP TABLE IF EXISTS `hb_spiel`;

CREATE TABLE IF NOT EXISTS `hb_spiel` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `spielIdHvw` int(6) NOT NULL DEFAULT '0',
  `ligaKuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hallenNr` int(6) DEFAULT NULL,
  `datumZeit` datetime DEFAULT NULL,
  `heim` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gast` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toreHeim` int(3) DEFAULT NULL,
  `toreGast` int(3) DEFAULT NULL,
  `bemerkung` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wertungHeim` int(3) DEFAULT NULL,
  `wertungGast` int(3) DEFAULT NULL,
  `eigenerVerein` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`saison`,`spielIdHvw`),
  UNIQUE KEY `spielIdHvw` (`spielIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spielbericht`
--
DROP TABLE IF EXISTS `hb_spielbericht`;

CREATE TABLE IF NOT EXISTS `hb_spielbericht` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `spielIdHvw` int(6) NOT NULL DEFAULT '0',
  `bericht` longtext COLLATE utf8_unicode_ci,
  `spielerliste` longtext COLLATE utf8_unicode_ci,
  `zusatz` longtext COLLATE utf8_unicode_ci,
  `halbzeitstand` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spielverlauf` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`saison`,`spielIdHvw`),
  UNIQUE KEY `spielIdHvw` (`spielIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_spielvorschau`
--
DROP TABLE IF EXISTS `hb_spielvorschau`;

CREATE TABLE IF NOT EXISTS `hb_spielvorschau` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `spielIdHvw` int(6) NOT NULL DEFAULT '0',
  `vorschau` longtext COLLATE utf8_unicode_ci,
  `treffOrt` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `treffZeit` time DEFAULT NULL,
  PRIMARY KEY (`saison`,`spielIdHvw`),
  UNIQUE KEY `spielIdHvw` (`spielIdHvw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_staffel`
--
DROP TABLE IF EXISTS `hb_staffel`;

CREATE TABLE IF NOT EXISTS `hb_staffel` (
  `staffel` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `staffelName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `geschlecht` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jugend` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `saison` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mannschaftenTabelle` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mannschaftenSpielplan` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `staffel` (`staffel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_tabelle`
--
DROP TABLE IF EXISTS `hb_tabelle`;

CREATE TABLE IF NOT EXISTS `hb_tabelle` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `platz` tinyint(2) DEFAULT NULL,
  `mannschaft` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `spiele` tinyint(2) DEFAULT NULL,
  `s` tinyint(2) DEFAULT NULL,
  `u` tinyint(2) DEFAULT NULL,
  `n` tinyint(2) DEFAULT NULL,
  `tore` mediumint(4) DEFAULT NULL,
  `gegenTore` mediumint(4) DEFAULT NULL,
  `torDiff` mediumint(4) DEFAULT NULL,
  `punkte` tinyint(2) DEFAULT NULL,
  `minusPunkte` tinyint(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_tabelle_details`
--
DROP TABLE IF EXISTS `hb_tabelle_details`;

CREATE TABLE IF NOT EXISTS `hb_tabelle_details` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `platz` tinyint(2) DEFAULT NULL,
  `mannschaft` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `spiele` tinyint(2) DEFAULT NULL,
  `s` tinyint(2) DEFAULT NULL,
  `sH` tinyint(2) DEFAULT NULL,
  `sA` tinyint(2) DEFAULT NULL,
  `u` tinyint(2) DEFAULT NULL,
  `uH` tinyint(2) DEFAULT NULL,
  `uA` tinyint(2) DEFAULT NULL,
  `n` tinyint(2) DEFAULT NULL,
  `nH` tinyint(2) DEFAULT NULL,
  `nA` tinyint(2) DEFAULT NULL,
  `tore` mediumint(4) DEFAULT NULL,
  `toreH` mediumint(4) DEFAULT NULL,
  `toreA` mediumint(4) DEFAULT NULL,
  `gegenTore` mediumint(4) DEFAULT NULL,
  `gegenToreH` mediumint(4) DEFAULT NULL,
  `gegenToreA` mediumint(4) DEFAULT NULL,
  `torDiff` mediumint(4) DEFAULT NULL,
  `torDiffH` mediumint(4) DEFAULT NULL,
  `torDiffA` mediumint(4) DEFAULT NULL,
  `punkte` tinyint(2) DEFAULT NULL,
  `punkteH` tinyint(2) DEFAULT NULL,
  `punkteA` tinyint(2) DEFAULT NULL,
  `minusPunkte` tinyint(2) DEFAULT NULL,
  `minusPunkteH` tinyint(2) DEFAULT NULL,
  `minusPunkteA` tinyint(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hb_halle`
--
DROP TABLE IF EXISTS `hb_halle`;

CREATE TABLE IF NOT EXISTS `hb_halle` (
  `halleId` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `hallenNr` int(6) DEFAULT NULL,
  `kurzname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hallenName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
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
  PRIMARY KEY (`halleId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `hb_updatelog`
--
DROP TABLE IF EXISTS `hb_updatelog`;

CREATE TABLE IF NOT EXISTS `hb_updatelog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `typ` text NOT NULL,
  `kuerzel` text NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
