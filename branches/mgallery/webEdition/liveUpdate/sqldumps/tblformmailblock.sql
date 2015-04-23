CREATE TABLE ###TBLPREFIX###tblformmailblock (
  id bigint(20) unsigned NOT NULL auto_increment,
  ip varchar(40) NOT NULL,
  blockedUntil int(11) NOT NULL,
  PRIMARY KEY  (id),
  KEY ipblockeduntil (blockedUntil),
  UNIQUE KEY ip (ip)
) ENGINE=MyISAM;