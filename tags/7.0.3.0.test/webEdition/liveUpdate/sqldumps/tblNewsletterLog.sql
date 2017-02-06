###UPDATEDROPCOL(UserTable,###TBLPREFIX###tblNewsletterLog)###
/* query separator */
###UPDATEDROPCOL(expires,###TBLPREFIX###tblNewsletterLog)###
/* query separator */
###UPDATEDROPCOL(token,###TBLPREFIX###tblNewsletterLog)###
/* query separator */
###UPDATEDROPCOL(password,###TBLPREFIX###tblNewsletterLog)###
/* query separator */
###UPDATEDROPCOL(loginPage,###TBLPREFIX###tblNewsletterLog)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblNewsletterLog (
  ID bigint unsigned NOT NULL auto_increment,
  NewsletterID int unsigned NOT NULL default '0',
  stamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Log ENUM('log_start_send','retry','log_continue_send','log_end_send','mail_sent','mail_failed','email_malformed','domain_nok','email_is_black','log_campaign_reset','log_save_newsletter') NOT NULL,
  Param varchar(255) NOT NULL default '',
  PRIMARY KEY (ID),
  KEY NewsletterID (NewsletterID,stamp)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(LogTime,###TBLPREFIX###tblNewsletterLog) UPDATE ###TBLPREFIX###tblNewsletterLog SET stamp=FROM_UNIXTIME(LogTime);###

/* query separator */
###UPDATEDROPCOL(LogTime,###TBLPREFIX###tblNewsletterLog)###

/* query separator */
###UPDATEONLY###DROP TABLE IF EXISTS ###TBLPREFIX###UserTable;