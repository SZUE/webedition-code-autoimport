CREATE TABLE ###TBLPREFIX###tblFailedLogins (
  ID  bigint unsigned NOT NULL AUTO_INCREMENT,
  Username varchar(64) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  isValid enum('true','false') NOT NULL DEFAULT 'true',
  LoginDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserTable enum('tblUser','tblWebUser') NOT NULL,
  Servername TINYTEXT NOT NULL,
  Port smallint unsigned NOT NULL DEFAULT '0',
  Script TINYTEXT NOT NULL,
  PRIMARY KEY (`ID`),
  KEY IP (LoginDate,UserTable,IP),
  KEY user (UserTable,Username,isValid,LoginDate)
) ENGINE=MyISAM;