CREATE TABLE ###TBLPREFIX###tblPasswordReset (
  ID bigint unsigned NOT NULL,
  UserTable enum('tblUser','tblWebUser') NOT NULL,
	expires datetime NOT NULL,
	token char(25) NOT NULL,
	password varchar(255) NOT NULL default '',
	loginPage int unsigned NOT NULL,
  PRIMARY KEY (ID,UserTable)
) ENGINE=MyISAM;