CREATE TABLE tblLangLink (
  ID int  NOT NULL IDENTITY(1,1),
  DID int  NOT NULL default '0',
  DLocale varchar(5) NOT NULL default '',
  IsFolder tinyint  NOT NULL default '0',
  IsObject tinyint  NOT NULL default '0',
  LDID int  NOT NULL default '0',
  Locale varchar(5) NOT NULL default '',
  
  DocumentTable varchar(25) CHECK(DocumentTable IN ('tblFile','tblObjectFile','tblDocTypes')) NOT NULL,
  PRIMARY KEY (ID),

  CONSTRAINT DID UNIQUE(DID,DLocale,IsObject,IsFolder,Locale,DocumentTable),
  CONSTRAINT DLocale UNIQUE(DLocale,IsFolder,IsObject,LDID,Locale,DocumentTable)
) 

CREATE INDEX idx_tblLangLink_LDID ON tblLangLink(LDID,DocumentTable,Locale);