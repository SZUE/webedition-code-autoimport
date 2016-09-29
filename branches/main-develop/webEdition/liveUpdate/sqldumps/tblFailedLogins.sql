CREATE TABLE ###TBLPREFIX###tblFailedLogins (
  ID bigint unsigned NOT NULL AUTO_INCREMENT,
  Username varchar(64) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  isValid enum('true','false') NOT NULL DEFAULT 'true',
  LoginDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserTable enum('tblUser','tblWebUser') NOT NULL,
  Servername varchar(150) NOT NULL,
  Port mediumint NOT NULL,
  Script varchar(150) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY IP (LoginDate,UserTable,IP),
  KEY user (UserTable,Username,isValid,LoginDate)
) ENGINE=MyISAM;