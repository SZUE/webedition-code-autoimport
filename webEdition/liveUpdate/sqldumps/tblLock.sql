###UPDATEDROPCOL(freeDoc,###TBLPREFIX###tblLock)###
/* query separator */
###UPDATEDROPCOL(freeDocUID,###TBLPREFIX###tblLock)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblLock (
  ID int unsigned NOT NULL default '0',
  UserID int unsigned NOT NULL default '0',
  sessionID binary(20) NOT NULL,
  lockTime datetime NOT NULL,
  tbl enum('tblFile','tblObject','tblTemplates','tblObjectFiles','tblVFile') NOT NULL,
	releaseRequestID int default NULL,
	releaseRequestText text default NULL,
	releaseRequestForce datetime NULL,
	releaseRequestReply text default NULL,
  PRIMARY KEY (ID,tbl),
  KEY UserID (UserID,sessionID),
	KEY releaseRequest(releaseRequestID),
  KEY lockTime (lockTime)
) ENGINE=MyISAM;