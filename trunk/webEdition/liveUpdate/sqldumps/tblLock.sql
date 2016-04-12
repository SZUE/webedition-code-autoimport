CREATE TABLE ###TBLPREFIX###tblLock (
  ID int unsigned NOT NULL default '0',
  UserID int unsigned NOT NULL default '0',
  sessionID char(64) NOT NULL default '',
  lockTime datetime NOT NULL,
  tbl enum('tblFile','tblObject','tblTemplates','tblObjectFiles') NOT NULL,
	freeDoc text NOT NULL,
	freeDocUID int unsigned NOT NULL default '0',
  PRIMARY KEY (ID,tbl),
  KEY UserID (UserID,sessionID),
  KEY lockTime (lockTime)
) ENGINE=MyISAM;