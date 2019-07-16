SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `crawling_url` (
  `id` int(11) NOT NULL,
  `tracking_id` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL,
  `type` smallint(6) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `migration_versions` (`version`) VALUES
('20180628093804'),
('20180628175638'),
('20180629072706'),
('20180702093826'),
('20180703083722'),
('20180703175736'),
('20180704122712');

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `tracking_id` int(11) DEFAULT NULL,
  `asin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_review_count` int(11) DEFAULT NULL,
  `total_review_count_verified` int(11) DEFAULT NULL,
  `total_star_rating` double DEFAULT NULL,
  `total_five` int(11) DEFAULT NULL,
  `total_five_verified` int(11) DEFAULT NULL,
  `total_four` int(11) DEFAULT NULL,
  `total_four_verified` int(11) DEFAULT NULL,
  `total_three` int(11) DEFAULT NULL,
  `total_three_verified` int(11) DEFAULT NULL,
  `total_two` int(11) DEFAULT NULL,
  `total_two_verified` int(11) DEFAULT NULL,
  `total_one` int(11) DEFAULT NULL,
  `total_one_verified` int(11) DEFAULT NULL,
  `total_child_review_count` int(11) DEFAULT NULL,
  `total_child_review_count_verified` int(11) DEFAULT NULL,
  `total_child_five` int(11) DEFAULT NULL,
  `total_child_five_verified` int(11) DEFAULT NULL,
  `total_child_four` int(11) DEFAULT NULL,
  `total_child_four_verified` int(11) DEFAULT NULL,
  `total_child_three` int(11) DEFAULT NULL,
  `total_child_three_verified` int(11) DEFAULT NULL,
  `total_child_two` int(11) DEFAULT NULL,
  `total_child_two_verified` int(11) DEFAULT NULL,
  `total_child_one` int(11) DEFAULT NULL,
  `total_child_one_verified` int(11) DEFAULT NULL,
  `is_downloaded` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `tracking_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` smallint(6) NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `helpful_count` int(11) NOT NULL,
  `verified` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `date_review` datetime NOT NULL,
  `active` smallint(6) NOT NULL,
  `review_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_downloaded` smallint(6) NOT NULL,
  `is_last_update` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tracking` (
  `id` int(11) NOT NULL,
  `asin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `crawling_url`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_306FA80A7D05ABBE` (`tracking_id`);

ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D34A04AD7D05ABBE` (`tracking_id`);

ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `review_id` (`review_id`) USING BTREE,
  ADD KEY `IDX_794381C67D05ABBE` (`tracking_id`);

ALTER TABLE `tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_A87C621CEA5C05C2` (`asin`);

ALTER TABLE `crawling_url`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `crawling_url`
  ADD CONSTRAINT `FK_6B2D5C527D05ABBE` FOREIGN KEY (`tracking_id`) REFERENCES `tracking` (`id`);

ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD7D05ABBE` FOREIGN KEY (`tracking_id`) REFERENCES `tracking` (`id`);

ALTER TABLE `review`
  ADD CONSTRAINT `FK_794381C67D05ABBE` FOREIGN KEY (`tracking_id`) REFERENCES `tracking` (`id`);
