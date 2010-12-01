CREATE TABLE tblWebUserAutoLogin (
  AutoLoginID varchar(64) NOT NULL default '',
  WebUserID bigint(20) NOT NULL default '0',
  LastIp varchar(40)NOT NULL DEFAULT '',
  LastLogin varchar(24) NOT NULL default '',
  PRIMARY KEY  (AutoLoginID,WebUserID),
  KEY `LastLogin` (`LastLogin`)
) ENGINE=MyISAM;
