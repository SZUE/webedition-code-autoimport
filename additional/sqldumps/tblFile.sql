CREATE TABLE tblFile (
  ID int(11) NOT NULL auto_increment,
  ParentID int(11) NOT NULL default '0',
  `Text` varchar(255) NOT NULL default '',
  Icon varchar(64) NOT NULL default '',
  IsFolder tinyint(1) NOT NULL default '0',
  ContentType varchar(32) NOT NULL default '',
  CreationDate int(11) NOT NULL default '0',
  ModDate int(11) NOT NULL default '0',
  `Path` varchar(255) NOT NULL default '',
  TemplateID int(11) NOT NULL default '0',
  temp_template_id int(11) NOT NULL default '0',
  Filename varchar(255) NOT NULL default '',
  Extension varchar(16) NOT NULL default '',
  IsDynamic tinyint(1) NOT NULL default '0',
  IsSearchable tinyint(1) NOT NULL default '0',
  DocType varchar(64) NOT NULL default '',
  temp_doc_type varchar(32) NOT NULL default '',
  ClassName varchar(64) NOT NULL default '',
  Category varchar(255) default NULL,
  temp_category varchar(255) default NULL,
  Deleted int(11) NOT NULL default '0',
  Published int(11) NOT NULL default '0',
  CreatorID bigint(20) NOT NULL default '0',
  ModifierID bigint(20) NOT NULL default '0',
  RestrictOwners tinyint(1) NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  documentArray text NOT NULL,
  `Language` varchar(5) NOT NULL default '',
  WebUserID bigint(20) NOT NULL default '0',
  listview tinyint(1) NOT NULL default '0',
  InGlossar tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY Path (Path),
  KEY WebUserID (WebUserID)
) ENGINE=MyISAM;
