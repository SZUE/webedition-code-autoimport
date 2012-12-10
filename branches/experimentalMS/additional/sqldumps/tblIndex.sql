CREATE TABLE tblIndex (
  DID int  NOT NULL default '0',
  "Text" text NOT NULL,
  ID bigint  NOT NULL default '0',
  OID bigint  NOT NULL default '0',
  BText text NOT NULL,
  Workspace varchar(1000) NOT NULL default '',
  WorkspaceID bigint  NOT NULL default '0',
  Category varchar(255) NOT NULL default '',
  ClassID bigint  NOT NULL default '0',
  Doctype bigint  NOT NULL default '0',
  Title varchar(255) NOT NULL default '',
  "Description" text NOT NULL,
  "Path" varchar(255) NOT NULL default '',
  "Language" varchar(5) default NULL,
  PRIMARY KEY (DID,OID,WorkspaceID)
) 
CREATE INDEX idx_tblIndex_OID ON tblIndex(OID);