<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Module = 'banner';
$this->Deprecated = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
