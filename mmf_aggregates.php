<?php
	include_once('mmf-sdk/MMF.php');
	include_once('mmf_utils.php');

	//Loads stylesheets and scripts needed for MMF Widgets
	enqueueStyles();

	class mmf_aggregates_widget extends WP_Widget {
		function mmf_aggregates_widget() {
			parent::WP_Widget(false, "MapMyFitness - Aggregates");
		}

		function form($instance) {
			wp_enqueue_script('mmf-aggregates-script', plugins_url('js/aggregates.js', __FILE__));

			if(!verifyAccessTokens())
				return msgInvalidAccessTokens();

			echo '<div class="mmf-aggregates-widget-admin">';

			// Pull values from existing instance
			if($instance) {
				$title = esc_attr($instance['title']);
				$selected = esc_attr($instance['selected']);
			}
			else {
				$title = '';
				$selected = '';
			}

			$authenticatedUserId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));
			$userId = $authenticatedUserId;

			//Get user activities IDs
			$stats = @MMF::getUserStats(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $userId, "lifetime");

			//Get activities' names with their IDs
			$activities = array();
			for($x = 0; $x < count($stats); $x++) {
				$activityId = $stats[$x]["_links"]["activity_type"][0]["id"];
				if (!array_key_exists($activityId, $activities)) {
					$activities[$activityId] = @MMF::getActivityType(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $activityId);
				}
			}

			//Title
			echo '
				<p>
					<label for="'.$this->get_field_id('title').'">Widget Title</label>
					<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />
				</p>
			';

			//Selected aggregates
			echo '
				<p class="selectedP">
					<label for="'.$this->get_field_id('multi-select').'">Selected Aggregates</label>
					<select multiple class="selectedSel widefat" id="'.$this->get_field_id('multi-select').'"></select>
					<input type="hidden" class="selectedSelHidden" id="'.$this->get_field_id('selected').'" name="'.$this->get_field_name('selected').'" value="'.$selected.'" />
				</p>
			';

			//Remove button
			echo '
				<p class="removeP" align="right">
					<button class="removeBtn button-primary" type="button">Remove</button>
				</p>
			';

			//Select activity
			echo '
				<h4 style="margin-bottom: 0;">Add an Aggregate:</h4>
				<p>
					<label>Activity</label>
					<select class="activitySel widefat">
						<option value="select" selected="selected">Please select...</option>';
						foreach ($activities as $key => $value) {
							echo'<option value="' . $key . '">' . $value['name'] . '</option>';
						}
			echo '	</select>
				</p>
			';

			//Select duration
			echo '
				<p class="durationP" style="display: none;">
					<label>Duration</label>
					<select class="durationSel widefat">
						<option value="select" selected="selected">Please select...</option>
						<option value="lifetime">Lifetime</option>
						<option value="year">Year</option>
						<option value="month">Month</option>
						<option value="week">Week</option>
						<option value="day">Day</option>
					</select>
				</p>
			';

			//Select stat
			echo '
				<p class="statP" style="display: none;">
					<label>Statistic</label>
					<select class="statSel widefat">
						<option value="select" selected="selected">Please select...</option>
						<option value="distance">Distance</option>
						<option value="duration">Duration</option>
						<option value="energy">Energy</option>
						<option value="activity_count">Activity count</option>
						<option value="avg_pace">Average pace</option>
						<option value="avg_speed">Average speed</option>
					</select>
				</p>
			';

			//Add button
			echo '
				<p class="addP" align="right" style="display: none;">
					<button class="addBtn button-primary" type="button">Add</button>
				</p>
			';

			echo '<div style="height: 15px;"></div>';

			if( $instance) {
				echo '
					<script>
						jQuery(document).ready(function() {
							var $ = jQuery;
							var $parent = $("#'.$this->get_field_id('selected').'").parents(".widget-content");

							var valuesString = $("#'.$this->get_field_id('selected').'").val();

							if (valuesString != "" && valuesString != " ") {
								var values = valuesString.split(",");

								for (var i = 0; i < values.length; i++) {
									var valuesSplit = values[i].split("|");

									var activityText = $parent.find(".activitySel option[value=" + valuesSplit[0] + "]").text();
									var durationText = $parent.find(".durationSel option[value=" + valuesSplit[1] + "]").text();
									var statText = $parent.find(".statSel option[value=" + valuesSplit[2] + "]").text();

									$parent.find(".selectedSel").append("<option value=\"" + values[i] + "\">" + activityText + " | " + durationText + " | " + statText + "</option>");
								}
							}
						});
					</script>
				';
			}

			echo '</div>';
		}

		function update($new_instance, $old_instance) {
			 $instance = $old_instance;

			 $instance['title'] = strip_tags($new_instance['title']);
			 $instance['selected'] = strip_tags($new_instance['selected']);

			 return $instance;
		}

		function widget($args, $instance) {
			if(!verifyAccessTokens())
				return msgInvalidAccessTokens();

			extract($args);

			echo($before_widget);

			$title = apply_filters('widget_title', $instance['title']);
			if ($title) {
				echo $before_title . $title . $after_title;
			}

			echo '<section class="mmf-widget widget"><div class="mmf-aggregates-widget">';

			$userId = @MMF::getAuthenticatedUserId(get_option('mmf_access_token'), get_option('mmf_access_token_secret'));

			$selected = $instance['selected'];

			if (!is_null($selected) && $selected != "" && $selected != " ") {
				$aggregates = explode(",", $selected);

				foreach($aggregates as $aggregate) {
					echo '<div class="mmf-aggregate">';

					$aggregateParts = explode("|", $aggregate);
					$id = $aggregateParts[0];
					$duration = $aggregateParts[1];
					$stat = $aggregateParts[2];

					$activityType = @MMF::getActivityType(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $id);
					$name = $activityType['name'];

					$stats = @MMF::getUserStats(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $userId, $duration);

					$index = null;

					for($x = 0; $x < count($stats); $x++) {
						if($stats[$x]["_links"]["activity_type"][0]["id"] == $id) {
							$index = $x;
							break;
						}
					}

					echo '<div class="mmf-aggregate-image"><img src="'.$activityType['_links']['icon_url'][0]['href'].'" /></div>';

					echo '<div class="mmf-aggregate-data">';

					echo '<div class="mmf-aggregate-activityname">'.$name.'</div>';

					if (is_null($index)) {
						echo '<div class="mmf-aggregate-statdata">--</div>';
					} else {
						$statDetail = $stats[$index][$stat];

						switch($stat){
							case "distance":
							$statDetail *= 0.000621371;
							if($statDetail > 100)
								$statDetail = ceil($statDetail);
							else
								$statDetail = round($statDetail, 2);
							$metric = "mi";
							break;
						case "duration":
							$hours = floor($statDetail/3600);
							$minutes = $statDetail - $hours * 3600;
							$minutes = floor($minutes/60);
							$seconds = $statDetail - $hours * 3600 - $minutes * 60;
							if($minutes < 10)
								$minutes = "0" . $minutes;
							if($seconds < 10)
								$seconds = "0" . $seconds;
							$statDetail = $hours . ":" . $minutes . ":" . $seconds;
							$metric = "";
							break;
						case "energy":
							$statDetail /= 4184;
							$metric = "kCal";
							break;
						case "activity_count":
							if($statDetail == 1)
								$metric = "time";
							else
								$metric = "times";
							break;
						case "avg_pace":
							$statDetail /= 2.23694;
							if($statDetail > 100)
								$statDetail = ceil($statDetail);
							else
								$statDetail = round($statDetail, 2);
							$metric = "h/mi";
							break;
						case "avg_speed":
							$statDetail *= 2.23694;
							if($statDetail > 100)
								$statDetail = ceil($statDetail);
							else
								$statDetail = round($statDetail, 2);
							$metric = "mi/h";
							break;
						}

						echo '<div class="mmf-aggregate-statdata">'.$statDetail.' '.$metric.'</div>';
					}

					switch($duration){
						case "lifetime":
							$duration = "in my lifetime";
							break;
						case "year":
							$duration = "in the past year";
							break;
						case "month":
							$duration = "in the past month";
							break;
						case "week":
							$duration = "in the past week";
							break;
						case "day":
							$duration = "in the last day";
							break;
					}

					echo '<div class="mmf-aggregate-duration">'.$duration.'</div>';

					echo '</div>';

					echo '<div style="clear:both;"></div></div>';
				}
			} else {
				echo "<div style=\"padding: 10px;\">No aggregates were selected</div>";
			}

			echo '</div><footer><a href="http://www.mapmyfitness.com/"><img class="mmf-logo" src="'.plugins_url('images/mmf-logo.png', __FILE__).'" /></a></footer></section>';

			echo($after_widget);
		}
	}

	add_action('widgets_init', function() {
		register_widget("mmf_aggregates_widget");
	})
?>