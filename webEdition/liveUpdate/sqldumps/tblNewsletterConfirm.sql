CREATE TABLE ###TBLPREFIX###tblNewsletterConfirm (
  confirmID binary(16) NOT NULL default '',
  subscribe_mail varchar(255) NOT NULL default '',
  subscribe_html tinyint unsigned NOT NULL default '0',
  subscribe_salutation varchar(255) NOT NULL default '',
  subscribe_title varchar(255) NOT NULL default '',
  subscribe_firstname varchar(255) NOT NULL default '',
  subscribe_lastname varchar(255) NOT NULL default '',
  lists text NOT NULL,
  expires int unsigned NOT NULL default '0',
  PRIMARY KEY (confirmID),
  KEY expires (expires),
  KEY subscribe (subscribe_mail(20))
) ENGINE=MyISAM;