###UPDATEONLY###UPDATE ###TBLPREFIX###tblTemporaryDoc SET DocTable="tblFile" WHERE DocTable="###TBLPREFIX###tblFile";
###UPDATEONLY###UPDATE ###TBLPREFIX###tblTemporaryDoc SET DocTable="tblObjectFiles" WHERE DocTable="###TBLPREFIX###tblObjectFiles";
/* query separator */
CREATE TABLE ###TBLPREFIX###tblTemporaryDoc (
  DocumentID bigint(20) unsigned NOT NULL default '0',
  DocumentObject longtext NOT NULL,
  DocTable enum('tblFile','tblObjectFiles') NOT NULL default '',
  UnixTimestamp bigint(20) unsigned NOT NULL default '0',
  Active tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`DocTable`,`DocumentID`,`Active`)
) ENGINE=MyISAM;
