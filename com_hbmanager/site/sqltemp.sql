
CREATE TABLE `hb_spielbericht_details` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `spielIdHvw` int(6) NOT NULL DEFAULT '0',
  `actionIndex` int(3) DEFAULT NULL,
  `timeString` varchar(5) DEFAULT NULL,
  `time` int(5) DEFAULT NULL,
  `scoreChange` tinyint(1) DEFAULT NULL,
  `scoreHome` int(3) DEFAULT NULL,
  `scoreAway` int(3) DEFAULT NULL,
  `scoreDiff` int(2) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `number` int(3) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `team` int(1) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stats_goals` int(3) DEFAULT NULL,
  `stats_yellow` int(1) DEFAULT NULL,
  `stats_suspension` int(1) DEFAULT NULL,
  `stats_red` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `hb_spielbericht_details`
  ADD PRIMARY KEY (`saison`,`spielIdHvw`, `actionIndex`);


INSERT INTO `hb_spielbericht_details` (`saison`, `spielIdHvw`, `actionIndex`, `timeString`, `time`, `scoreChange`, `scoreHome`, `scoreAway`, `scoreDiff`, `text`, `number`, `name`, `alias`, `team`, `category`, `stats_goals`, `stats_yellow`, `stats_suspension`, `stats_red`) 
VALUES 
( '2016-2017', 70049, 0, "00:00", 0, 1, 0, 0, 0, "Spielbeginn", null, null, "", 0, "", 0, 0, 0, 0 ),
( '2016-2017', 70049, 1, "01:52", 112, 0, 0, 0, 0, "Verwarnung für Matthias Hipp (9)", "9", "Matthias Hipp", "matthias-hipp", -1, "yellow", 0, 1, 0, 0 ),
( '2016-2017', 70049, 2, "02:29", 149, 1, 1, 0, -1, "Tor durch Lucas Herre (10)", "10", "Lucas Herre", "lucas-herre", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 3, "04:23", 263, 1, 1, 1, 0, "Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 4, "06:08", 368, 1, 2, 1, -1, "Tor durch Thorsten Schlaich (7)", "7", "Thorsten Schlaich", "thorsten-schlaich", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 5, "07:06", 426, 1, 2, 2, 0, "Tor durch Markus Buhl (17)", "17", "Markus Buhl", "markus-buhl", -1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 6, "07:45", 465, 0, 2, 2, 0, "Verwarnung für Marius Sigrist (5)", "5", "Marius Sigrist", "marius-sigrist", -1, "yellow", 0, 1, 0, 0 ),
( '2016-2017', 70049, 7, "08:46", 526, 0, 2, 2, 0, "Verwarnung für Luis Herre (9)", "9", "Luis Herre", "luis-herre", 1, "yellow", 0, 1, 0, 0 ),
( '2016-2017', 70049, 8, "09:51", 591, 1, 2, 3, 1, "Tor durch Matthias Hipp (9)", "9", "Matthias Hipp", "matthias-hipp", -1, "goal", 1, 1, 0, 0 ),
( '2016-2017', 70049, 9, "11:45", 705, 0, 2, 3, 1, "Verwarnung für Steffen Bechtold (8)", "8", "Steffen Bechtold", "steffen-bechtold", 1, "yellow", 0, 1, 0, 0 ),
( '2016-2017', 70049, 10, "13:21", 801, 1, 3, 3, 0, "7m-Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "penalty", 0, 0, 0, 0 ),
( '2016-2017', 70049, 11, "14:15", 855, 1, 3, 4, 1, "Tor durch Markus Buhl (17)", "17", "Markus Buhl", "markus-buhl", -1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 12, "14:59", 899, 1, 3, 5, 2, "Tor durch Marius Sigrist (5)", "5", "Marius Sigrist", "marius-sigrist", -1, "goal", 1, 1, 0, 0 ),
( '2016-2017', 70049, 13, "15:06", 906, 1, 4, 5, 1, "Tor durch Luis Herre (9)", "9", "Luis Herre", "luis-herre", 1, "goal", 1, 1, 0, 0 ),
( '2016-2017', 70049, 14, "15:10", 910, 0, 4, 5, 1, "2-min Strafe für Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "suspension", 1, 0, 1, 0 ),
( '2016-2017', 70049, 15, "15:42", 942, 1, 4, 6, 2, "Tor durch Jeremias Heppeler (15)", "15", "Jeremias Heppeler", "jeremias-heppeler", -1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 16, "18:12", 1092, 1, 5, 6, 1, "Tor durch Felix Foth (14)", "14", "Felix Foth", "felix-foth", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 17, "19:06", 1146, 1, 5, 7, 2, "Tor durch Jeremias Heppeler (15)", "15", "Jeremias Heppeler", "jeremias-heppeler", -1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 18, "19:40", 1180, 1, 6, 7, 1, "Tor durch Lukas Eberhart (4)", "4", "Lukas Eberhart", "lukas-eberhart", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 19, "21:04", 1264, 1, 7, 7, 0, "Tor durch Lucas Herre (10)", "10", "Lucas Herre", "lucas-herre", 1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 20, "21:18", 1278, 1, 7, 8, 1, "Tor durch Jonas Schwarz (3)", "3", "Jonas Schwarz", "jonas-schwarz", -1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 21, "22:39", 1359, 1, 8, 8, 0, "Tor durch Luis Herre (9)", "9", "Luis Herre", "luis-herre", 1, "goal", 2, 1, 0, 0 ),
( '2016-2017', 70049, 22, "22:47", 1367, 0, 8, 8, 0, "Verwarnung für Jonas Schwarz (3)", "3", "Jonas Schwarz", "jonas-schwarz", -1, "yellow", 1, 1, 0, 0 ),
( '2016-2017', 70049, 23, "24:39", 1479, 1, 8, 9, 1, "Tor durch Simon Moser (2)", "2", "Simon Moser", "simon-moser", -1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 24, "26:01", 1561, 1, 9, 9, 0, "Tor durch Andreas Haug (5)", "5", "Andreas Haug", "andreas-haug", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 25, "26:43", 1603, 1, 9, 10, 1, "Tor durch Jonas Schwarz (3)", "3", "Jonas Schwarz", "jonas-schwarz", -1, "goal", 2, 1, 0, 0 ),
( '2016-2017', 70049, 26, "27:10", 1630, 0, 9, 10, 1, "7m KEIN Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "penalty", 0, 0, 0, 0 ),
( '2016-2017', 70049, 27, "30:00", 1800, 0, 9, 10, 1, "Verwarnung für Felix Foth (14)", "14", "Felix Foth", "felix-foth", 1, "yellow", 1, 1, 0, 0 ),
( '2016-2017', 70049, 28, "30:27", 1827, 1, 10, 10, 0, "Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 29, "30:33", 1833, 0, 10, 10, 0, "2-min Strafe für Felix Foth (14)", "14", "Felix Foth", "felix-foth", 1, "suspension", 1, 1, 1, 0 ),
( '2016-2017', 70049, 30, "30:40", 1840, 1, 10, 11, 1, "7m-Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "penalty", 1, 0, 1, 0 ),
( '2016-2017', 70049, 31, "31:40", 1900, 1, 11, 11, 0, "Tor durch Marcel Schick (35)", "35", "Marcel Schick", "marcel-schick", 1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 32, "31:58", 1918, 0, 11, 11, 0, "2-min Strafe für Felix Kohle (47)", "47", "Felix Kohle", "felix-kohle", 1, "suspension", 0, 0, 1, 0 ),
( '2016-2017', 70049, 33, "32:02", 1922, 0, 11, 11, 0, "7m KEIN Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "penalty", 1, 0, 1, 0 ),
( '2016-2017', 70049, 34, "32:35", 1955, 1, 12, 11, -1, "Tor durch Thorsten Schlaich (7)", "7", "Thorsten Schlaich", "thorsten-schlaich", 1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 35, "33:04", 1984, 1, 12, 12, 0, "7m-Tor durch Hannes Leibinger (14)", "14", "Hannes Leibinger", "hannes-leibinger", -1, "penalty", 0, 0, 0, 0 ),
( '2016-2017', 70049, 36, "33:44", 2024, 1, 13, 12, -1, "Tor durch Felix Foth (14)", "14", "Felix Foth", "felix-foth", 1, "goal", 2, 1, 1, 0 ),
( '2016-2017', 70049, 37, "35:13", 2113, 0, 13, 12, -1, "2-min Strafe für Markus Buhl (17)", "17", "Markus Buhl", "markus-buhl", -1, "suspension", 2, 0, 1, 0 ),
( '2016-2017', 70049, 38, "35:16", 2116, 1, 14, 12, -2, "7m-Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "penalty", 1, 0, 0, 0 ),
( '2016-2017', 70049, 39, "36:23", 2183, 1, 15, 12, -3, "7m-Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "penalty", 1, 0, 0, 0 ),
( '2016-2017', 70049, 40, "36:33", 2193, 0, 15, 12, -3, "Auszeit HSG Fridingen/Mühlheim 2", null, null, "", -1, "timeout", 0, 0, 0, 0 ),
( '2016-2017', 70049, 41, "40:38", 2438, 1, 15, 13, -2, "Tor durch Lukas Schnell (13)", "13", "Lukas Schnell", "lukas-schnell", -1, "goal", 1, 0, 0, 0 ),
( '2016-2017', 70049, 42, "42:54", 2574, 1, 16, 13, -3, "Tor durch Lucas Herre (10)", "10", "Lucas Herre", "lucas-herre", 1, "goal", 3, 0, 0, 0 ),
( '2016-2017', 70049, 43, "43:42", 2622, 1, 17, 13, -4, "Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 44, "45:54", 2754, 1, 17, 14, -3, "Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "goal", 2, 0, 1, 0 ),
( '2016-2017', 70049, 45, "47:08", 2828, 1, 17, 15, -2, "7m-Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "penalty", 2, 0, 1, 0 ),
( '2016-2017', 70049, 46, "47:14", 2834, 0, 17, 15, -2, "Auszeit HK Ostdorf/Geislingen", null, null, "", 1, "timeout", 0, 0, 0, 0 ),
( '2016-2017', 70049, 47, "47:46", 2866, 1, 17, 16, -1, "Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "goal", 3, 0, 1, 0 ),
( '2016-2017', 70049, 48, "48:38", 2918, 1, 18, 16, -2, "Tor durch Marcel Schick (35)", "35", "Marcel Schick", "marcel-schick", 1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 49, "48:57", 2937, 1, 18, 17, -1, "Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "goal", 4, 0, 1, 0 ),
( '2016-2017', 70049, 50, "49:36", 2976, 1, 18, 18, 0, "Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "goal", 5, 0, 1, 0 ),
( '2016-2017', 70049, 51, "49:45", 2985, 1, 19, 18, -1, "Tor durch Lucas Herre (10)", "10", "Lucas Herre", "lucas-herre", 1, "goal", 4, 0, 0, 0 ),
( '2016-2017', 70049, 52, "52:26", 3146, 1, 20, 18, -2, "Tor durch Thorsten Schlaich (7)", "7", "Thorsten Schlaich", "thorsten-schlaich", 1, "goal", 3, 0, 0, 0 ),
( '2016-2017', 70049, 53, "53:48", 3228, 0, 20, 18, -2, "2-min Strafe für Lucas Herre (10)", "10", "Lucas Herre", "lucas-herre", 1, "suspension", 4, 0, 1, 0 ),
( '2016-2017', 70049, 54, "54:08", 3248, 1, 20, 19, -1, "Tor durch Andreas Hipp (4)", "4", "Andreas Hipp", "andreas-hipp", -1, "goal", 6, 0, 1, 0 ),
( '2016-2017', 70049, 55, "55:24", 3324, 1, 21, 19, -2, "Tor durch Felix Foth (14)", "14", "Felix Foth", "felix-foth", 1, "goal", 3, 1, 1, 0 ),
( '2016-2017', 70049, 56, "56:24", 3384, 1, 22, 19, -3, "Tor durch Andreas Haug (5)", "5", "Andreas Haug", "andreas-haug", 1, "goal", 2, 0, 0, 0 ),
( '2016-2017', 70049, 57, "56:37", 3397, 1, 22, 20, -2, "Tor durch Jonas Schwarz (3)", "3", "Jonas Schwarz", "jonas-schwarz", -1, "goal", 3, 1, 0, 0 ),
( '2016-2017', 70049, 58, "57:18", 3438, 1, 23, 20, -3, "7m-Tor durch Phillip Koch (2)", "2", "Phillip Koch", "phillip-koch", 1, "penalty", 2, 0, 0, 0 ),
( '2016-2017', 70049, 59, "57:32", 3452, 0, 23, 20, -3, "Auszeit HSG Fridingen/Mühlheim 2", null, null, "", -1, "timeout", 0, 0, 0, 0 ),
( '2016-2017', 70049, 60, "59:08", 3548, 1, 24, 20, -4, "Tor durch Felix Foth (14)", "14", "Felix Foth", "felix-foth", 1, "goal", 4, 1, 1, 0 ),
( '2016-2017', 70049, 61, "59:35", 3575, 1, 24, 21, -3, "Tor durch Jeremias Heppeler (15)", "15", "Jeremias Heppeler", "jeremias-heppeler", -1, "goal", 3, 0, 0, 0 ),
( '2016-2017', 70049, 62, "59:46", 3586, 1, 25, 21, -4, "Tor durch Marcel Schick (35)", "35", "Marcel Schick", "marcel-schick", 1, "goal", 3, 0, 0, 0 ),
( '2016-2017', 70049, 63, "60:00", 3600, 1, 25, 21, -4, "Spielende", null, null, "", 0, "", 0, 0, 0, 0 );



  
CREATE TABLE `hb_spritesheets` (
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `index` int(3) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `hb_spritesheets`
  ADD PRIMARY KEY (`saison`,`kuerzel`, `index`);

INSERT INTO `hb_spritesheets` (`saison`, `kuerzel`, `index`, `alias`) 
VALUES 
( '2016-2017', 'M-1', 0, 'luis-herre' ),
( '2016-2017', 'M-1', 1, 'andreas-haug' ),
( '2016-2017', 'M-1', 2, 'dummy' ),
( '2016-2017', 'M-1', 3, 'fabian-stegmaier' ),
( '2016-2017', 'M-1', 4, 'felix-kohle' ),
( '2016-2017', 'M-1', 5, 'florian-struecker' ),
( '2016-2017', 'M-1', 6, 'lucas-herre' ),
( '2016-2017', 'M-1', 7, 'bernd-schreyeck' ),
( '2016-2017', 'M-1', 8, 'lukas-eberhart' ),
( '2016-2017', 'M-1', 9, 'marcel-schick' ),
( '2016-2017', 'M-1', 10, 'markus-schuler' ),
( '2016-2017', 'M-1', 11, 'phillip-koch' ),
( '2016-2017', 'M-1', 12, 'steffen-bechtold' ),
( '2016-2017', 'M-1', 13, 'thorsten-schlaich' );