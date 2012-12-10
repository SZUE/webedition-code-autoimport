CREATE TABLE tblWebUserSessions (
  SessionID varchar(32) NOT NULL default '',
  SessionIp varchar(40)NOT NULL DEFAULT '',
  WebUserID bigint  NOT NULL default '0',
  WebUserGroup varchar(255) NOT NULL DEFAULT '',
  WebUserDescription varchar(255) NOT NULL DEFAULT '',
  Browser varchar(255) NOT NULL DEFAULT '',
  Referrer varchar(255) NOT NULL DEFAULT '',
  LastLogin datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  LastAccess datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PageID bigint  NOT NULL default '0',
  ObjectID bigint  NOT NULL DEFAULT '0',
  SessionAutologin tinyint  NOT NULL DEFAULT '0',
  PRIMARY KEY  (SessionID)
) 
CREATE INDEX idx_tblWebUserSessions_WebUserID ON tblWebUserSessions(WebUserID);
CREATE INDEX idx_tblWebUserSessions_LastAccess ON tblWebUserSessions(LastAccess);