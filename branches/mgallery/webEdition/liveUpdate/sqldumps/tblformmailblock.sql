CREATE TABLE ###TBLPREFIX###tblformmailblock (
  id bigint unsigned NOT NULL auto_increment,
  ip varchar(40) NOT NULL,
  blockedUntil int NOT NULL,
  PRIMARY KEY  (id),
  KEY ipblockeduntil (blockedUntil),
  UNIQUE KEY ip (ip)
) ENGINE=MyISAM;