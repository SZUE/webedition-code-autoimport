CREATE TABLE ###TBLPREFIX###tblTemporaryDoc (
  DocumentID bigint unsigned NOT NULL default '0',
  DocumentObject longtext NOT NULL,
  DocTable enum('tblFile','tblObjectFiles') NOT NULL,
	saved TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Active tinyint unsigned NOT NULL default '0',
  PRIMARY KEY (`DocTable`,`DocumentID`,`Active`)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(UnixTimestamp,###TBLPREFIX###tblTemporaryDoc)UPDATE ###TBLPREFIX###tblTemporaryDoc SET saved=FROM_UNIXTIME(UnixTimestamp) ;###
/* query separator */
###UPDATEDROPCOL(UnixTimestamp,###TBLPREFIX###tblTemporaryDoc)###
