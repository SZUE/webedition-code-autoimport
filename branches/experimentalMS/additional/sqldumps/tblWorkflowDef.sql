CREATE TABLE tblWorkflowDef (
  ID int  NOT NULL IDENTITY(1,1),
  "Text" varchar(255) NOT NULL default '',
  "Type" bigint  NOT NULL default '0',
  Folders varchar(255) NOT NULL default '',
  DocType varchar(255) NOT NULL default '',
  "Objects" varchar(255) NOT NULL default '',
  ObjectFileFolders varchar(255) NOT NULL default '',
  Categories text NOT NULL,
  ObjCategories varchar(255) NOT NULL default '',
  "Status" tinyint  NOT NULL default '0',
  EmailPath tinyint  NOT NULL DEFAULT '0',
  LastStepAutoPublish tinyint  NOT NULL DEFAULT '0',
  PRIMARY KEY  (ID)
) 