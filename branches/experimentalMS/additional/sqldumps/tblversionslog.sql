CREATE TABLE tblversionslog (
  ID bigint  NOT NULL IDENTITY(1,1),
  "timestamp" int  NOT NULL,
  "action" int  NOT NULL,
  userID int  NOT NULL,
  data text NOT NULL,
  PRIMARY KEY  (ID)
)