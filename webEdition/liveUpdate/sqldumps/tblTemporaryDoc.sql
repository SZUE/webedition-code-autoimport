###UPDATEONLY###DELETE FROM ###TBLPREFIX###tblTemporaryDoc WHERE Active=0;
/* query separator */
###UPDATEONLY###UPDATE ###TBLPREFIX###tblTemporaryDoc SET DocTable="tblFile" WHERE DocTable="###TBLPREFIX###tblFile";
/* query separator */
###UPDATEONLY###UPDATE ###TBLPREFIX###tblTemporaryDoc SET DocTable="tblObjectFiles" WHERE DocTable="###TBLPREFIX###tblObjectFiles";
/* query separator */
###UPDATEDROPCOL(ID,###TBLPREFIX###tblTemporaryDoc)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblTemporaryDoc (
  DocumentID bigint unsigned NOT NULL default '0',
  DocumentObject longtext NOT NULL,
  DocTable enum('tblFile','tblObjectFiles') NOT NULL,
  UnixTimestamp int unsigned NOT NULL default '0',
  Active tinyint unsigned NOT NULL default '0',
  PRIMARY KEY (`DocTable`,`DocumentID`,`Active`)
) ENGINE=MyISAM;