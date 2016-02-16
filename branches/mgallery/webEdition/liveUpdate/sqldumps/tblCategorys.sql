###UPDATEDROPCOL(Icon,###TBLPREFIX###tblCategorys)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblCategorys (
  ID int unsigned NOT NULL auto_increment,
  Category varchar(64) NOT NULL default '',
  `Text` varchar(64) default NULL,
  Path varchar(255) default NULL,
  ParentID int unsigned default NULL,
	Title varchar(255) NOT NULL default '',
	Description longtext NOT NULL,
	`Language` varchar(5) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY Path (Path)
) ENGINE=MyISAM;

/*drop IsFolder */
