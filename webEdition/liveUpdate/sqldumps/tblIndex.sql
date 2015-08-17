###UPDATEDROPCOL(viewid,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPCOL(Timestamp,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPCOL(IP,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPCOL(Referer,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPCOL(Page,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPKEY(DID,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPKEY(OID,###TBLPREFIX###tblIndex)###
/* query separator */
###UPDATEDROPKEY(UDID,###TBLPREFIX###tblIndex)###
/* query separator */
###ONKEYFAILED(search,###TBLPREFIX###tblIndex)ALTER TABLE ###TBLPREFIX###tblIndex DROP PRIMARY KEY;###
/* query separator */
###ONKEYFAILED(search,###TBLPREFIX###tblIndex)ALTER TABLE ###TBLPREFIX###tblIndex DROP COLUMN ID;###
/* query separator */
###ONKEYFAILED(search,###TBLPREFIX###tblIndex)ALTER TABLE ###TBLPREFIX###tblIndex ADD ID int unsigned NOT NULL default '0' FIRST;###
/* query separator */
###ONKEYFAILED(search,###TBLPREFIX###tblIndex)UPDATE ###TBLPREFIX###tblIndex SET ID=IF(OID>0,OID,DID);###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblIndex (
	ID int(11) unsigned NOT NULL default '0',
  DID int(11) unsigned NOT NULL default '0',
	OID int(11) unsigned NOT NULL default '0',
  WorkspaceID int(11) unsigned NOT NULL default '0',
  `Text` text NOT NULL,
  Workspace varchar(1000) NOT NULL default '',
  Category varchar(255) NOT NULL default '',
  ClassID int(11) unsigned NOT NULL default '0',
  Doctype smallint(6) unsigned NOT NULL default '0',
  Title varchar(255) NOT NULL default '',
  Description text NOT NULL,
  Path varchar(255) NOT NULL default '',
  Language varchar(5) default NULL,
  PRIMARY KEY (ID,WorkspaceID,ClassID),
	UNIQUE `documents` (`ID`, `ClassID`, `Doctype`),
	KEY wsp (Workspace),
	FULLTEXT Text (Text)
) ENGINE=MyISAM;

/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblIndex)ALTER IGNORE TABLE ###TBLPREFIX###tblIndex ADD PRIMARY KEY (ID,WorkspaceID,ClassID);###

/* query separator */
###ONKEYFAILED(documents,###TBLPREFIX###tblIndex)ALTER IGNORE TABLE ###TBLPREFIX###tblIndex ADD UNIQUE `documents` (`ID`, `ClassID`, `Doctype`);###

/* query separator */
###UPDATEONLY###DROP TABLE IF EXISTS ###TBLPREFIX###tblIndex_Backup;