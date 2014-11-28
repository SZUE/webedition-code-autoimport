CREATE TABLE ###TBLPREFIX###tblNewsletterLog (
  ID bigint(20) unsigned NOT NULL auto_increment,
  NewsletterID int(11) unsigned NOT NULL default '0',
  stamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Log ENUM('log_start_send','retry','log_continue_send','log_end_send','mail_sent','mail_failed','email_malformed','domain_nok','email_is_black','log_campagne_reset','log_save_newsletter') NOT NULL,
  Param varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY NewsletterID (NewsletterID,stamp)
) ENGINE=MyISAM;
