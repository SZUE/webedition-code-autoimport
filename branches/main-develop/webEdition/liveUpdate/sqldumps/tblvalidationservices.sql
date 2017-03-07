###ONCOL(PK_tblvalidationservices,###TBLPREFIX###tblvalidationservices) ALTER TABLE ###TBLPREFIX###tblvalidationservices CHANGE COLUMN PK_tblvalidationservices ID smallint unsigned NOT NULL auto_increment;###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblvalidationservices (
  ID smallint unsigned NOT NULL auto_increment,
  category enum('xhtml','links','css','accessibility') NOT NULL,
  name tinytext NOT NULL,
  host tinytext NOT NULL,
  path tinytext NOT NULL,
  method enum('post','get') NOT NULL,
  varname tinytext NOT NULL default '',
  checkvia enum('url','fileupload') NOT NULL,
  additionalVars tinytext NOT NULL default '',
  ctype enum('text/html','text/css') NOT NULL,
  fileEndings tinytext NOT NULL default '',
  active tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;