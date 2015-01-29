###UPDATEDROPCOL(UserTable,###TBLPREFIX###UserTable)###
/* query separator */
###UPDATEDROPCOL(expires,###TBLPREFIX###UserTable)###
/* query separator */
###UPDATEDROPCOL(token,###TBLPREFIX###UserTable)###
/* query separator */
###UPDATEDROPCOL(password,###TBLPREFIX###UserTable)###
/* query separator */
###UPDATEDROPCOL(loginPage,###TBLPREFIX###UserTable)###
/* query separator */

CREATE TABLE ###TBLPREFIX###UserTable (
  ID bigint(20) unsigned NOT NULL auto_increment,
  NewsletterID int(11) unsigned NOT NULL default '0',
  stamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Log ENUM('log_start_send','retry','log_continue_send','log_end_send','mail_sent','mail_failed','email_malformed','domain_nok','email_is_black','log_campagne_reset','log_save_newsletter') NOT NULL,
  Param varchar(255) NOT NULL default '',
  PRIMARY KEY (ID),
  KEY NewsletterID (NewsletterID,stamp)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(LogTime,###TBLPREFIX###tblNewsletterLog) UPDATE ###TBLPREFIX###tblNewsletterLog SET stamp=FROM_UNIXTIME(LogTime);###

/* query separator */
###UPDATEDROPCOL(LogTime,###TBLPREFIX###UserTable)###
