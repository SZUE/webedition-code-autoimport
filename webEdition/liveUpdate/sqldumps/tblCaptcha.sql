CREATE TABLE ###TBLPREFIX###tblCaptcha (
  ID  bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  IP char(40) NOT NULL default '',
	agent varchar(100) NOT NULL default '',
	code varchar(255) NOT NULL default '',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
	KEY created (created),
	KEY IP(IP)
) ENGINE=MyISAM;
