<?php
	include_once('../../../../wp-load.php');
	include_once('../mmf-sdk/MMF.php');

	header('Content-Type: application/json');

	$success = true;

	try {
		$result = @MMF::getRoute(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $_GET['routeid']);
	} catch(Exception $e) {
		$success = false;
	}

	if ($success) {
		echo json_encode($result);
	} else {
		echo "{error: true}";
	}
?>