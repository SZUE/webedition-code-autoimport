CREATE TABLE tblWebUserAutoLogin (
  AutoLoginID varchar(64) NOT NULL default '',
  WebUserID bigint  NOT NULL default '0',
  LastIp varchar(40)NOT NULL DEFAULT '',
  LastLogin datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (AutoLoginID,WebUserID)
) 
CREATE INDEX idx_tblWebUserAutoLogin_LastLogin ON tblWebUserAutoLogin(LastLogin);