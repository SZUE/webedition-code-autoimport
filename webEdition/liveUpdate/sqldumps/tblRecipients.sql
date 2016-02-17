CREATE TABLE ###TBLPREFIX###tblRecipients (
  ID smallint unsigned NOT NULL auto_increment,
  Email varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY Email (Email)
) ENGINE=MyISAM;