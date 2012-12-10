CREATE TABLE tblvotinglog (
  id bigint  NOT NULL IDENTITY(1,1),
  votingsession varchar(255) NOT NULL,
  voting bigint  NOT NULL,
  "time" int  NOT NULL,
  ip varchar(255) NOT NULL,
  agent varchar(255) NOT NULL,
  userid bigint  NOT NULL DEFAULT '0',
  cookie tinyint  NOT NULL,
  fallback tinyint  NOT NULL,
  "status" tinyint  NOT NULL,
  answer varchar(255) NOT NULL,
  answertext text NOT NULL,
  successor bigint  NOT NULL DEFAULT '0',
  additionalfields text NOT NULL,
  PRIMARY KEY  (id)
)