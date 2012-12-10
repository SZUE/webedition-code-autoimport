CREATE TABLE tblCleanUp (
  ID int  NOT NULL IDENTITY(1,1),
  "Path" varchar(255) NOT NULL default '',
  Date int  NOT NULL default '0',
  PRIMARY KEY  (ID),
  CONSTRAINT Path UNIQUE(Path)
) 

CREATE INDEX idx_tblCleanUp_Date ON tblCleanUp(Date);