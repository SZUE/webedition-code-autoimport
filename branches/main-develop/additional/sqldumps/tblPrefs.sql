CREATE TABLE ###TBLPREFIX###tblPrefs (
  userID bigint(20) unsigned NOT NULL default '0',
  `key` varchar(100) NOT NULL default '',
	value text NOT NULL,
  PRIMARY KEY (`userID`,`key`),
	KEY lookup (`key`)
) ENGINE=MyISAM;

/* query separator */
###UPDATEONLY###UPDATE ###TBLPREFIX###tblPrefs SET value='http://www.webedition.org/de/feeds/aktuelles.xml' WHERE `key`="cockpit_rss_feed_url" AND value LIKE "%www.webedition.de/presse/%"
