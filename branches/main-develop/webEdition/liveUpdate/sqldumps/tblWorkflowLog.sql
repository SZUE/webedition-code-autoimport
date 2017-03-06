CREATE TABLE ###TBLPREFIX###tblWorkflowLog (
  ID mediumint unsigned NOT NULL auto_increment,
  RefID int unsigned NOT NULL default '0',
  docTable enum('tblWorkflowLog') NOT NULL default 'tblWorkflowLog',
  userID int unsigned NOT NULL default '0',
  Date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	Event enum('approve','approve_force','decline','decline_force','doc_finished','doc_finished_force','doc_inserted','doc_removed') NOT NULL,
  Description tinytext NOT NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

/* query separator */
###ONCOL(logDate,###TBLPREFIX###tblWorkflowLog) UPDATE ###TBLPREFIX###tblWorkflowLog SET Date=FROM_UNIXTIME(logDate);###

/* query separator */
###ONCOL(Type,###TBLPREFIX###tblWorkflowLog) UPDATE ###TBLPREFIX###tblWorkflowLog SET Event=ELT(Type,'approve','approve_force','decline','decline_force','doc_finished','doc_finished_force','doc_inserted','doc_removed');###

/* query separator */
###UPDATEDROPCOL(Type,###TBLPREFIX###tblWorkflowLog)###

/* query separator */
###UPDATEDROPCOL(logDate,###TBLPREFIX###tblWorkflowLog)###
