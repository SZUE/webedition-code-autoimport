<?php

$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Groups[] = 'if_tags';
$this->Deprecated = true;
$this->Module = 'voting';
