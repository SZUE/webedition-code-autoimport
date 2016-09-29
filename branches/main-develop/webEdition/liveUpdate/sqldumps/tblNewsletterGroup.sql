CREATE TABLE ###TBLPREFIX###tblNewsletterGroup (
  ID mediumint unsigned NOT NULL auto_increment,
  NewsletterID mediumint unsigned NOT NULL default '0',
  Emails longtext NOT NULL,
  Customers longtext NOT NULL,
  SendAll tinyint unsigned NOT NULL default '0',
  Filter blob NOT NULL,
  Extern longtext,
  PRIMARY KEY (ID),
  KEY NewsletterID (NewsletterID)
) ENGINE=MyISAM;