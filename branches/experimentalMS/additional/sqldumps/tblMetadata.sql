CREATE TABLE tblMetadata (
  id int  NOT NULL IDENTITY(1,1),
  tag varchar(255) NOT NULL,
  "type" varchar(255) NOT NULL,
  importFrom varchar(255) NOT NULL,
  PRIMARY KEY  (id)
)  
