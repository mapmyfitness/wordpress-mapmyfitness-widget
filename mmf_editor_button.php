<?php

include_once('mmf_utils.php');

function add_mmf_button($context) {
	$img = plugins_url( 'images/mmf-logo-16.png' , __FILE__ );
	$context .= '<a href="javascript:void(0);" id="insert-mmf-wdiget-button" class="button" title="Add MapMyFitness Widget"><img style="padding-left:0;" src="'.$img.'" />Add MapMyFitness Widget</a>';

	return $context;
}

function mmf_editor_popup() {
	wp_enqueue_script('lightbox_me', plugins_url('js/lightbox_me.js', __FILE__), array('jquery'), '1.0', true);
	wp_enqueue_script('mmf_admin_popup_js', plugins_url('js/admin_popup.js', __FILE__), array('jquery'), '1.0', true);
	enqueueStyles();
	?>
		<div id="mmf_admin_popup" style="display:none;">
		  <div id="mmf_admin_content">
		  	<div class="mmf_loading"></div>
		  </div>
		</div>

		<script>
			// We bind the lightbox script here instead o JS file because we need the dynamic plugin dir
			jQuery(document).ready(function() {
				jQuery("#insert-mmf-wdiget-button").click(function() {
					jQuery("#mmf_admin_popup").lightbox_me({
						centered: true,
						onLoad: function() { mmf_load_admin_popup('<?php echo plugin_dir_url(__FILE__); ?>'); },
						onClose: mmf_close_admin_popup
					});
				});
			});
		</script>
	<?php
}

add_action('media_buttons_context',  'add_mmf_button');
add_action('admin_footer', 'mmf_editor_popup');

?>