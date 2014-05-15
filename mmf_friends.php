<?php
include_once('mmf-sdk/MMF.php');
include_once('mmf_utils.php');

// Loads stylesheets needed for MMF Widgets
enqueueStyles();

function mmf_friends($userId, $widget=false) {
    // Gets friends array
    $friends = MMF::getFriendsOfUser(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $userId);

    // Initialize Output String
    $output = '<section class="mmf-widget widget mmf-friends">';

    // Run for each friend returned from API
    foreach ($friends as $friend) {
        $friendId = $friend["id"];
        $friendDisplayName = $friend["display_name"];
        $friendImageResults = MMF::getUserProfilePicture(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $friendId);
        $friendImage = $friendImageResults["small"];

        $output .= '
            <div class="mmf-friend"><a href="http://www.mapmyfitness.com/profile/' . $friendId . '" target="_new">
            <div class="mmf-friend-content">
                <img alt="'. $friendDisplayName .'" src="'. $friendImage .'">
                <p>' . $friendDisplayName . '</p>
            </div>
            </a></div>';
    }

    // Widget Footer
    $output .= '<div style="height: 1px;clear:both;"></div><footer><a href="http://www.mapmyfitness.com/"><img class="mmf-logo" src="'.plugins_url('images/mmf-logo.png', __FILE__).'" /></a></footer>';

    $output .= '
        <script>
            jQuery(document).ready(function() {
               jQuery(".mmf-friends").each(function() {
                    if (jQuery(this).width() < 300) {
                        jQuery(".mmf-friend", this).css("float", "none").css("width", "auto");
                    }
               });
            });
        </script>
    ';

    // $output .= '</section>';

    return $output;
}

// Register the Widget
class mmf_friends_widget extends WP_Widget
{
    function mmf_friends_widget()
    {
        parent::WP_Widget(false, 'MapMyFitness - Friends');
    }

	function form($instance) {
		// Pull values from existing instance
		if( $instance) {
			$title = esc_attr($instance['title']);
		} else {
			$title = '';
		}

		echo '
			<p>
			<label for="'.$this->get_field_id('title').'">Widget Title</label>
			<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />
			</p>
		';
	}

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function widget($args, $instance) {
        // Verify stored access token
        if(!verifyAccessTokens()) {
            return msgInvalidAccessTokens();
        }

        // Get the User ID of the authenticated user
        $authenticatedUserId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));

        extract($args);

        // Widget Options
        $title = apply_filters('widget_title', $instance['title']);

        echo($before_widget);

        if ($title) {
            echo $before_title . $title . $after_title;
        }

        echo(mmf_friends($authenticatedUserId, true));

        echo($after_widget);
    }
}
add_action('widgets_init', create_function('', 'return register_widget("mmf_friends_widget");'));
?>