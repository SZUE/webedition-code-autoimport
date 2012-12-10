CREATE TABLE tblTemplates (
  ID int  NOT NULL IDENTITY(1,1),
  ParentID int  NOT NULL default '0',
  "Text" varchar(255) NOT NULL default '',
  Icon varchar(25) CHECK(Icon IN ('folder.gif','we_template.gif')) NOT NULL default 'we_template.gif',
  IsFolder tinyint  NOT NULL default '0',
  ContentType varchar(25) CHECK(ContentType IN  ('folder','text/weTmpl')) NOT NULL default 'text/weTmpl',
  CreationDate int  NOT NULL default '0',
  ModDate int  NOT NULL default '0',
  "Path" varchar(255) NOT NULL default '',
  "Filename" varchar(64) NOT NULL default '',
  Extension varchar(25) CHECK(Extension IN('','.tmpl')) NOT NULL default '',
  ClassName varchar(64) NOT NULL default '',
  Deleted int  NOT NULL default '0',
  Owners varchar(255) default NULL,
  RestrictOwners tinyint  default NULL,
  OwnersReadOnly text,
  CreatorID bigint  NOT NULL default '0',
  ModifierID bigint  NOT NULL default '0',
  MasterTemplateID bigint  NOT NULL default '0',
  IncludedTemplates varchar(255) NOT NULL default '',
  CacheType varchar(25) CHECK(CacheType IN ('','none','tag','document','full')) NOT NULL default 'none',
  CacheLifeTime int  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
CREATE INDEX idx_tblTemplates_ParentID ON tblTemplates(ParentID,Filename);
CREATE INDEX idx_tblTemplates_MasterTemplateID ON tblTemplates(MasterTemplateID);
CREATE INDEX idx_tblTemplates_IncludedTemplates ON tblTemplates(IncludedTemplates);

