<?php


// For the WP Plugin only, we are including a seperate file that is configured by the plugin's admin interface
if (file_exists('./wp-load.php')) {
	include_once('./wp-load.php');
} else if (file_exists('../wp-load.php')) {
	include_once('../wp-load.php');
} else if (file_exists('../../wp-load.php')) {
	include_once('../../wp-load.php');
} else if (file_exists('../../../wp-load.php')) {
	include_once('../../../wp-load.php');
} else if (file_exists('../../../../wp-load.php')) {
	include_once('../../../../wp-load.php');
} else if (file_exists('../../../../wp-load.php')) {
	include_once('../../../../wp-load.php');
}

define("MMF_OAUTH_CONSUMER_KEY", get_option('mmf_oauth_key'));
define("MMF_OAUTH_CONSUMER_SECRET", get_option('mmf_oauth_secret'));

// DO NOT TOUCH BELOW
define("MMF_OAUTH_HOST","https://api.mapmyapi.com/v7.0");
define("MMF_REQUEST_T0KEN_URL","https://api.mapmyapi.com/v7.0/oauth/temporary_credential/" );
define("MMF_AUTHORIZE_URL", "https://www.mapmyfitness.com/oauth/authorize/");
define("MMF_TOKEN_CREDENTIAL_URL", "https://api.mapmyapi.com/v7.0/oauth/token_credential/");

// This turns off warnings for PHP 5.5 Strict Standards. The OAuth library does not comply.
// Comment this line if you wish to see annoying warnings.
error_reporting((E_ALL ^ E_STRICT) & ~E_STRICT);

?>
