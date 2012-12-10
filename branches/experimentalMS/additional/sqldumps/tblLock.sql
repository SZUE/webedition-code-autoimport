CREATE TABLE tblLock (
  ID bigint  NOT NULL default '0',
  UserID int  NOT NULL default '0',
  sessionID varchar(64) NOT NULL default '',
  lockTime datetime NOT NULL,
  tbl varchar(32) NOT NULL default '',
  PRIMARY KEY (ID,tbl)
) 
CREATE INDEX idx_tblLock_UserID ON tblLock(UserID,sessionID);
CREATE INDEX idx_tblLock_lockTime ON tblLock(lockTime);