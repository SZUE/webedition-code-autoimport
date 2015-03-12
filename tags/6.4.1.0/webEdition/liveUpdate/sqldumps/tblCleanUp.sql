CREATE TABLE ###TBLPREFIX###tblCleanUp (
  ID mediumint(8) unsigned NOT NULL auto_increment,
  Path char(255) NOT NULL default '',
  `Date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY Path (Path),
  KEY `Date` (`Date`)
) ENGINE=MyISAM;
