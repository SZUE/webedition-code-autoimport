###UPDATEDROPCOL(Icon,###TBLPREFIX###tblCategorys)###
/* query separator */
###UPDATEDROPCOL(IsFolder,###TBLPREFIX###tblCategorys)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblCategorys (
  ID mediumint unsigned NOT NULL auto_increment,
  Category varchar(64) NOT NULL default '',
  `Text` varchar(64) default NULL,
  Path varchar(800) default NULL,
  ParentID int unsigned default NULL,
	Title varchar(255) NOT NULL default '',
	Description longtext NOT NULL,
	`Language` varchar(5) NOT NULL default '',
  PRIMARY KEY  (ID),
	UNIQUE KEY ParentID (ParentID,Text),
  KEY Path (Path(250))
) ENGINE=MyISAM;

/*drop IsFolder */
