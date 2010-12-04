CREATE TABLE tblWebUserAutoLogin (
  AutoLoginID varchar(64) NOT NULL default '',
  WebUserID bigint(20) NOT NULL default '0',
  LastIp varchar(40)NOT NULL DEFAULT '',
  LastLogin timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (AutoLoginID,WebUserID),
  KEY `LastLogin` (`LastLogin`)
) ENGINE=MyISAM;
