
CREATE TABLE tblWebUser (
  ID bigint  NOT NULL IDENTITY(1,1),
  Username varchar(255) NOT NULL default '',
  "Password" varchar(255) NOT NULL default '',
  Anrede_Anrede varchar(25) CHECK(Anrede_Anrede IN ('','Herr','Frau')) NOT NULL,
  Anrede_Titel varchar(200) NOT NULL default '',
  Forename varchar(128) NOT NULL default '',
  Surname varchar(128) NOT NULL default '',
  LoginDenied tinyint  NOT NULL default '0',
  MemberSince int  NOT NULL default '0',
  LastLogin int  NOT NULL default '0',
  LastAccess int  NOT NULL default '0',
  AutoLoginDenied tinyint  NOT NULL default '0',
  AutoLogin tinyint  NOT NULL default '0',
  ModifyDate bigint  NOT NULL default '0',
  ModifiedBy varchar(25) CHECK(ModifiedBy IN('','backend','frontend','external')) NOT NULL default'',
  ParentID bigint  NOT NULL default '0',
  "Path" varchar(255) default NULL,
  IsFolder tinyint  default NULL,
  Icon varchar(255) default NULL,
  "Text" varchar(255) default NULL,
  Newsletter_Ok varchar(25) CHECK(Newsletter_Ok IN ('','ja','0','1','2')) NOT NULL,
  Newsletter_HTMLNewsletter varchar(25) CHECK(Newsletter_HTMLNewsletter  IN('','ja','0','1','2')) NOT NULL,
  PRIMARY KEY  (ID),
  CONSTRAINT Username UNIQUE(Username)
  
) 
CREATE INDEX idx_tblWebUser_Surname ON tblWebUser(Surname);
CREATE INDEX idx_tblWebUser_Username ON tblWebUser(Username);