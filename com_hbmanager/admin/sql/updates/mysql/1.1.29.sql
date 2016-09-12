
--
-- Table structure for table `hb_mannschaftsdetails`
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
  `berichtLink` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `hb_spiel` ADD PRIMARY KEY( `saison`, `spielIdHvw`);



ALTER TABLE `hb_spiel_spieler`
  DROP PRIMARY KEY,   ADD PRIMARY KEY(`spielIdHvw`, `alias`, `saison`, `trikotNr`);