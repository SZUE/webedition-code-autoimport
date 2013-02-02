###UPDATEONLY###UPDATE ###TBLPREFIX###tblPrefs SET cockpit_rss_feed_url='http://www.webedition.org/de/feeds/aktuelles.xml' WHERE cockpit_rss_feed_url LIKE "%www.webedition.de/presse/%"
/* query separator */

CREATE TABLE ###TBLPREFIX###tblPrefs (
  userID bigint(20) unsigned NOT NULL default '0',
  key varchar(100) NOT NULL default '',
	value text NOT NULL,
  PRIMARY KEY (`userID`,key)
) ENGINE=MyISAM;

