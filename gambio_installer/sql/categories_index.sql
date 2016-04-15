DROP TABLE IF EXISTS `categories_index`;
CREATE TABLE IF NOT EXISTS `categories_index` (
  `products_id` int(11) NOT NULL,
  `categories_index` text NOT NULL,
  PRIMARY KEY (`products_id`),
  FULLTEXT KEY `categories_index` (`categories_index`)
) ENGINE = MyISAM ;

INSERT INTO `categories_index` (`products_id`, `categories_index`) VALUES(1, '-0--1-');