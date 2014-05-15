<?php
	/*
	Plugin Name: MapMyFitness
	Plugin URI: http://www.mapmyfitness.com
	Description: Widgets and Shortcode extensions for displaying your MMF data
	Author: MapMyFitness
	Version: 0.1
	Author URI: http://www.mapmyfitness.com
	*/

	// Includes admin to render config page
	function mmf_admin() {
		include('mmf_admin_page.php');
	}

	// Adds menu item to the wordpress menu
	function mmf_admin_actions() {
		add_options_page("MapMyFitness", "MapMyFitness", 1, "mmf", "mmf_admin");
	}
	add_action('admin_menu', 'mmf_admin_actions');

	// Initializes the mmf_authorized option in the WP Database
	function mmf_init() {
		update_option('mmf_authorized', false);
		update_option('mmf_show_admin_authorize', true);
	}
	register_activation_hook( __FILE__, 'mmf_init' );

	// Show message when activating plugin
	function mmf_activation_message() {
		if (get_option('mmf_show_admin_authorize') == true) {
			?>
				<div class="updated">
					<p>The MapMyFitness plugin is now activated, but you must visit the settings page to authorize your account and enter an API key.</p>
				</div>
			<?php
			update_option('mmf_show_admin_authorize', false);
		}
		?>
		<script>
			window.mmf_plugin_url = '<?php echo trim(plugins_url(" ", __FILE__)); ?>';
			window.wp_site_url = '<?php echo trim(site_url()); ?>';
		</script>
		<?php
	}
	add_action('admin_notices', 'mmf_activation_message');

	function mmf_deactivation() {
		delete_option('mmf_access_token');
		delete_option('mmf_access_token_secret');
		delete_option('mmf_authorized');
		delete_option('mmf_oauth_key');
		delete_option('mmf_oauth_secret');
	}
	register_deactivation_hook( __FILE__, 'mmf_deactivation');

	// Includes widgets/shortcodes
	include_once('mmf_recent_workouts.php');
	include_once('mmf_aggregates.php');
	include_once('mmf_route.php');
	include_once('mmf_course.php');
	include_once('mmf_editor_button.php');
	include_once('mmf_friends.php');
?>