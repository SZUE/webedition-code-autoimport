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
  DID int(11) unsigned NOT NULL default '0',
  `Text` text NOT NULL,
  OID bigint(20) unsigned NOT NULL default '0',
  Workspace varchar(1000) NOT NULL default '',
  WorkspaceID int(11) unsigned NOT NULL default '0',
  Category varchar(255) NOT NULL default '',
  ClassID int(11) unsigned NOT NULL default '0',
  Doctype smallint(6) unsigned NOT NULL default '0',
  Title varchar(255) NOT NULL default '',
  Description text NOT NULL,
  Path varchar(255) NOT NULL default '',
  Language varchar(5) default NULL,
  PRIMARY KEY (`OID`,WorkspaceID,`DID`),
  KEY `DID` (`DID`)
) ENGINE=MyISAM;
