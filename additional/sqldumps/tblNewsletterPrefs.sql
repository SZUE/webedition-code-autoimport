CREATE TEMPORARY TABLE IF NOT EXISTS _newNewsPref(
  pref_name varchar(30) NOT NULL default '',
  pref_value longtext NOT NULL,
  PRIMARY KEY name (pref_name(30))
)ENGINE = MYISAM;
/* query separator */
INSERT IGNORE INTO _newNewsPref SELECT DISTINCT * FROM tblNewsletterPrefs GROUP BY pref_name;
/* query separator */
TRUNCATE tblNewsletterPrefs;
/* query separator */
INSERT INTO tblNewsletterPrefs SELECT * FROM _newNewsPref;
/* query separator */

DROP TEMPORARY TABLE IF EXISTS _newNewsPref;
/* query separator */

CREATE TABLE tblNewsletterPrefs (
  pref_name varchar(30) NOT NULL default '',
  pref_value longtext NOT NULL,
  PRIMARY KEY pref_name (pref_name(30))
) ENGINE=MyISAM;
