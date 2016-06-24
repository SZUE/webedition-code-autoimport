
switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case 'tool_<?= $TOOLNAME; ?>_edit':
		include(WE_INCLUDES_PATH.'we_tools/tools_frameset.php');
	break;
}