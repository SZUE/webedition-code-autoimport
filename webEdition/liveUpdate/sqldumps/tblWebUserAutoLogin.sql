CREATE TABLE ###TBLPREFIX###tblWebUserAutoLogin (
  AutoLoginID binary(20) NOT NULL,
  WebUserID bigint unsigned NOT NULL default '0',
  LastIp varchar(40) NOT NULL DEFAULT '',
  LastLogin timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (AutoLoginID,WebUserID),
  KEY `LastLogin` (`LastLogin`),
	KEY WebUserID(WebUserID)
)  ENGINE=MyISAM;