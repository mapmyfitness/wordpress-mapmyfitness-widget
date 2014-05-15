<?php
	include_once('mmf-sdk/MMF.php');
	include_once('mmf_utils.php');

	// Loads stylesheets needed for MMF Widgets
	enqueueStyles();

	function mmf_recent_workouts($userId, $widget = false) {
		// Gets workouts array
		$workouts = @MMF::getWorkoutsForUser(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $userId);

		// Initialize Output String
		if ($widget)
			$output = '<section class="mmf-widget widget" id="recent_workouts">';
		else
			$output = '<section class="mmf-widget" id="recent_workouts">';

		// Widget Header
		if (!$widget) $output .= '
				<header>
					<a style="float: right; padding-top: 5px;" href="http://www.mapmyfitness.com/" target="_blank"><img class="logo" src="'.plugins_url('images/mmf-logo.png', __FILE__).'" /></a>
					<h2>Recent Workouts</h2>
				</header>
		';

		// Run for each recent workout returned from API
		foreach ($workouts as $workout) {
			$activityType = MMF::getActivityType(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $workout['_links']['activity_type'][0]['id']);
			$timeZone = new DateTimeZone('America/Denver');
			$datetime = new DateTime($workout["start_datetime"], $timeZone);
			$workoutName = empty($workout["name"]) ? "No Name" : $workout["name"];
			$distance = $workout["aggregates"]["distance_total"] * 0.000621371;
			if($distance > 100)
				$distance = ceil($distance);
			else
				$distance = round($distance, 2);
			$activityTimeTotal = $workout["aggregates"]["active_time_total"];
			$statDetail = $activityTimeTotal;
			$hours = floor($statDetail/3600);
			$minutes = $statDetail - $hours * 3600;
			$minutes = floor($minutes/60);
			$seconds = $statDetail - $hours * 3600 - $minutes * 60;
			if($minutes < 10)
				$minutes = "0" . $minutes;
			if($seconds < 10)
				$seconds = "0" . $seconds;
			$statDetail = $hours . ":" . $minutes . ":" . $seconds;
			$activityTimeTotal = $statDetail;
			$caloriesBurned = isset($workout["aggregates"]["metabolic_energy_total"]) ? $workout["aggregates"]["metabolic_energy_total"] / 4184 : 0;

			$output .= '
				<div class="recent-workout">
					<div class="activity-image">
						<a href="http://www.mapmyfitness.com/workout/'.$workout['_links']['self'][0]['id'].'" target="_new">
							<img alt="'.$workout['name'].'" src="'.$activityType['_links']['icon_url'][0]['href'].'">
						</a>
					</div>
					<div class="activity-details">
						<div class="workout-description">
							<a href="http://www.mapmyfitness.com/workout/'.$workout['_links']['self'][0]['id'].'" target="_new">'.$workoutName.'</a>
						</div>
						<div class="workout-data">
							<div class="workout-data-point">
								<h4>Distance</h4>
								<p><span class="medium-number">'. $distance .'</span> mi</p>
							</div>

							<div class="workout-data-point">
								<h4>Duration</h4>
								<p><span class="medium-number">'. $activityTimeTotal .'</span></p>
							</div>

							<div class="workout-data-point">
								<h4>Calories Burned</h4>
								<p><span class="medium-number">'.$caloriesBurned.'</span> kCal</p>
							</div>
						</div>
					</div>
					<div class="clr"></div>
				</div>
			';
		}

		// Widget Footer
		if (!$widget) $output .= '
				<footer>
					<div class="view-all">
						<a href="http://www.mapmyfitness.com/workouts/'.$userId.'/" target="_new">View All</a>
					</div>
					<div style="height: 0px; clear: both;"></div>
				</footer>
		';
		else $output .= '<footer><a href="http://www.mapmyfitness.com/"><img class="mmf-logo" src="'.plugins_url('images/mmf-logo.png', __FILE__).'" /></a></footer>';

		$output .= '</section>';

		return $output;
	}


	// Register the Shortcode
	function mmf_recent_workouts_shortcode($atts, $content = null) {
		// First verify the stored access tokens
		if (!verifyAccessTokens()) {
			return msgInvalidAccessTokens();
		}

		// Lets get the User ID of the authenticated user
		$authenticatedUserId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));

		// Extract the shortcode attributes
		extract(shortcode_atts(array(
			"userid" => $authenticatedUserId
		), $atts));

		// Render the widget
		return mmf_recent_workouts($userid);
	}
	add_shortcode('mmf-recent-workouts', 'mmf_recent_workouts_shortcode');

	// Register the Widget
	class mmf_recent_workouts_widget extends WP_Widget {

		function mmf_recent_workouts_widget() {
			parent::WP_Widget(false, 'MapMyFitness - Recent Workouts');
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
			// First verify the stored access tokens
			if (!verifyAccessTokens()) {
				return msgInvalidAccessTokens();
			}

			// Lets get the User ID of the authenticated user
			$authenticatedUserId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));

			extract($args);

			// Widget Options
			$title = apply_filters('widget_title', $instance['title']);

			echo $before_widget;

			if ( $title ) {
			  echo $before_title . $title . $after_title;
			}

			echo mmf_recent_workouts($authenticatedUserId, true);

			echo $after_widget;
		}
	}
	add_action('widgets_init', create_function('', 'return register_widget("mmf_recent_workouts_widget");'));

?>