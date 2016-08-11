###ONCOL(Icon,###TBLPREFIX###tblUser) UPDATE ###TBLPREFIX###tblUser SET IsFolder=1 WHERE Type=1;###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblUser)###
/* query separator */
###UPDATEDROPCOL(Portal,###TBLPREFIX###tblUser)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblUser (
  ID int unsigned NOT NULL auto_increment,
  ParentID int unsigned NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Path varchar(255) NOT NULL default '',
  IsFolder tinyint unsigned NOT NULL default '0',
  `Type` tinyint unsigned NOT NULL default '0',
  username varchar(255) NOT NULL default '',
  passwd varchar(255) NOT NULL default '',
  UseSalt tinyint unsigned NOT NULL default '0',
  LoginDenied tinyint unsigned NOT NULL default '0',
  Permissions text NOT NULL,
  ParentPerms tinyint unsigned NOT NULL default '0',
  Alias int unsigned NOT NULL default '0',
  CreatorID int unsigned NOT NULL default '0',
  CreateDate int unsigned NOT NULL default '0',
  ModifierID int unsigned NOT NULL default '0',
  ModifyDate int unsigned NOT NULL default '0',
  Ping datetime default NULL,
  workSpace TEXT NOT NULL default '',
  workSpaceDef TEXT NOT NULL default '',
  workSpaceTmp TEXT NOT NULL default '',
  workSpaceNav TEXT NOT NULL default '',
  workSpaceObj TEXT NOT NULL default '',
  workSpaceNwl TEXT NOT NULL default '',
  workSpaceCust TEXT NOT NULL default '',
  ParentWs tinyint unsigned NOT NULL default '0',
  ParentWst tinyint unsigned NOT NULL default '0',
  ParentWsn tinyint unsigned NOT NULL default '0',
  ParentWso tinyint unsigned NOT NULL default '0',
  ParentWsnl tinyint unsigned NOT NULL default '0',
  ParentWsCust tinyint unsigned NOT NULL default '0',
  Salutation varchar(32) NOT NULL default '',
  `First` tinytext NOT NULL default '',
  `Second` tinytext NOT NULL default '',
  Address tinytext NOT NULL default '',
  HouseNo varchar(11) NOT NULL default '',
  City tinytext NOT NULL default '',
  PLZ varchar(32) NOT NULL default '',
  State tinytext NOT NULL default '',
  Country tinytext NOT NULL default '',
  Tel_preselection varchar(11) NOT NULL default '',
  Telephone varchar(32) NOT NULL default '',
  Fax_preselection varchar(11) NOT NULL default '',
  Fax varchar(32) NOT NULL default '',
  Handy varchar(32) NOT NULL default '',
  Email tinytext NOT NULL default '',
  Description TEXT NOT NULL,
  PRIMARY KEY (ID),
  KEY Ping (Ping),
  KEY Alias (Alias),
  UNIQUE KEY username (username)
) ENGINE=MyISAM;
/* query separator */
###INSTALLONLY###INSERT INTO ###TBLPREFIX###tblUser SET ID=1,Text='admin',Path='/admin',username='admin',passwd='c0e024d9200b5705bc4804722636378a',Permissions='a:1:{s:13:"ADMINISTRATOR";i:1;}',CreateDate=UNIX_TIMESTAMP();
