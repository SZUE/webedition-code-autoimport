###UPDATEDROPCOL(ID,###TBLPREFIX###tblCaptcha)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblCaptcha (
  IP BINARY(16) NOT NULL default '',
	agent char(32) NOT NULL default '',
	code varchar(255) NOT NULL default '',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (IP,code),
	KEY created (created)
) ENGINE=MyISAM;
