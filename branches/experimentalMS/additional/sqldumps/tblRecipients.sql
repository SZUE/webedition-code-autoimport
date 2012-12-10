CREATE TABLE tblRecipients (
  ID bigint  NOT NULL IDENTITY(1,1),
  Email varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  CONSTRAINT Email UNIQUE(Email)
) 
