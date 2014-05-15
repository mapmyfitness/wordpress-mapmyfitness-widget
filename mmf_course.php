<?php
	include_once('mmf-sdk/MMF.php');
	include_once('mmf_utils.php');

	// Loads stylesheets needed for MMF Widgets
	enqueueStyles();

	function mmf_course($courseId, $height, $showLeaderboard, $static, $activityId) {
		try {
			// Gets course data
			$course = @MMF::getCourseById(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $courseId, 500, 500);
			if(!$static) $courseMap = @MMF::getCourseMap(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $courseId);
		} catch (Exception $e) {
			echo "<div class=\"mmf-exception\"><div class=\"mmf-exception-title\">MapMyFitness Plugin Error: Course could not be loaded</div>This course may not exist, privacy settings are blocking it, or our servers are down.</div>";
			return;
		}

		// Initialize Output String
		$output = '';

		// Widget Header
		$output .= '
			<section class="mmf-widget" id="course-'.$courseId.'">
			    <header>
		            <a class="header-logo" href="http://www.mapmyfitness.com/courses/view/'.$courseId.'" target="_blank"><img class="logo" src="'.plugins_url('images/mmf-logo.png', __FILE__).'"/></a>
			        <h2>'.$course["name"].' Course</h2>
			    </header>
		';

		if($static) {
			$thumbnailURL = stripslashes($course["thumbnail"]);
			$output .= '
				<img src="' . $thumbnailURL . '" />
			';
		} else {
			$output .='
			    <div class="mmf-course-map" id="mmf-course-map-'.$courseId.'" style="height: '.$height.'px; width: 100%;">
			    	<div class="mmf_loading"></div>
			    </div>
			';

			// Google Maps Script output
			$output .= '
				<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQ05JwE60aFa6Pm1ny7Fk61xXRozj1r08&sensor=false"></script>
				<script type="text/javascript">
					function initialize() {
						var course = '.json_encode($courseMap["points"]).';

						var lat, lng, pointLatLng;
						var bounds = new google.maps.LatLngBounds();
						var courseCoordinates = new Array();
						for(var i = 0; i < course.length; i++) {
							lat = course[i]["lat"];
							lng = course[i]["lng"];
							courseCoordinates[i] = new google.maps.LatLng(lat, lng);
							pointLatLng = new google.maps.LatLng(lat, lng);
							bounds.extend(pointLatLng);
						}

						var coursePath = new google.maps.Polyline({
							path: courseCoordinates,
							geodesic: true,
							strokeColor: "#FF0000",
							strokeOpacity: 0.5,
							strokeWeight: 5
						});

						var startIcon = "'.plugins_url('images/map-start.png', __FILE__).'";
						var startLatlng = new google.maps.LatLng(course[0]["lat"], course[0]["lng"]);
						var startMarker = new google.maps.Marker({
							position: startLatlng,
							title: "Start",
							icon: startIcon
						});

						var finishIcon = "'.plugins_url('images/map-finish.png', __FILE__).'";
						var finishLatlng = new google.maps.LatLng(lat, lng);
						var finishMarker = new google.maps.Marker({
							position: finishLatlng,
							title: "Finish",
							icon: finishIcon
						});

						var mapOptions = {
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};

						var map = new google.maps.Map(
							document.getElementById("mmf-course-map-'.$courseId.'"),
							mapOptions
						);

						map.fitBounds(bounds);
						coursePath.setMap(map);
						startMarker.setMap(map);
						finishMarker.setMap(map);
					}

					google.maps.event.addDomListener(window, "load", initialize);
				</script>
			';
		}

		if ($showLeaderboard) {
			$leaderboard = @MMF::getCourseLeaderBoard(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $courseId, $activityId);
			$output .= '
                <table class="leaderboard-table">

                    <thead>
                        <tr>
                            <th>Badge</th>
                            <th>Name</th>
                            <th>Monthly Points</th>
                            <th>Yearly Points</th>
                            <th>All-Time Points</th>
                        </tr>
                    </thead>

                    <tbody>
	        ';

	        $forLimit = (count($leaderboard) > 10) ? 10 : count($leaderboard);

	        if ($forLimit < 1) {
	        	$output .= '<tr><td colspan=5>No leaderboard data was found for this activity type.</td></tr>';
	        }

            for ($i = 0; $i < $forLimit; $i++) {
            	$leaderboardUser = $leaderboard[$i];

            	$userData = MMF::getUserByID(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $leaderboardUser['_links']['user'][0]['id']);

                if($leaderboardUser['workout_achievements'] != NULL) {
                    $badge = $leaderboardUser['workout_achievements'][0]['title'];
                } else {
                    $badge = '';
                }

                $output .= '
                	<tr>
	                    <td style="text-align: center;">'.$badge.'</td>
	                    <td style="text-align: center;"><a href="http://www.mapmyfitness.com/profile/'.$userData['username'].'" target="_blank">'.$userData['display_name'].'</a></td>
	                    <td style="text-align: center;">'.mmfNumberFormat($leaderboardUser['points']).'</td>
	                    <td style="text-align: center;">'.mmfNumberFormat($leaderboardUser['yearly_points']).'</td>
	                    <td style="text-align: center;">'.mmfNumberFormat($leaderboardUser['alltime_points']).'</td>
	                </tr>
                ';
        	}

        	$output .= '
    				</tbody>
                </table>
        	';
		}

		$output .= "</section>";

		return $output;
	}

	function mmf_course_shortcode($atts, $content = null) {
		// First verify the stored access tokens
		if (!verifyAccessTokens()) {
			return msgInvalidAccessTokens();
		}

		// Extract the shortcode attributes
		extract(shortcode_atts(array(
	        "id" => 123456,
	        "height" => 400,
	        "leaderboard" => false,
			"staticmap" => false,
	        "activitytype" => 11
	    ), $atts));

	    // Render the widget
		return mmf_course($id, $height, $leaderboard, $staticmap, $activitytype);
	}

	add_shortcode('mmf-course', 'mmf_course_shortcode');

?>