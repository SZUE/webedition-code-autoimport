CREATE TABLE tblshopvats (
  id int  NOT NULL IDENTITY(1,1),
  "text" varchar(255) NOT NULL default '',
  vat varchar(16) NOT NULL default '',
  standard tinyint  NOT NULL default '0',
  PRIMARY KEY  (id)
) 
