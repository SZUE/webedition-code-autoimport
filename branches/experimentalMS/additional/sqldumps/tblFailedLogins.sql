CREATE TABLE tblFailedLogins (
  ID  bigint  NOT NULL IDENTITY(1,1),
  Username varchar(64) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  LoginDate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserTable varchar(25) CHECK( UserTable IN('tblUser','tblWebUser')) NOT NULL,
  Servername varchar(150) NOT NULL,
  Port int NOT NULL,
  Script varchar(150) NOT NULL,
  PRIMARY KEY (ID)
) 
CREATE INDEX idx_blFailedLogins_IP ON blFailedLogins(LoginDate,UserTable,IP);
CREATE INDEX idx_blFailedLogins_user ON blFailedLogins(LoginDate,Username);
