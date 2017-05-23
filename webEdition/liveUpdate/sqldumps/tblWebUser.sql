###ONCOL(Icon,###TBLPREFIX###tblWebUser) UPDATE ###TBLPREFIX###tblWebUser SET Path=CONCAT("/",Username) WHERE SUBSTR(Path,1,1)!="/";###
/* query separator */
###UPDATEDROPCOL(Icon,###TBLPREFIX###tblWebUser)###
/* query separator */
###UPDATEDROPCOL(Text,###TBLPREFIX###tblWebUser)###
/* query separator */
###UPDATEDROPCOL(IsFolder,###TBLPREFIX###tblWebUser)###
/* query separator */
###UPDATEDROPCOL(ParentID,###TBLPREFIX###tblWebUser)###
/* query separator */
###UPDATEDROPCOL(Path,###TBLPREFIX###tblWebUser)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblWebUser (
  ID bigint unsigned NOT NULL auto_increment,
  Username varchar(255) NOT NULL default '',
  `Password` varchar(255) NOT NULL default '',
  Forename varchar(128) NOT NULL default '',
  Surname varchar(128) NOT NULL default '',
  LoginDenied tinyint unsigned NOT NULL default '0',
  MemberSince int unsigned NOT NULL default '0',
  LastLogin int unsigned NOT NULL default '0',
  LastAccess int unsigned NOT NULL default '0',
  AutoLogin tinyint unsigned NOT NULL default '0',
  AutoLoginDenied tinyint unsigned NOT NULL default '0',
  ModifyDate int unsigned NOT NULL default '0',
  ModifiedBy enum('','backend','frontend','external') NOT NULL default '',
  Newsletter_Ok enum('','ja','0','1','2') NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY Username (Username),
  KEY Surname (Surname(3))
)  ENGINE=MyISAM;