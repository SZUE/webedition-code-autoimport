CREATE TABLE tblFile (
  ID int  NOT NULL IDENTITY(1,1),
  ParentID int  NOT NULL default '0',
  "Text" varchar(255) NOT NULL default '',
  
  Icon varchar(25) CHECK( Icon IN ('','pdf.gif','zip.gif','word.gif','excel.gif','powerpoint.gif','prog.gif','link.gif','image.gif','html.gif','we_dokument.gif','javascript.gif','css.gif','htaccess.gif','folder.gif','flashmovie.gif','quicktime.gif','odg.gif')) NOT NULL,
  IsFolder tinyint  NOT NULL default '0',
  
  ContentType varchar(30) CHECK(ContentType IN ('','image/*','text/html','text/webedition','text/js','text/css','text/htaccess','text/plain','folder','application/x-shockwave-flash','application/*','video/quicktime','application/*','text/xml')) NOT NULL default '',
  CreationDate int  NOT NULL default '0',
  ModDate int  NOT NULL default '0',
  "Path" varchar(255) NOT NULL default '',
  TemplateID int  NOT NULL default '0',
  temp_template_id int  NOT NULL default '0',
  "Filename" varchar(255) NOT NULL default '',
  Extension varchar(16) NOT NULL default '',
  IsDynamic tinyint  NOT NULL default '0',
  IsSearchable tinyint  NOT NULL default '0',
  DocType tinyint NOT NULL default '0',
  temp_doc_type tinyint NOT NULL default '0',
  ClassName varchar(64) NOT NULL default '',
  Category text NULL default NULL,
  temp_category text NULL default NULL,
  Deleted int  NOT NULL default '0',
  Published int  NOT NULL default '0',
  CreatorID bigint  NOT NULL default '0',
  ModifierID bigint  NOT NULL default '0',
  RestrictOwners tinyint  NOT NULL default '0',
  Owners varchar(255) NOT NULL default '',
  OwnersReadOnly text NOT NULL default '',
/*  documentArray text NOT NULL, */
  "Language" varchar(5) NOT NULL default '',
  WebUserID bigint  NOT NULL default '0',
  listview tinyint  NOT NULL default '0',
  InGlossar tinyint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
CREATE INDEX idx_tblFile_Path ON tblFile(Path);
CREATE INDEX idx_tblFile_WebUserID ON tblFile(WebUserID);