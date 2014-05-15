<?php
	include_once('../../../../wp-load.php');
	include_once('../mmf-sdk/MMF.php');

	header('Content-Type: application/json');

	$userId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));

	$bookmarksResult = @MMF::getBookmarkedRoutesForUser(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $userId);

	$return = array();

	foreach($bookmarksResult as $route) {
		$routeResult = @MMF::getRoute(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $route['_links']['route'][0]['id']);
		array_push($return, $routeResult);
	}

	echo json_encode($return);
?>