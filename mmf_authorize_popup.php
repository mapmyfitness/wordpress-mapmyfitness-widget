<?php
	include_once("../../../wp-load.php");
	include_once("mmf-sdk/MMF_OAuth.php");

	// Generate absolute URL for callback action
	$callback_url = plugins_url('mmf_authorize_callback.php', __FILE__);

	try {
		// We need the Authorize URL to adk for the User's permission to access their MMF account
		$authorize_url = MMF_OAuth::getAuthorizeURL($callback_url);
		header( 'Location: ' . $authorize_url ) ;
	} catch(Exception $e) {
		echo "Request for Authorization failed. Double check your API Key and Secret.";
	}

?>