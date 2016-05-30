CREATE TABLE ###TBLPREFIX###tblNewsletterGroup (
  ID bigint unsigned NOT NULL auto_increment,
  NewsletterID bigint NOT NULL default '0',
  Emails longtext NOT NULL,
  Customers longtext NOT NULL,
  SendAll tinyint unsigned NOT NULL default '0',
  Filter blob NOT NULL,
  Extern longtext,
  PRIMARY KEY (ID),
  KEY NewsletterID (NewsletterID)
) ENGINE=MyISAM;