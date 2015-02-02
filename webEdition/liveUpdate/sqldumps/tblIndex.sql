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

CREATE TABLE ###TBLPREFIX###tblIndex (
  DID int(11) unsigned NULL default NULL,
  OID bigint(20) unsigned NULL default NULL,
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
  PRIMARY KEY (`OID`,WorkspaceID,`DID`),
  UNIQUE KEY `UDID` (`DID`)
) ENGINE=MyISAM;

/* query separator */
###ONKEYFAILED(UDID,###TBLPREFIX###tblIndex)UPDATE ###TBLPREFIX###tblIndex SET DID=NULL WHERE DID=0;###
/* query separator */
###ONKEYFAILED(UDID,###TBLPREFIX###tblIndex)UPDATE ###TBLPREFIX###tblIndex SET OID=NULL WHERE OID=0;###
/* query separator */
###ONKEYFAILED(UDID,###TBLPREFIX###tblIndex)ALTER IGNORE TABLE ###TBLPREFIX###tblIndex ADD UNIQUE KEY `UDID` (`DID`);###
/* query separator */
###UPDATEDROPKEY(DID,###TBLPREFIX###tblIndex)###
