CREATE TABLE tblnavigation (
  ID bigint  NOT NULL IDENTITY(1,1),
  ParentID bigint  NOT NULL default '0',
  "Path" varchar(900) NOT NULL default '',/*   War 2000 */
  Published int  NOT NULL DEFAULT '1',
  "Text" varchar(800) NOT NULL default '',
  Display varchar(800) NOT NULL default '',

  ContentType varchar(25) CHECK(ContentType IN('weNavigation')) NOT NULL default 'weNavigation',
  Icon varchar(25) CHECK(Icon IN('folder.gif','link.gif')) NOT NULL,
  
  IsFolder tinyint  NOT NULL default '0',
  TitleField varchar(255) NOT NULL default '',
  IconID bigint  NOT NULL default '0',
  
  Selection varchar(25) CHECK(Selection IN ('dynamic','nodynamic','static')) NOT NULL,
  LinkID bigint  NOT NULL default '0',
  CurrentOnUrlPar tinyint  NOT NULL DEFAULT '0',
  CurrentOnAnker tinyint  NOT NULL DEFAULT '0',
  
  SelectionType varchar(25) CHECK(SelectionType IN('urlLink','category','catLink','classname','objLink','docLink','doctype')) NOT NULL default 'docLink',
  FolderID bigint  NOT NULL default '0',
  DocTypeID tinyint  NOT NULL,
  ClassID bigint  NOT NULL default '0',
  Categories text NOT NULL,
  Sort text NOT NULL,
  ShowCount tinyint  NOT NULL default '0',
  Ordn tinyint  NOT NULL default '0',
  Depended tinyint  NOT NULL default '0',
  WorkspaceID bigint NOT NULL default '-1',
  CatParameter varchar(255) NOT NULL default '',
  Parameter varchar(255) NOT NULL default '',
  LinkSelection varchar(255) NOT NULL default '',
  Url varchar(255) NOT NULL default '',
  UrlID bigint  NOT NULL default '0',
  Charset varchar(255) NOT NULL default '',
  Attributes text NOT NULL,
  FolderSelection  varchar(25) CHECK(FolderSelection IN ('docLink','objLink','urlLink')) NOT NULL,
  FolderWsID bigint  NOT NULL default '0',
  FolderParameter varchar(255) NOT NULL default '',
  FolderUrl varchar(255) NOT NULL default '',
  LimitAccess tinyint  NOT NULL default '0',
  AllCustomers tinyint  NOT NULL default '1',
  ApplyFilter tinyint  NOT NULL default '0',
  Customers text NOT NULL,
  CustomerFilter text NOT NULL,
  BlackList text NOT NULL,
  WhiteList text NOT NULL,
  UseDocumentFilter tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 

CREATE INDEX idx_tblnavigation_ParentID ON tblnavigation(ParentID);
CREATE INDEX idx_tblnavigation_LinkID ON tblnavigation(LinkID);
CREATE INDEX idx_tblnavigation_Path ON tblnavigation(Path);
