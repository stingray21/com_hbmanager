CREATE TABLE IF NOT EXISTS `#__hb_holidays` (
  `date` date NOT NULL,
  `holiday` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
