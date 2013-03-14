<?php

/**
 * check if its beta
 */

// not possible for too old versions
if (isset($clientRequestVars['isBeta'])) {
	print notification::getLiveUpdateNotPossibleForOldBetaResponse();
	exit;

}

// beta versions!
if (isset($clientRequestVars['betaVersion'])) {
	switch ($clientRequestVars['betaVersion']) {
		case '5000':
			//$_SESSION['testUpdate'] = true;
			//print notification::getBetaExpiredResponse();
			//exit;
			break;
		case '5090':
		case '5091':
		case '5092':
		case '5093':
		case '5094':
		case '5095':
		case '5096':
		case '5097':
		case '5098':
		case '5099':
			//$_SESSION['testUpdate'] = true;
			print notification::getBetaExpiredResponse();
			exit;
			break;
		case '5490':
		case '5491':
		case '5492':
		case '5493':
		case '5494':
		case '5495':
		case '5496':
		case '5497':
		case '5498':
		case '5499':
		case '5500':
			//$_SESSION['testUpdate'] = true;
			print notification::getBetaExpiredResponse();
			exit;
			break;
		case '5900':
		case '5901':
		case '5902':
		case '5903':
		case '5904':
		case '5905':
		case '5906':
		case '5907':
		case '5908':
		case '5909':
			//$_SESSION['testUpdate'] = true;
			print notification::getBetaExpiredResponse();
			exit;
			break;
		/*
		case '5VG3QF4m31NkksvYVIUGZbqEtV6kVUQ':
			$_SESSION['testUpdate'] = true;
			//print notification::getBetaExpiredResponse();
			//exit;
			break;
		*/
	}

}

?>