###UPDATEDROPCOL(JavaScript,###TBLPREFIX###tblDocTypes)###
/* query separator */
###UPDATEDROPCOL(Notify,###TBLPREFIX###tblDocTypes)###
/* query separator */
###UPDATEDROPCOL(NotifyTemplateID,###TBLPREFIX###tblDocTypes)###
/* query separator */
###UPDATEDROPCOL(NotifySubject,###TBLPREFIX###tblDocTypes)###
/* query separator */
###UPDATEDROPCOL(NotifyOnChange,###TBLPREFIX###tblDocTypes)###
/* query separator */
###UPDATEDROPCOL(Deleted,###TBLPREFIX###tblDocTypes)###
/* query separator */
###UPDATEDROPCOL(LockID,###TBLPREFIX###tblDocTypes)###
/* query separator */
###ONCOL(ContentTable,###TBLPREFIX###tblDocTypes)ALTER TABLE ###TBLPREFIX###tblDocTypes CHANGE SubDir SubDir ENUM('0','1','2','3','4','-','y','ym','ymd') NOT NULL DEFAULT '-';###
/* query separator */
###ONCOL(ContentTable,###TBLPREFIX###tblDocTypes)UPDATE ###TBLPREFIX###tblDocTypes SET SubDir='' WHERE SubDir='0';###
/* query separator */
###ONCOL(ContentTable,###TBLPREFIX###tblDocTypes)UPDATE ###TBLPREFIX###tblDocTypes SET SubDir='y' WHERE SubDir='1';###
/* query separator */
###ONCOL(ContentTable,###TBLPREFIX###tblDocTypes)UPDATE ###TBLPREFIX###tblDocTypes SET SubDir='ym' WHERE SubDir='2';###
/* query separator */
###ONCOL(ContentTable,###TBLPREFIX###tblDocTypes)UPDATE ###TBLPREFIX###tblDocTypes SET SubDir='ymd' WHERE SubDir='3';###
/* query separator */
###UPDATEDROPCOL(ContentTable,###TBLPREFIX###tblDocTypes)###
/* query separator */

CREATE TABLE ###TBLPREFIX###tblDocTypes (
  ID mediumint unsigned NOT NULL auto_increment,
  DocType varchar(64) NOT NULL default '',
  Extension varchar(16) NOT NULL default '',
  ParentID int unsigned NOT NULL default '0',
  ParentPath text NOT NULL default '',
  SubDir enum('-','y','ym','ymd') NOT NULL default '-',
  TemplateID int unsigned NOT NULL default '0',
  IsDynamic tinyint unsigned NOT NULL default '0',
  IsSearchable tinyint unsigned NOT NULL default '0',
  Templates text NOT NULL default '',
  Category text default NULL,
  Language char(5) default NULL,
  PRIMARY KEY  (ID),
	UNIQUE KEY ParentID (ParentID,DocType)
) ENGINE=MyISAM;