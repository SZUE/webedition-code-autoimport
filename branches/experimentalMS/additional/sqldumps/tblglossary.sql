CREATE TABLE tblglossary (
  ID int  NOT NULL IDENTITY(1,1),
  "Path" varchar(255) default NULL,
  IsFolder tinyint  default NULL,
  Icon varchar(25) CHECK( Icon IN ('folder.gif','prog.gif')) NOT NULL,
  "Text" varchar(255) NOT NULL default '',
  "Type"  varchar(25) CHECK(Type IN ('abbreviation','acronym','foreignword','link','textreplacement')) NOT NULL default 'abbreviation',
  "Language" varchar(5) NOT NULL default '',
  Title text NOT NULL,
  Attributes text NOT NULL,
  Linked tinyint  NOT NULL default '0',
  "Description" text NOT NULL,
  CreationDate int  NOT NULL default '0',
  ModDate int  NOT NULL default '0',
  Published int  NOT NULL default '0',
  CreatorID bigint  NOT NULL default '0',
  ModifierID bigint  NOT NULL default '0',
  PRIMARY KEY  (ID)
) 
CREATE INDEX idx_tblglossary_valid ON tblglossary(Language,Published);