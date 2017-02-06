<?php

//NOTE you are inside the constructor of weTagData.class.php

include(WE_INCLUDES_PATH . 'weTagWizard/we_tags/we_tag_' . str_replace('Not', '', $tagName) . '.inc.php');
$this->Description = g_l('weTag', '[ifIsActive][description]', true);
