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
###UPDATEDROPKEY(search,###TBLPREFIX###tblIndex)###
/* query separator */
###ONCOL(Workspace,###TBLPREFIX###tblIndex)ALTER TABLE ###TBLPREFIX###tblIndex DROP PRIMARY KEY;###
/* query separator */
###ONCOL(Workspace,###TBLPREFIX###tblIndex)ALTER TABLE ###TBLPREFIX###tblIndex DROP COLUMN ID;###
/* query separator */
###ONCOL(Workspace,###TBLPREFIX###tblIndex)ALTER TABLE ###TBLPREFIX###tblIndex ADD ID int unsigned NOT NULL default '0' FIRST;###
/* query separator */
###ONCOL(Workspace,###TBLPREFIX###tblIndex)UPDATE ###TBLPREFIX###tblIndex SET ID=IF(OID>0,OID,DID);###
/* query separator */
###UPDATEDROPCOL(Workspace,###TBLPREFIX###tblIndex)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblIndex (
	ID int unsigned NOT NULL default '0',
  DID int unsigned NOT NULL default '0',
	OID int unsigned NOT NULL default '0',
  WorkspaceID int unsigned NOT NULL default '0',
  `Text` text NOT NULL,
  Category varchar(255) NOT NULL default '',
  ClassID int unsigned NOT NULL default '0',
  Doctype smallint unsigned NOT NULL default '0',
  Title varchar(255) NOT NULL default '',
  Description text NOT NULL,
  Path varchar(1000) NOT NULL default '',
  Language varchar(5) default NULL,
  PRIMARY KEY (ID,ClassID,WorkspaceID),
	FULLTEXT Text (Text)
) ENGINE=MyISAM;

/* query separator */
###ONKEYFAILED(PRIMARY,###TBLPREFIX###tblIndex)ALTER IGNORE TABLE ###TBLPREFIX###tblIndex ADD PRIMARY KEY (ID,ClassID,WorkspaceID);###

/* query separator */
###UPDATEONLY###DROP TABLE IF EXISTS ###TBLPREFIX###tblIndex_Backup;