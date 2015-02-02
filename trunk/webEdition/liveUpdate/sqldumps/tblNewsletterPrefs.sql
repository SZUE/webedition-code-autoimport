/*FIXME: mv to tblSettings*/
###UPDATEDROPCOL(ID,###TBLPREFIX###tblNewsletterPrefs)###
/* query separator */
###UPDATEDROPCOL(id,###TBLPREFIX###tblNewsletterPrefs)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblNewsletterPrefs (
  pref_name varchar(30) NOT NULL default '',
  pref_value longtext NOT NULL,
  PRIMARY KEY (pref_name)
) ENGINE=MyISAM;

/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblNewsletterPrefs)ALTER IGNORE TABLE ###TBLPREFIX###tblNewsletterPrefs ADD PRIMARY KEY (pref_name);###
