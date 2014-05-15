<?php
	include_once("mmf-sdk/MMF.php");

	// We take the OAuth Token and Verifier and exchange it for an Access Token and Secret
	$access_token = MMF_OAuth::getAccessToken($_GET["oauth_token"], $_GET["oauth_verifier"]);
?>

<script>
	window.opener.submitAuthorizeForm("<?php echo $access_token['access_token']; ?>", "<?php echo $access_token['access_token_secret'] ?>");
	window.close();
</script>