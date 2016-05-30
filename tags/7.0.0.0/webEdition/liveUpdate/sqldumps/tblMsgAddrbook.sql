CREATE TABLE ###TBLPREFIX###tblMsgAddrbook (
  ID int unsigned NOT NULL auto_increment,
  UserID int unsigned default NULL,
  strMsgType varchar(255) default NULL,
  strID varchar(255) default NULL,
  strAlias varchar(255) NOT NULL default '',
  strFirstname varchar(255) default NULL,
  strSurname varchar(255) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;