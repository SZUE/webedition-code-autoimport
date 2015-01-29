/*FIXME: mv to tblSettings*/
###ONCOL(id,###TBLPREFIX###tblNewsletterPrefs)CREATE TEMPORARY TABLE IF NOT EXISTS _newNewsPref( pref_name varchar(30) NOT NULL default '',  pref_value longtext NOT NULL,  PRIMARY KEY name (pref_name(30)))ENGINE = MYISAM;###
/* query separator */
###ONCOL(id,###TBLPREFIX###tblNewsletterPrefs)INSERT IGNORE INTO _newNewsPref SELECT DISTINCT * FROM ###TBLPREFIX###tblNewsletterPrefs GROUP BY pref_name;###
/* query separator */
###ONCOL(id,###TBLPREFIX###tblNewsletterPrefs)TRUNCATE ###TBLPREFIX###tblNewsletterPrefs;###
/* query separator */
###ONCOL(id,###TBLPREFIX###tblNewsletterPrefs)INSERT INTO ###TBLPREFIX###tblNewsletterPrefs SELECT * FROM _newNewsPref;###
/* query separator */
###ONCOL(id,###TBLPREFIX###tblNewsletterPrefs)DROP TEMPORARY TABLE IF EXISTS _newNewsPref;###
/* query separator */
###UPDATEDROPCOL(ID,###TBLPREFIX###tblNewsletterPrefs)###
/* query separator */


CREATE TABLE ###TBLPREFIX###tblNewsletterPrefs (
  pref_name varchar(30) NOT NULL default '',
  pref_value longtext NOT NULL,
  PRIMARY KEY (pref_name)
) ENGINE=MyISAM;
