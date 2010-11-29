CREATE TABLE tblWebUserSessions (
  SessionID varchar(32) NOT NULL default '',
  SessionIp varchar(40)NOT NULL DEFAULT '',
  WebUserID bigint(20) NOT NULL default '0',
  WebUserGroup varchar(255) NOT NULL DEFAULT '',
  WebUserDescription varchar(255) NOT NULL DEFAULT '',
  Browser varchar(255) NOT NULL DEFAULT '',
  LastLogin varchar(24) NOT NULL default '',
  LastAccess varchar(24) NOT NULL default '',
  PageID bigint(20) NOT NULL default '0',
  SessionAutologin tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (SessionID),
  KEY `WebUserID` (`WebUserID`)
) ENGINE=MyISAM;
