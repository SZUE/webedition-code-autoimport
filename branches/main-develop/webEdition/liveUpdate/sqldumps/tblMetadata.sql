###UPDATEDROPCOL(ID,###TBLPREFIX###tblMetadata)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblMetadata (
  tag varchar(255) NOT NULL,
  `type` enum('textfield','textarea','date','wysiwyg') NOT NULL default 'textfield',
  importFrom varchar(255) NOT NULL,
  mode enum('none','manual','auto') NOT NULL default 'none',
  csv tinyint unsigned NOT NULL default '0',
	closed tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag`)
) ENGINE=MyISAM;