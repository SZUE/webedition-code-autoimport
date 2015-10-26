###UPDATEONLY### UPDATE ###TBLPREFIX###tblWebUser SET Path=CONCAT("/",Username) WHERE SUBSTR(Path,1,1)!="/"
/* query separator */

CREATE TABLE ###TBLPREFIX###tblWebUser (
  ID bigint(20) unsigned NOT NULL auto_increment,
  Username varchar(255) NOT NULL default '',
  `Password` varchar(255) NOT NULL default '',
  Forename varchar(128) NOT NULL default '',
  Surname varchar(128) NOT NULL default '',
  LoginDenied tinyint(1) unsigned NOT NULL default '0',
  MemberSince int(10) unsigned NOT NULL default '0',
  LastLogin int(10) unsigned NOT NULL default '0',
  LastAccess int(10) unsigned NOT NULL default '0',
  AutoLoginDenied tinyint(1) unsigned NOT NULL default '0',
  AutoLogin tinyint(1) unsigned NOT NULL default '0',
  ModifyDate int(10) unsigned NOT NULL default '0',
  `ModifiedBy` enum('','backend','frontend','external') NOT NULL default '',
  ParentID tinyint(0) unsigned NOT NULL default '0',
  Path varchar(255) default NULL,
  IsFolder tinyint(1) unsigned NOT NULL default '0',
  Icon ENUM('customer.gif') NOT NULL default 'customer.gif',
  `Text` varchar(255) default NULL,
  `Newsletter_Ok` enum('','ja','0','1','2') NOT NULL,
  PRIMARY KEY  (ID),
  UNIQUE KEY `Username` (`Username`),
  KEY Surname (Surname(3))
) ENGINE=MyISAM;
