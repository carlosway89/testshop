DROP TABLE IF EXISTS `products_content`;
CREATE TABLE `products_content` (
  `content_id` int(11) NOT NULL auto_increment,
  `products_id` int(11) NOT NULL default '0',
  `group_ids` text,
  `content_name` varchar(32) NOT NULL default '',
  `content_file` varchar(64) NOT NULL default '',
  `content_link` text NOT NULL,
  `languages_id` int(11) NOT NULL default '0',
  `content_read` int(11) NOT NULL default '0',
  `file_comment` text NOT NULL,
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM;