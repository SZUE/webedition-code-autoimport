CREATE TABLE ###TBLPREFIX###tblbannerviews (
  viewid bigint unsigned NOT NULL AUTO_INCREMENT,
  ID bigint unsigned NOT NULL default '0',
  `Timestamp` int unsigned default NULL,
  IP varchar(40) NOT NULL default '',
  Referer varchar(255) NOT NULL default '',
  DID int unsigned NOT NULL default '0',
  Page varchar(255) NOT NULL default '',
  PRIMARY KEY (`viewid`),
	KEY ID (ID,Page,`Timestamp`)
) ENGINE=MyISAM;