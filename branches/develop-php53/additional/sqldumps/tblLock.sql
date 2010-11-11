CREATE TABLE tblLock (
  ID bigint(20) NOT NULL default '0',
  UserID bigint(20) NOT NULL default '0',
  tbl varchar(32) NOT NULL default '',
  PRIMARY KEY (ID,tbl),
  KEY UserID (UserID)
) ENGINE=MyISAM;
