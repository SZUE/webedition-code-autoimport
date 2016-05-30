CREATE TABLE ###TBLPREFIX###tblTODOHistory (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  UserID int unsigned NOT NULL default '0',
  fromUserID int unsigned NOT NULL default '0',
  `Comment` text,
  Created int unsigned default NULL,
  `action` int unsigned default NULL,
  `status` tinyint unsigned default NULL,
  tag tinyint unsigned default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;