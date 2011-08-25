CREATE TABLE ###TBLPREFIX###tblTemporaryDoc (
  ID bigint(20) unsigned NOT NULL auto_increment,
  DocumentID bigint(20) unsigned NOT NULL default '0',
  DocumentObject longtext NOT NULL,
  DocTable varchar(64) NOT NULL default '',
  UnixTimestamp bigint(20) unsigned NOT NULL default '0',
  Active tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY DocumentID (DocumentID),
  KEY DocTable (DocTable,Active)
) ENGINE=MyISAM;
