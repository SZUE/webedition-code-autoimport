<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
echo we_html_tools::getHtmlTop() .
 STYLESHEET;
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 2);
?>
<script><!--
	function revertToPublished() {
		if (confirm("<?php echo addslashes(g_l('weEditorInfo', '[revert_publish_question]')); ?>")) {
			top.we_cmd("revert_published");
		}
	}

<?php if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'revert_published'){ ?>
		var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrameByTransaction("<?php echo $we_transaction; ?>");
		_EditorFrame.setEditorIsHot(false);
	<?php echo $GLOBALS['we_doc']->getUpdateTreeScript(true); ?>
		_EditorFrame.getDocumentReference().frames.editFooter.location.reload();
<?php } ?>
//-->
</script>
</head>
<body class="weEditorBody">
	<?php
	if($GLOBALS['we_doc']->ContentType !== we_base_ContentTypes::FOLDER && $GLOBALS['we_doc']->ContentType !== we_base_ContentTypes::COLLECTION){
		$fs = $GLOBALS['we_doc']->getFilesize();
	}
	$_html = '
<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">ID</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID ? : "-") . '</div>
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[content_type]') . '</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ContentType ? g_l('weEditorInfo', '[' . $GLOBALS['we_doc']->ContentType . ']') : '') . '</div>' .
		(isset($fs) ?
			'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[file_size]') . '</div>
<div style="margin-bottom:10px;">' . round(($fs / 1024), 2) . "&nbsp;KB&nbsp;(" . $fs . "&nbsp;Byte)" . '</div>' :
			'');

	$parts = array(
		array(
			'headline' => '',
			'html' => $_html,
			'space' => 140,
			'iconX' => we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . $GLOBALS['we_doc']->ContentType . '",true,"' . (isset($GLOBALS['we_doc']->Extension) ? $GLOBALS['we_doc']->Extension : '') . '"))')
		)
	);

	if($GLOBALS['we_doc']->ContentType !== we_base_ContentTypes::FOLDER){
		$_html = '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[creation_date]') . '</div>
<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', '[date_format]'), $GLOBALS['we_doc']->CreationDate) . '</div>' .
			($GLOBALS['we_doc']->CreatorID && ($name = f('SELECT CONCAT(First," ",Second," (",username,")") AS name FROM ' . USER_TABLE . ' WHERE ID=' . intval($GLOBALS['we_doc']->CreatorID))) ?
				'
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', '[created_by]') . '</div>
<div style="margin-bottom:10px;">' . $name . '</div>' :
				'') .
			'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[changed_date]') . '</div>
<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', '[date_format]'), $GLOBALS['we_doc']->ModDate) . '</div>' .
			($GLOBALS['we_doc']->ModifierID && ($name = f('SELECT CONCAT(First," ",Second," (",username,")") AS name FROM ' . USER_TABLE . ' WHERE ID=' . intval($GLOBALS['we_doc']->ModifierID))) ?
				'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', '[changed_by]') . '</div>
<div style="margin-bottom:10px;">' . $name . '</div>' .
				(in_array($GLOBALS['we_doc']->ContentType, array(we_base_ContentTypes::HTML, we_base_ContentTypes::WEDOCUMENT)) ?
					'<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[lastLive]') . '</div>' .
					'<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->Published ? date(g_l('weEditorInfo', '[date_format]'), $GLOBALS['we_doc']->Published) : "-") . '</div>' :
					'') .
				(!in_array($we_doc->Table, array(TEMPLATES_TABLE, VFILE_TABLE)) && $GLOBALS['we_doc']->Published && $GLOBALS['we_doc']->ModDate > $GLOBALS['we_doc']->Published ?
					'<div style="margin-bottom:10px;">' . we_html_button::create_button('revert_published', 'javascript:revertToPublished();', true, 280) . '</div>' :
					'') :
				'');


		$parts[] = array(
			'headline' => '',
			'html' => $_html,
			'space' => 140,
			'icon' => 'cal.gif'
		);

		switch($GLOBALS['we_doc']->Table){
			case TEMPLATES_TABLE:
			case VFILE_TABLE:
				break;
			default:
				$rp = realpath($GLOBALS['we_doc']->getRealPath());
				$http = $GLOBALS['we_doc']->getHttpPath();

				switch($GLOBALS['we_doc']->ContentType){
					default:
						$showlink = false;
						break;
					case we_base_ContentTypes::WEDOCUMENT:
						$showlink = true;
						if(defined('WORKFLOW_TABLE')){
							$anzeige = (we_workflow_utility::inWorkflow($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table) ?
									we_workflow_utility::getDocumentStatusInfo($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table) :
									we_workflow_utility::getLogButton($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table));
						}
						break;

					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::IMAGE:
					case we_base_ContentTypes::FLASH:
					case we_base_ContentTypes::QUICKTIME:
					case we_base_ContentTypes::VIDEO:
					case we_base_ContentTypes::AUDIO:
						$showlink = true;
				}

				$published = !(($GLOBALS['we_doc']->ContentType == we_base_ContentTypes::HTML || $GLOBALS['we_doc']->ContentType == we_base_ContentTypes::WEDOCUMENT) && $GLOBALS['we_doc']->Published == 0);

				$_html = '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[local_path]') . '</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID == 0 || !$published ? '-' : '<span title="' . oldHtmlspecialchars($rp) . '">' . oldHtmlspecialchars(we_base_util::shortenPath($rp, 74)) . '</span>') . '</div>
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', '[http_path]') . '</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID == 0 || !$published ? '-' : ($showlink ? '<a href="' . $http . '" target="_blank" title="' . oldHtmlspecialchars($http) . '">' : '') . we_base_util::shortenPath($http, 74) . ($showlink ? '</a>' : '')) . '</div>';

				$parts[] = array(
					'headline' => '',
					'html' => $_html,
					'space' => 140,
					'icon' => 'path.gif'
				);
		}

		if(isset($anzeige)){
			$parts[] = array(
				'headline' => g_l('modules_workflow', '[workflow]'),
				'html' => $anzeige,
				'space' => 140,
				'forceRightHeadline' => 1,
				'icon' => 'workflow.gif'
			);
		}

		switch($GLOBALS['we_doc']->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				list($cnt, $select) = $GLOBALS['we_doc']->formTemplateDocuments();
				$parts[] = array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[documents]') . ($cnt ? ' (' . $cnt . ')' : ''), 'html' => $select, 'space' => 140);
				break;
			case we_base_ContentTypes::IMAGE:
				$_metaData = $GLOBALS['we_doc']->getMetaData();
				$_metaDataTable = '
<table class="default">
	<tr><td style="padding-bottom: 5px;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_exif_data]') . '</td></tr>';
				if(empty($_metaData['exif'])){
					$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont" colspan="2">' . g_l('metadata', (is_callable("exif_read_data") ? '[no_exif_data]' : '[no_exif_installed]')) . '</td></tr>';
				} else {
					foreach($_metaData['exif'] as $_key => $_val){
						$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_val) . '</td></tr>';
					}
				}

				$_metaDataTable .= '<tr><td style="padding:10px 0 5px 0;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_iptc_data]') . '</td></tr>';
				if(empty($_metaData['iptc'])){
					$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont" colspan="2">' . g_l('metadata', '[no_iptc_data]') . '</td></tr>';
				} else {
					foreach($_metaData['iptc'] as $_key => $_val){
						$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_val) . '</td></tr>';
					}
				}
				$_metaDataTable .= '</table>';
				break;
			case we_base_ContentTypes::APPLICATION:
				if($GLOBALS['we_doc']->Extension === '.pdf'){
					$metaData = $GLOBALS['we_doc']->getMetaData();
					$_metaDataTable = '
<table class="default">
	<tr><td style="padding-bottom: 5px;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_pdf_data]') . '</td></tr>';
					if(!empty($metaData['pdf'])){
						foreach($metaData['pdf'] as $key => $val){
							$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($val) . '</td></tr>';
						}
					}
					$_metaDataTable .= '</table>';
					break;
				}
			//no break;
			default:
				if($GLOBALS['we_doc']->isBinary()){
					$_metaDataTable = g_l('metadata', '[no_metadata_supported]');
				}
		}

		if($GLOBALS['we_doc']->isBinary()){
			$formReference = $GLOBALS['we_doc']->formReferences();
			$parts[] = array(
				'headline' => g_l('weClass', '[isUsed]') . ' (' . $formReference['num'] . ')',
				'html' => $formReference['form'],
				'space' => 140,
				'forceRightHeadline' => 1,
				'icon' => 'references.gif'
			);
		}

		if(isset($_metaDataTable)){
			$parts[] = array(
				'headline' => '',
				'html' => $_metaDataTable,
				'space' => 140,
				'forceRightHeadline' => 1,
				'icon' => 'meta.gif'
			);
		}
	}

	echo we_html_multiIconBox::getJS() .
	we_html_multiIconBox::getHTML('', $parts, 20, '', -1, '', '', false);
	?>
</body>
</html>
