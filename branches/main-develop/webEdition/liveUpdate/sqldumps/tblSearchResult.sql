CREATE TABLE ###TBLPREFIX###tblSearchResult (
	UID INT unsigned NOT NULL,
	docID INT unsigned NOT NULL,
	docTable ENUM('tblFile','tblObjectFiles','tblTemplates','tblObject','tblVFile') NOT NULL,
	Text tinytext NOT NULL,
	Path text NOT NULL,
	ParentID INT unsigned NOT NULL,
	IsFolder TINYINT unsigned NOT NULL,
	IsProtected TINYINT unsigned NOT NULL,
	temp_template_id INT unsigned NOT NULL,
	TemplateID INT unsigned NOT NULL,
	ContentType tinytext NOT NULL,
	SiteTitle tinytext NOT NULL,
	CreationDate INT unsigned NOT NULL,
	CreatorID INT unsigned NOT NULL,
	ModDate INT unsigned NOT NULL,
	Published INT unsigned NOT NULL,
	Extension tinytext NOT NULL,
	TableID INT unsigned NOT NULL,
	VersionID INT unsigned NOT NULL,
	media_alt tinytext NOT NULL,
	media_title tinytext NOT NULL,
	media_filesize INT unsigned NOT NULL,
	IsUsed TINYINT unsigned NOT NULL,
	remTable tinytext NOT NULL,
	remCT tinytext NOT NULL,
	remClass MEDIUMINT unsigned NOT NULL,
	PRIMARY KEY (UID,docTable,docID)
)ENGINE=MEMORY;

/* query separator */
TRUNCATE ###TBLPREFIX###tblSearchResult;
/* query separator */
DROP TABLE IF EXISTS SEARCH_TEMP_TABLE;