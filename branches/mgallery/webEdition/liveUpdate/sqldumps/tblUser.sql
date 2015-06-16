###UPDATEDROPCOL(Portal,###TBLPREFIX###tblUser)###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblUser)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblUser (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Path varchar(255) NOT NULL default '',
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  `Type` tinyint(1) unsigned NOT NULL default '0',
  username varchar(255) NOT NULL default '',
  passwd varchar(255) NOT NULL default '',
  UseSalt tinyint(1) unsigned NOT NULL default '0',
  LoginDenied tinyint(1) unsigned NOT NULL default '0',
  Permissions text NOT NULL,
  ParentPerms tinyint(1) unsigned NOT NULL default '0',
  Alias int(11) unsigned NOT NULL default '0',
  CreatorID int(11) unsigned NOT NULL default '0',
  CreateDate int(10) unsigned NOT NULL default '0',
  ModifierID int(11) unsigned NOT NULL default '0',
  ModifyDate int(10) unsigned NOT NULL default '0',
  Ping datetime default NULL,
  workSpace TEXT NOT NULL default '',
  workSpaceDef TEXT NOT NULL default '',
  workSpaceTmp TEXT NOT NULL default '',
  workSpaceNav TEXT NOT NULL default '',
  workSpaceObj TEXT NOT NULL default '',
  workSpaceNwl TEXT NOT NULL default '',
  workSpaceCust TEXT NOT NULL default '',
  ParentWs tinyint(1) unsigned NOT NULL default '0',
  ParentWst tinyint(1) unsigned NOT NULL default '0',
  ParentWsn tinyint(1) unsigned NOT NULL default '0',
  ParentWso tinyint(1) unsigned NOT NULL default '0',
  ParentWsnl tinyint(1) unsigned NOT NULL default '0',
  ParentWsCust tinyint(1) unsigned NOT NULL default '0',
  Salutation varchar(32) NOT NULL default '',
  `First` varchar(255) NOT NULL default '',
  `Second` varchar(255) NOT NULL default '',
  Address varchar(255) NOT NULL default '',
  HouseNo varchar(11) NOT NULL default '',
  City varchar(255) NOT NULL default '',
  PLZ varchar(32) NOT NULL default '',
  State varchar(255) NOT NULL default '',
  Country varchar(255) NOT NULL default '',
  Tel_preselection varchar(11) NOT NULL default '',
  Telephone varchar(32) NOT NULL default '',
  Fax_preselection varchar(11) NOT NULL default '',
  Fax varchar(32) NOT NULL default '',
  Handy varchar(32) NOT NULL default '',
  Email varchar(255) NOT NULL default '',
  Description TEXT NOT NULL,
  PRIMARY KEY  (ID),
  KEY Ping (Ping),
  KEY Alias (Alias),
  UNIQUE username (username)
) ENGINE=MyISAM;
/* query separator */
###INSTALLONLY###INSERT INTO ###TBLPREFIX###tblUser SET ID=1,Text='admin',Path='/admin',username='admin',passwd='c0e024d9200b5705bc4804722636378a',Permissions='a:1:{s:13:"ADMINISTRATOR";i:1;}',CreateDate=UNIX_TIMESTAMP(),UseSalt=1;
/* query separator */
###UPDATEONLY### UPDATE ###TBLPREFIX###tblUser SET IsFolder=1 WHERE Type=1;