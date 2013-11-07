CREATE TABLE ###TBLPREFIX###tblLock (
  ID int(11) unsigned NOT NULL default '0',
  UserID int(11) unsigned NOT NULL default '0',
  sessionID char(64) NOT NULL default '',
  lockTime datetime NOT NULL,
  tbl enum('tblFile','tblObject','tblTemplates','tblObjectFiles') NOT NULL,
  PRIMARY KEY (ID,tbl),
  KEY UserID (UserID,sessionID),
  KEY lockTime (lockTime)
) ENGINE=MyISAM;
