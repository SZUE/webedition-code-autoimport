CREATE TABLE ###TBLPREFIX###tblNewsletterBlock (
  ID bigint unsigned NOT NULL auto_increment,
  NewsletterID bigint unsigned NOT NULL default '0',
  Groups varchar(255) NOT NULL default '',
  `Type` tinyint unsigned NOT NULL default '0',
  LinkID bigint unsigned NOT NULL default '0',
  Field varchar(255) NOT NULL default '',
  Source longtext NOT NULL,
  Html longtext NOT NULL,
  Pack tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY NewsletterID (NewsletterID)
) ENGINE=MyISAM;