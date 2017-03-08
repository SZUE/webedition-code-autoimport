###UPDATEDROPCOL(ID,###TBLPREFIX###tblCleanUp)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblCleanUp (
  Path varchar(400) NOT NULL default '',
  `Date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (Path),
  KEY `Date` (`Date`)
) ENGINE=MyISAM;

/* query separator */
###UPDATEDROPKEY(Path,###TBLPREFIX###tblCleanUp)###
