###UPDATEDROPCOL(Icon,###TBLPREFIX###tblglossary)###
/* query separator */
CREATE TABLE ###TBLPREFIX###tblglossary (
  ID INT unsigned NOT NULL auto_increment,
  Path VARCHAR(255) DEFAULT NULL,
  IsFolder tinyint unsigned DEFAULT NULL,
  `Text` VARCHAR(255) NOT NULL DEFAULT '',
  `Type` enum('abbreviation','acronym','foreignword','link','textreplacement') NOT NULL DEFAULT 'abbreviation',
  Language CHAR(5) NOT NULL DEFAULT '',
  Title tinytext NOT NULL,
  Attributes text NOT NULL,
  Linked tinyINT unsigned NOT NULL DEFAULT '0',
  Description text NOT NULL,
	Fullword tinyINT unsigned DEFAULT '1',
  CreationDate INT unsigned NOT NULL DEFAULT '0',
  ModDate INT unsigned NOT NULL DEFAULT '0',
  Published INT unsigned NOT NULL DEFAULT '0',
  CreatorID INT unsigned NOT NULL DEFAULT '0',
  ModifierID INT unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (ID),
  KEY valid (Language,Published)
) ENGINE=MyISAM;
/* query separator */
UPDATE ###TBLPREFIX###tblglossary SET Language="de_DE" WHERE Language="";
