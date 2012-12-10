CREATE TABLE tblContent (
  ID bigint  NOT NULL IDENTITY(1,1),
  BDID int  NOT NULL default '0',
  Dat text,
  IsBinary tinyint  NOT NULL default '0',
  AutoBR char(3) NOT NULL default '',
  LanguageID int  NOT NULL default '0',
  PRIMARY KEY  (ID)

) 
CREATE INDEX idx_tblContent_BDID ON tblContent(BDID);