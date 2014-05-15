<?php
	include_once('mmf-sdk/MMF.php');

	// Get Stored Access Tokens
	$accessToken = get_option('mmf_access_token');
	$accessTokenSecret = get_option('mmf_access_token_secret');

	// Get Page Mode
	if(isset($_POST['page_mode'])) {
		$pageMode = $_POST['page_mode'];
	} else {
		if (!get_option('mmf_oauth_key'))
			$pageMode = 'oauthkey';
		else {
			if (get_option('mmf_authorized'))
				$pageMode = "authorized";
			else
				$pageMode = "not_authorized";
		}
	}
?>

<style>
	.mmf-plugin .authenticated-user {
		background: -moz-linear-gradient(center top , #F5F5F5 0%, #FCFCFC 97%, #F9F9F9 98%, #EAEAEA 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
		border: 1px solid #C3C2C2;
		box-shadow: 0 3px 1px rgba(0, 0, 0, 0.05);
		margin-bottom: 15px;
		padding: 10px;
		width: 400px;
	}
	.mmf-plugin .authenticated-user-image {
		float: left;
		margin-right: 15px;
	}
	.mmf-plugin .authenticated-user-name {
		font-weight: bold;
		font-size: 18px;
		margin: 14px 0;
	}
</style>

<div class="wrap mmf-plugin">
	<h2>MapMyFitness Plugin Authorization</h2>

<!--
	Page Mode: No stored Access Token
-->
	<?php if($pageMode == "not_authorized") { ?>
		<h3 class="title"><strong>Authorization Status:</strong> <span style="color: red;">Not Authorized</span></h3>

		<p>This plugin is not currently authorized with any MapMyFitness Account. In order to use this plugin, you must first authorize it with your account. Start by clicking the authorize button below:</p>
		<input type="button" value="Authorize" class="button button-primary" id="btn-authorize" name="">

		<script>
			jQuery("#btn-authorize").click(function() {
				window.open(
					'<?php echo plugins_url('mmf_authorize_popup.php', __FILE__); ?>', // Authorize URL we got from MMF_OAuth
					'authorizewindow', // Giving the frame in the new popup window a name
					config='height=600, width=340, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no' // Settings for popup window
				);
			});

			function submitAuthorizeForm(accessToken, accessTokenSecret) {
				var form = jQuery("#authorize_form");
				form.find("#access_token").val(accessToken);
				form.find("#access_token_secret").val(accessTokenSecret);
				form.submit();
			}
		</script>

		<form id="authorize_form" name="authorize_form" method="post" action="" style="display: none;">
			<input id="access_token" name="access_token" value="" />
			<input id="access_token_secret" name="access_token_secret" value="" />
			<input id="next_page_mode" name="page_mode" value="set_access_tokens" />
			<input id="authorize_form_submit" type="submit" />
		</form>

		<p><a id="link_change_api_key" href="javascript:void(0)">Change API Key</a></p>
		<form id="change_api_key_form" name="change_api_key_form" method="post" action="" style="display:none;">
			<input id="next_page_mode" name="page_mode" value="oauthkey" type="hidden" />
			<input id="authorize_form_submit" type="submit" value="Save" />
		</form>
		<script>
			jQuery("#link_change_api_key").click(function(){ if(confirm("This will clear any existing API keys and you will be required to enter a new one before you can use the plugin again. This will also deauthorize you if you are current authorized. Are you sure you want to do this?")) jQuery('#change_api_key_form').submit(); });
		</script>
	<?php } ?>

<!--
	Page Mode: Stored Access Token Exists
-->
	<?php if($pageMode == "authorized") { ?>
		<h3 class="title"><strong>Authorization Status:</strong> <span style="color: green;">Authorized</span></h3>
		<?php $authenticatedUser = @MMF::getAuthenticatedUser(get_option('mmf_access_token'), get_option('mmf_access_token_secret')); ?>

		<div class="authenticated-user">
			<?php $authenticatedUserPicture = @MMF::getUserProfilePicture(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $authenticatedUser['id']); ?>
			<div class="authenticated-user-image"><img src="<?php echo $authenticatedUserPicture['medium']; ?>" /></div>
			<div class="authenticated-user-name"><?php echo $authenticatedUser['display_name']; ?></div>
			<div class="authenticated-user-deauthorize">
				<form id="deauthorize_form" name="deauthorize_form" method="post" action="">
					<input type="hidden" name="page_mode" value="deauthorize" />
					<input type="submit" value="De-authorize" class="button button-primary" id="btn-deauthorize" name="" />
				</form>
			</div>
			<div style="clear: both; height: 0px;"></div>
		</div>

		<p>You have already authorized this app with MapMyFitness. You can start using the widgets and shortcodes! </p>

		<p><a id="link_change_api_key" href="javascript:void(0)">Change API Key</a></p>
		<form id="change_api_key_form" name="change_api_key_form" method="post" action="" style="display:none;">
			<input id="next_page_mode" name="page_mode" value="oauthkey" type="hidden" />
			<input id="authorize_form_submit" type="submit" value="Save" />
		</form>
		<script>
			jQuery("#link_change_api_key").click(function(){ if(confirm("This will clear any existing API keys and you will be required to enter a new one before you can use the plugin again. This will also deauthorize you if you are current authorized. Are you sure you want to do this?")) jQuery('#change_api_key_form').submit(); });
		</script>

	<?php } ?>

<!--
	Page Mode: Set Access Tokens in WP Options
-->
	<?php if($pageMode == "set_access_tokens") { ?>
		<h3 class="title"><strong>Authorization Status:</strong> <span style="color: green;">New Authorization Saved</span></h3>

		<?php
			update_option('mmf_access_token', $_POST['access_token']);
			update_option('mmf_access_token_secret', $_POST['access_token_secret']);
			update_option('mmf_authorized', true);
		?>

		<p>You have successfully authorized the plugin with your account!</p>
		<p>You will be redirect back in a few seconds... or <a id="link-authorization-done" href="javascript:void();">click here</a>.</p>

		<script>
			jQuery('#link-authorization-done').click(function() {
				window.location.href = window.location.pathname + window.location.search;
			});
			setTimeout(function() {jQuery('#link-authorization-done').trigger('click');}, 2000);
		</script>
	<?php } ?>

<!--
	Page Mode: Remove stored OAuth Access Tokens
-->
	<?php if($pageMode == "deauthorize") { ?>
		<h3 class="title"><strong>Authorization Status:</strong> <span style="color: red;">De-authorized</span></h3>

		<?php
			delete_option('mmf_access_token');
			delete_option('mmf_access_token_secret');
			update_option('mmf_authorized', false);
		?>

		<p>You have successfully removed the Authorization stored for this plugin.</p>
		<p>You will be redirect back in a few seconds... or <a id="link-authorization-done" href="javascript:void();">click here</a>.</p>

		<script>
			jQuery('#link-authorization-done').click(function() {
				window.location.href = window.location.pathname + window.location.search;
			});
			setTimeout(function() {jQuery('#link-authorization-done').trigger('click');}, 5000);
		</script>
	<?php } ?>

<!--
	Page Mode: Add API Keys
-->
	<?php if($pageMode == "oauthkey") { ?>

	<?php
		delete_option('mmf_oauth_key');
		delete_option('mmf_oauth_secret');
		delete_option('mmf_access_token');
		delete_option('mmf_access_token_secret');
		update_option('mmf_authorized', false);
	?>

		<h3 class="title"><strong>API Key</strong></h3>
		<p>In order to connect to the MapMyFitness server, we need an API key and an API secret. If you have these two, please enter them below. If you do not have an API key, please visit <a href="https://www.mapmyapi.com/Standard_API">this page</a>, to get one!</p>

		<form id="authorize_form" name="authorize_form" method="post" action="">
			<p>API Client Key: <input id="mmf_api_key" name="mmf_oauth_key" type="text" placeholder="Client Key" value="" /></p>
			<p>API Client Secret: <input id="mmf_api_secret" name="mmf_oauth_secret" type="text" placeholder="Client Secret" value="" /></p>
			<input id="next_page_mode" name="page_mode" value="set_oauth_keys" type="hidden" />
			<p><input id="authorize_form_submit" type="button" value="Save" class="button button-primary"/></p>
		</form>

		<script>
			jQuery(document).ready(function() {
				jQuery("#authorize_form_submit").click(function() {
					if (jQuery("#mmf_api_key").val() == "" || jQuery("#mmf_api_secret").val() == "" || jQuery("#mmf_api_key").val() == undefined || jQuery("#mmf_api_secret").val() == undefined)
						alert("API Client Key and Secret cannot be blank! Please try again.");
					else jQuery("#authorize_form").submit();
				});
			});
		</script>
	<?php } ?>

<!--
	Page Mode: Save API Keys
-->
	<?php if($pageMode == "set_oauth_keys") { ?>
		<h3 class="title"><strong>API Key Saved</strong></h3>

		<?php
			update_option('mmf_oauth_key', $_POST['mmf_oauth_key']);
			update_option('mmf_oauth_secret', $_POST['mmf_oauth_secret']);
			update_option('mmf_authorized', false);
		?>

		<p>You have successfully saved your API key.</p>
		<p>You will be redirect back in a few seconds... or <a id="link-authorization-done" href="javascript:void();">click here</a>.</p>

		<script>
			jQuery('#link-authorization-done').click(function() {
				window.location.href = window.location.pathname + window.location.search;
			});
			setTimeout(function() {jQuery('#link-authorization-done').trigger('click');}, 2000);
		</script>
	<?php } ?>

</div>