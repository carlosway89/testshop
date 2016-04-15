DROP TABLE IF EXISTS `feature_set_to_products`;
CREATE TABLE IF NOT EXISTS `feature_set_to_products` (
`feature_set_id` int(11) UNSIGNED NOT NULL,
`products_id` int(11) UNSIGNED NOT NULL,
  KEY `products_id` (`products_id`)
) ENGINE=MyISAM;