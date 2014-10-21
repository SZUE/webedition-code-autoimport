###UPDATEDROPCOL(ID,###TBLPREFIX###tblMetadata)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblMetadata (
  `tag` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `importFrom` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag`)
)  ENGINE=MyISAM;
