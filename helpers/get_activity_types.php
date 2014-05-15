<?php
	include_once('../../../../wp-load.php');
	include_once('../mmf-sdk/MMF.php');

	header('Content-Type: application/json');

	$userId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));

	//Get user activities IDs
	$stats = @MMF::getUserStats(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $userId, "lifetime");

	$activities = array();
	for($x = 0; $x < count($stats); $x++) {
		$activityId = $stats[$x]["_links"]["activity_type"][0]["id"];
		$activity = @MMF::getActivityType(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $activityId);
		array_push($activities, $activity);
	}

	echo json_encode($activities);
?>