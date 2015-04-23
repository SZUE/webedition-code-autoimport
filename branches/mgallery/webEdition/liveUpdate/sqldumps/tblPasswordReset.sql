CREATE TABLE ###TBLPREFIX###tblPasswordReset (
  ID bigint(20) unsigned NOT NULL,
  UserTable enum('tblUser','tblWebUser') NOT NULL,
	expires	datetime NOT NULL,
	token char(25) NOT NULL,
	password varchar(255) NOT NULL default '',
	loginPage int(11) unsigned NOT NULL,
  PRIMARY KEY (ID,UserTable)
) ENGINE=MyISAM;