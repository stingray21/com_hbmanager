CREATE TABLE `#__hb_holidays` (
  `date` date NOT NULL,
  `holiday` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `#__hb_holidays`
  ADD UNIQUE KEY `date` (`date`);
