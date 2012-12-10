CREATE TABLE tblObjectFiles (
  ID int  NOT NULL IDENTITY(1,1) PRIMARY KEY ,
  ParentID int  NOT NULL default '0',
  "Text" varchar(255) NOT NULL default '',
  Icon varchar(25) CHECK(Icon IN ('class_folder.gif','folder.gif','objectFile.gif')) NOT NULL,
  IsFolder tinyint  NOT NULL default '0',
  ContentType varchar(25) CHECK(ContentType IN ('folder','objectFile')) NOT NULL,
  CreationDate int  NOT NULL default '0',
  ModDate int  NOT NULL default '0',
  "Path" varchar(255) NOT NULL default '',
  Url varchar(255) NOT NULL default '',
  TriggerID bigint  NOT NULL default '0',
  CreatorID bigint  NOT NULL default '0',
  ModifierID bigint  NOT NULL default '0',
  RestrictOwners tinyint  NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL,
  Workspaces varchar(1000) NOT NULL default '',
  ExtraWorkspaces varchar(1000) NOT NULL default '',
  ExtraWorkspacesSelected varchar(1000) NOT NULL default '',
  Templates varchar(255) NOT NULL default '',
  ExtraTemplates varchar(255) NOT NULL default '',
  TableID bigint  NOT NULL default '0',
  ObjectID bigint  NOT NULL default '0',
  Category text NOT NULL default '',
  ClassName varchar(25) CHECK(ClassName IN ('we_class_folder','we_objectFile')) NOT NULL,
  IsClassFolder tinyint  NOT NULL default '0',
  IsNotEditable tinyint  NOT NULL default '0',
  Published int  NOT NULL default '0',
  IsSearchable tinyint  NOT NULL default '1',
  Charset varchar(64) default NULL,
  "Language" varchar(5) default NULL,
  WebUserID bigint  NOT NULL
) 
CREATE INDEX idx_tblObjectFiles_Path ON tblObjectFiles(Path);
CREATE INDEX idx_tblObjectFiles_WebUserID ON tblObjectFiles(WebUserID);
CREATE INDEX idx_tblObjectFiles_TableID ON tblObjectFiles(TableID);
CREATE INDEX idx_tblObjectFiles_Url ON tblObjectFiles(Url);