CREATE TABLE ###TBLPREFIX###tblCleanUp (
  ID int(11) NOT NULL auto_increment,
  Path varchar(255) NOT NULL default '',
  `Date` int(11) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY Path (Path),
  KEY `Date` (`Date`)
) ENGINE=MyISAM;
