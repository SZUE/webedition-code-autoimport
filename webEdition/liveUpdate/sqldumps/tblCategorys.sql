CREATE TABLE ###TBLPREFIX###tblCategorys (
  ID int(11) unsigned NOT NULL auto_increment,
  Category varchar(64) NOT NULL default '',
  `Text` varchar(64) default NULL,
  Path varchar(255) default NULL,
  ParentID int(11) unsigned default NULL,
  IsFolder tinyint(1) unsigned  default NULL,
  Icon ENUM('cat.gif','folder.gif') NOT NULL default 'cat.gif',
	Title varchar(255) NOT NULL default '',
	Description longtext NOT NULL,
  PRIMARY KEY  (ID),
  KEY Path (Path)
) ENGINE=MyISAM;
