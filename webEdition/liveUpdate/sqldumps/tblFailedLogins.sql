###UPDATEDROPCOL(ID,###TBLPREFIX###tblFailedLogins)###
/* query separator */
###UPDATEDROPKEY(IP,###TBLPREFIX###tblFailedLogins)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblFailedLogins (
  Username varchar(64) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  isValid enum('true','false') NOT NULL DEFAULT 'true',
  LoginDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserTable enum('tblUser','tblWebUser') NOT NULL,
  Servername varchar(150) NOT NULL,
  Port mediumint NOT NULL,
  Script varchar(150) NOT NULL,
  PRIMARY KEY (LoginDate,UserTable,IP),
  KEY user (UserTable,Username,isValid,LoginDate)
) ENGINE=MyISAM;