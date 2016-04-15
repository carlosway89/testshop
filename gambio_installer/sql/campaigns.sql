DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE `campaigns` (
  `campaigns_id` int(11) NOT NULL auto_increment,
  `campaigns_name` varchar(32) NOT NULL default '',
  `campaigns_refID` varchar(64) default NULL,
  `campaigns_leads` int(11) NOT NULL default '0',
  `date_added` datetime default NULL,
  `last_modified` datetime default NULL,
  PRIMARY KEY  (`campaigns_id`),
  KEY `IDX_CAMPAIGNS_NAME` (`campaigns_name`)
) ENGINE=MyISAM;