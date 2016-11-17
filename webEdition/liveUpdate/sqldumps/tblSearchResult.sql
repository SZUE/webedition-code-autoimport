CREATE TABLE ###TBLPREFIX###tblSearchResult (
	UID INT unsigned NOT NULL,
	docID INT unsigned NOT NULL,
	docTable enum('tblFile','tblObjectFiles','tblTemplates','tblObject','tblVFile') NOT NULL,
	Text VARCHAR(255) NOT NULL,
	Path VARCHAR(800) NOT NULL,
	ParentID INT unsigned NOT NULL,
	IsFolder TINYINT unsigned NOT NULL,
	IsProtected TINYINT unsigned NOT NULL,
	temp_template_id INT unsigned NOT NULL,
	TemplateID INT unsigned NOT NULL,
	ContentType VARCHAR(32) NOT NULL,
	SiteTitle VARCHAR(255) NOT NULL,
	CreationDate INT unsigned NOT NULL,
	CreatorID INT unsigned NOT NULL,
	ModDate INT unsigned NOT NULL,
	Published INT unsigned NOT NULL,
	Extension VARCHAR(16) NOT NULL,
	TableID INT unsigned NOT NULL,
	VersionID INT unsigned NOT NULL,
	media_alt VARCHAR(255) NOT NULL,
	media_title VARCHAR(255) NOT NULL,
	media_filesize INT unsigned NOT NULL,
	IsUsed TINYINT unsigned NOT NULL,
	remTable VARCHAR(32) NOT NULL,
	remCT VARCHAR(32) NOT NULL,
	remClass MEDIUMINT unsigned NOT NULL,
	PRIMARY KEY (UID,docTable,docID)
)ENGINE=MyISAM;

/* query separator */
TRUNCATE ###TBLPREFIX###tblSearchResult;
/* query separator */
DROP TABLE IF EXISTS SEARCH_TEMP_TABLE;