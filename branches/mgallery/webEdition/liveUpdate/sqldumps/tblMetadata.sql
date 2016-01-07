###UPDATEDROPCOL(ID,###TBLPREFIX###tblMetadata)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblMetadata (
  `tag` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL default 'textfield',
  `importFrom` varchar(255) NOT NULL,
  `mode` enum('none','manual','auto') NOT NULL default 'none',
  `csv` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag`)
) ENGINE=MyISAM;