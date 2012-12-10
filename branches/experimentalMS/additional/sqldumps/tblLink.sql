CREATE TABLE tblLink (
  DID int  NOT NULL default '0',
  CID int  NOT NULL default '0',
  Type varchar(16) NOT NULL default '',
  Name varchar(255) NOT NULL default '',
  DocumentTable varchar(25) CHECK(DocumentTable IN ('tblFile','tblTemplates')) NOT NULL,
  PRIMARY KEY (CID)
) 
CREATE INDEX idx_tblLink_DID ON tblLink(DID,DocumentTable);
CREATE INDEX idx_tblLink_Name ON tblLink(Name);
CREATE INDEX idx_tblLink_Type ON tblLink(Type);