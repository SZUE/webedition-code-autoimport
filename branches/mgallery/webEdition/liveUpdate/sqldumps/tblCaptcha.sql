###UPDATEDROPCOL(ID,###TBLPREFIX###tblCaptcha)###
/* query separator */
###UPDATEDROPCOL(created,###TBLPREFIX###tblCaptcha)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblCaptcha (
  IP BINARY(16) NOT NULL default '',
	agent binary(16) NOT NULL,
	typ enum('captcha','token') NOT NULL default 'captcha',
	code char(32) NOT NULL default '',
  valid timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (IP,typ,code),
	KEY valid (valid)
) ENGINE=Memory;