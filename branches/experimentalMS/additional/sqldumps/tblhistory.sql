CREATE TABLE tblhistory (
  ID bigint  NOT NULL IDENTITY(1,1),
  DID bigint  NOT NULL default '0',
  DocumentTable varchar(64) NOT NULL default '',
  
  ContentType varchar(30) CHECK(ContentType IN ('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile')) NOT NULL,
  ModDate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  Act varchar(30) CHECK(Act IN('save')) NOT NULL default 'save',
  UserName varchar(64) NOT NULL default '',
  PRIMARY KEY  (ID)
) 
CREATE INDEX idx_tblhistory_UserName ON tblhistory(UserName,DocumentTable);
CREATE INDEX idx_tblhistoryy_DID ON tblhistory(DID,DocumentTable,ModDate);