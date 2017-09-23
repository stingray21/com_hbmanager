
--
-- Table structure for table `hb_mannschaftsdetails`
--

-- DROP TABLE IF EXISTS `hb_mannschaftsdetails`;
CREATE TABLE IF NOT EXISTS `hb_mannschaftsdetails` (
  `kuerzel` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `saison` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `tabellenGraph` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- Indexes for table `hb_mannschaftsdetails`
ALTER TABLE `hb_mannschaftsdetails`
 ADD PRIMARY KEY (`kuerzel`,`saison`);

-- Update hb_mannschaft

ALTER TABLE `hb_mannschaft` ADD `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ;