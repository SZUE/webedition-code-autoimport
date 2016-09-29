CREATE TABLE ###TBLPREFIX###tblformmailblock (
  id mediumint unsigned NOT NULL auto_increment,
  ip varchar(40) NOT NULL,
  blockedUntil int NOT NULL,
  PRIMARY KEY  (id),
  KEY ipblockeduntil (blockedUntil),
  UNIQUE KEY ip (ip)
) ENGINE=MyISAM;