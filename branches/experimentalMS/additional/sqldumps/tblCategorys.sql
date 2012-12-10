CREATE TABLE tblCategorys (
  ID int  NOT NULL IDENTITY(1,1),
  Category varchar(64) NOT NULL default '',
  "Text" varchar(64) default NULL,
  "Path" varchar(255) default NULL,
  ParentID bigint  default NULL,
  IsFolder tinyint   default NULL,
  Icon varchar(64) default NULL,
  Catfields text NOT NULL,
  PRIMARY KEY  (ID)
) 

CREATE INDEX idx_tblCategorys_Path ON tblCategorys(Path);