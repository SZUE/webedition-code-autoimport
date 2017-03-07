CREATE TABLE ###TBLPREFIX###tblvalidationservices (
  PK_tblvalidationservices smallint unsigned NOT NULL auto_increment,
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
  PRIMARY KEY  (PK_tblvalidationservices)
) ENGINE=MyISAM;