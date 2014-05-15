<?php
include_once "mmf-sdk/MMF.php";

	/**
	 * Checks the currently stored WP Options for mmf_access_token and mmf_access_token_secret and verifies its validity
	 * @return bool 	True if tokens are valid
	 */
	function verifyAccessTokens() {
		$accessToken = get_option('mmf_access_token');
		$accessTokenSecret = get_option('mmf_access_token_secret');

		if(empty($accessToken)) {
			return false;
		} else if (empty($accessTokenSecret)) {
			return false;
		}

		// TODO: Add logic to check if Access Tokens actually work.

		return true;
	}

	/**
	 * Returns a Message that tells the user the Access tokens are invalid
	 * @return string 	HTML string
	 */
	function msgInvalidAccessTokens() {
		return '<div style="font-weight: bold;">!! Your MMF Access Tokens are Invalid. You have either not authorized the plugin yet, or your access token has been revoked. Please check the MMF Plugin configration page.</div>';
	}

	/**
	 * Queues up the global styles used for the widgets
	 */
	function enqueueStyles() {
		$plugins_url = plugins_url();

		wp_enqueue_style('mmf-google-fonts-open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,800,700italic,800italic');
		wp_enqueue_style('mmf-main-style', plugins_url('css/main.css', __FILE__) );
	}

	function mmfNumberFormat($var, $decimals = 2) {
		return sprintf(round($var, $decimals) == intval($var) ? "%d" : "%.".$decimals."f", $var);
	}

    // *
    // Grabs a routes data from getRouteMap() with field_set set to detailed for points. *********************
    // *
    function getRouteGraphData($routeInput)
    {
        // Checks if Route data has been queried and stored, if false then query route data
        is_array($routeInput) ? $routeData = $routeInput : $routeData = @MMF::getRoute(get_option('mmf_access_token'), get_option('mmf_access_token_secret'),$routeID = $routeInput);

        $minElevation = $routeData['min_elevation']*3.28084;
        $maxElevation = $routeData['max_elevation']*3.28084;
        $routeTotalDistance = $routeData['distance']*3.28084/5280;
        $routePoints = $routeData['points'];
        $highChartSeries = array();
        isset($routeData['name']) ? $routeName = $routeData['name'] : $routeName = 'No Route Name';

        $nextX = 0;

        foreach ($routePoints as $index=>$routePoint) {

            //convert meters to miles
            $ConvertedDistance = $routePoint['dis']*3.28084/5280;
            $ConvertedElevation = $routePoint['ele']*3.28084;

            if ($ConvertedDistance > $nextX) {
                array_push($highChartSeries, array('id' => $index, 'x' => round($ConvertedDistance, 2), 'y' => round($ConvertedElevation, 2)));
                //another Commit
                //comment
                $nextX += 0.01;
            }
        }


        return array('highChartSeries' => $highChartSeries, 'minElevation' => $minElevation, 'maxElevation' => $maxElevation, 'routeTotalDistance' => $routeTotalDistance, 'routeName' => $routeName, 'routePoints' => $routePoints);
    }

?>