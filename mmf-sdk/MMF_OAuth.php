<?php

include_once "oauth-lib/OAuthRequester.php";
include_once "oauth-lib/OAuthStore.php";
include_once "MMF_Config.php";

class MMF_OAuth {

	/**
	 * Gets an authorize URL from the MMF OAuth server and sets the callback URL for the request
	 * @param  string $callback_url Callback URL to navigate to after the Authorization Request
	 * @return string               Returns the Authorize URL with the OAuthToken appended as a querystring
	 */
	function getAuthorizeURL($callback_url) {
		$store = MMF_OAuth::setOAuthStore(); // We need an OAuthStore to work with first

		$result = OAuthRequester::requestRequestToken(MMF_OAUTH_CONSUMER_KEY, 1, array("oauth_callback" => $callback_url));

		return $result['authorize_uri'] . '?oauth_token=' . $result['token'];
	}

	/**
	 * Exchanges the OAuth Request Token and Verifier for a OAuth Access Token
	 * @param  string $oauth_token    OAuth Request Token used for Exchange
	 * @param  string $oauth_verifier OAuth Request Verifier used for Exchange
	 * @return array                  Array containing the Access Token ("access_token") and the Access Token Secret ("access_token_secret")
	 */
	function getAccessToken($oauth_token, $oauth_verifier) {
		$store = MMF_OAuth::setOAuthStore(); // We need an OAut Store to work with first

		try {
			OAuthRequester::requestAccessToken(
				MMF_OAUTH_CONSUMER_KEY,
				$oauth_token,
				0,
				'POST',
				array(
					'oauth_token' => $oauth_token,
					'oauth_verifier' => $oauth_verifier
				)
			);

		} catch (Exception $e) {
			echo "Exception: " . $e->getMessage();
		}

		$sessionObj = $store->getSecretsForSignature('', 0); // Gets the Session Data from the OAuthStore

		return array(
			"access_token" => $sessionObj["token"],
			"access_token_secret" => $sessionObj["token_secret"]
		);

	}

	/**
	 * Creates an OAuthStore with the server details defined in the config file
	 */
	function setOAuthStore() {
		$options = array(
			'consumer_key' => MMF_OAUTH_CONSUMER_KEY,
			'consumer_secret' => MMF_OAUTH_CONSUMER_SECRET,
			'server_uri' => MMF_OAUTH_HOST,
			'request_token_uri' => MMF_REQUEST_T0KEN_URL,
			'authorize_uri' => MMF_AUTHORIZE_URL,
			'access_token_uri' => MMF_TOKEN_CREDENTIAL_URL
		);

		return OAuthStore::instance("Session", $options);
	}
}

?>