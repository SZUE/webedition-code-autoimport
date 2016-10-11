<?php
$langs = array('aa', 'be', 'cy', 'en', 'fo', 'he', 'ig', 'ja', 'ku', 'mk', 'my', 'ny', 'pt', 'si', 'st', 'ti', 'ug', 'xh', 'af', 'bg', 'da', 'eo', 'fr', 'hi', 'ii', 'ka', 'kw', 'ml', 'nb', 'oc', 'ro', 'sk', 'sv', 'tl', 'uk', 'yo', 'ak', 'bn', 'de', 'es', 'ga', 'hr', 'in', 'kk', 'ky', 'mn', 'ne', 'om', 'ru', 'sl', 'sw', 'tn', 'ur', 'zh', 'am', 'bo', 'dv', 'et', 'gl', 'hu', 'is', 'kl', 'ln', 'mo', 'nl', 'or', 'rw', 'so', 'ta', 'to', 'uz', 'zu', 'ar', 'bs', 'dz', 'eu', 'gu', 'hy', 'it', 'km', 'lo', 'mr', 'nn', 'pa', 'sa', 'sq', 'te', 'tr', 've', 'as', 'ca', 'ee', 'fa', 'gv', 'ia', 'iu', 'kn', 'lt', 'ms', 'no', 'pl', 'se', 'sr', 'tg', 'ts', 'vi', 'az', 'cs', 'el', 'fi', 'ha', 'id', 'iw', 'ko', 'lv', 'mt', 'nr', 'ps', 'sh', 'ss', 'th', 'tt', 'wo');
foreach($langs as $lang){
	$terx = (Zend_Locale::getTranslationList('Territory', $lang));

	file_put_contents($_SERVER['DOCUMENT' . '_ROOT'] . '/z/' . $lang . '.inc.php', '<?php return we_unserialize(\'' . str_replace('\'','\\\'',we_serialize([
			'territory' => array_filter($terx, function($v, $k){
					return strlen($k) == 2;
				}, ARRAY_FILTER_USE_BOTH),
			'region' => array_filter($terx, function($v, $k){
					return strlen($k) > 2;
				}, ARRAY_FILTER_USE_BOTH),
			'language' => (Zend_Locale::getTranslationList('Language', $lang)),
			'script' => (Zend_Locale::getTranslationList('Script', $lang)),
			'months' => (Zend_Locale::getTranslationList('Months', $lang)['format']),
			'days' => (Zend_Locale::getTranslationList('Days', $lang)['format']),
			//p_r(Zend_Locale::getTranslationList('Week', $lang));
			//p_r(Zend_Locale::getTranslationList('Quarters', $lang));
			//p_r(Zend_Locale::getTranslationList('Eras', $lang));
			], 'json')) . '\');');
}
