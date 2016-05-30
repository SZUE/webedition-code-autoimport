CREATE TABLE ###TBLPREFIX###tblformmaillog (
  id bigint unsigned NOT NULL auto_increment,
  ip varchar(40) NOT NULL,
  unixTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (id),
  KEY ipwhen (ip,unixTime)
) ENGINE=MyISAM;