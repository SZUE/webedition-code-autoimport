###UPDATEDROPCOL(freeDoc,###TBLPREFIX###tblLock)###
/* query separator */
###UPDATEDROPCOL(freeDocUID,###TBLPREFIX###tblLock)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblLock (
  ID int unsigned NOT NULL default '0',
  UserID int unsigned NOT NULL default '0',
  sessionID char(64) NOT NULL default '',
  lockTime datetime NOT NULL,
  tbl enum('tblFile','tblObject','tblTemplates','tblObjectFiles','tblVFile') NOT NULL,
  PRIMARY KEY (ID,tbl),
  KEY UserID (UserID,sessionID),
  KEY lockTime (lockTime)
) ENGINE=MyISAM;