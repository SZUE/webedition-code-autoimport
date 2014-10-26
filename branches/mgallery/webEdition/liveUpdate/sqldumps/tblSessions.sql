CREATE TABLE ###TBLPREFIX###tblSessions (
  sessionName char(15) NOT NULL,
  session_id binary(20) NOT NULL,
  lockTime timestamp NULL DEFAULT NULL,
  lockid char(23) NOT NULL,
  touch timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  session_data longblob NOT NULL,
  PRIMARY KEY (sessionName,session_id)
) ENGINE=MyISAM;
