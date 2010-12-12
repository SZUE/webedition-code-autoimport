CREATE TABLE tblFailedLogins (
  ID bigint(20) NOT NULL default '0',
  Username varchar(64) NOT NULL default '',
  `Password` varchar(32) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  LoginDate int(11) NOT NULL default '0',
  PRIMARY KEY (`ID`),
  KEY IP (IP,LoginDate)
) ENGINE=MyISAM;
