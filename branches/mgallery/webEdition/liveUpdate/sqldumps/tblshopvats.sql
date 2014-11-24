CREATE TABLE ###TBLPREFIX###tblshopvats (
  id int(11) unsigned NOT NULL auto_increment,
  `text` varchar(255) NOT NULL default '',
  vat varchar(16) NOT NULL default '',
  standard tinyint(4) unsigned NOT NULL default '0',
  territory varchar(5) NOT NULL default '',
  textProvince varchar(32) NOT NULL default '',
  categories varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
