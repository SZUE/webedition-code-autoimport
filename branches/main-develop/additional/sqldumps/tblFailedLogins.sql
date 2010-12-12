CREATE TABLE tblFailedLogins (
  ID  bigint(20) NOT NULL AUTO_INCREMENT,
  Username varchar(64) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  LoginDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY IP (IP,LoginDate)
) ENGINE=MyISAM;
