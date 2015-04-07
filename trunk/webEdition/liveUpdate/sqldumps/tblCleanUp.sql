###UPDATEDROPCOL(ID,###TBLPREFIX###tblCleanUp)###
/* query separator */
###UPDATEDROPKEY(Path,###TBLPREFIX###tblCleanUp)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblCleanUp (
  Path char(255) NOT NULL default '',
  `Date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Path),
  KEY `Date` (`Date`)
) ENGINE=MyISAM;
