<?php
	include_once('mmf-sdk/MMF.php');
	include_once('mmf_utils.php');

	// Loads stylesheets needed for MMF Widgets
	enqueueStyles();

	function mmf_route($routeId, $height, $static, $showelevation) {
		try {
			// Gets route data
			$route = @MMF::getRoute(get_option('mmf_access_token'), get_option('mmf_access_token_secret'), $routeId);
		} catch (Exception $e) {
			echo "<div class=\"mmf-exception\"><div class=\"mmf-exception-title\">MapMyFitness Plugin Error: Route could not be loaded</div>This route may not exist, privacy settings are blocking it, or our servers are down.</div>";
			return;
		}

		// Initialize Output String
		$output = '';

		// Widget Header
		$output .= '
			<section class="mmf-widget" id="route-'.$routeId.'">
			    <header>
		            <a class="header-logo" href="http://www.mapmyfitness.com/routes/view/'.$routeId.'" target="_blank"><img class="logo" src="'.plugins_url('images/mmf-logo.png', __FILE__).'" /></a>
			        <h2>'.$route["name"].' Route</h2>
			    </header>
		';

		if($static=="true") {
			$thumbnailURL = stripslashes($route["_links"]["thumbnail"][0]["href"]);
			$thumbnailURL = str_replace("100x100", "500x500", $thumbnailURL);
			$output .= '
				<img src="' . $thumbnailURL . '" />
			';
		} else {
			$output .= '
				<div class="mmf-route-map" id="mmf-route-map-'.$routeId.'" style="height: '.$height.'px; width: 100%;">
						<div class="mmf_loading"></div>
				</div>
			';

			// Google Maps Script output
			$output .= '
				<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQ05JwE60aFa6Pm1ny7Fk61xXRozj1r08&sensor=false"></script>
				<script type="text/javascript">
				var map;
					function initialize() {
						var course = '.json_encode($route["points"]).';

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

						map = new google.maps.Map(
							document.getElementById("mmf-route-map-'.$routeId.'"),
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

		} //end else

        // Highcharts graph output
        if($showelevation == "true")
        {
            wp_enqueue_script('mmf_highcharts_js', plugins_url('js/highcharts.js', __FILE__), array('jquery'), '1.0', true);

            $routeGraphDataResults = getRouteGraphData($route);

            $highChartSeries = $routeGraphDataResults['highChartSeries'];
            $minElevation = $routeGraphDataResults['minElevation']-100;
            $maxElevation = $routeGraphDataResults['maxElevation'];
            $totalRouteDistance = $routeGraphDataResults['routeTotalDistance'];
            $routeName = $routeGraphDataResults['routeName'];
            $routePoints = $routeGraphDataResults['routePoints'];

            $output .= '
            <script>
                var markersArray = [];
                function clearOverlays() {
                    for (var i = 0; i < markersArray.length; i++ ) {
                        markersArray[i].setMap(null);
                    }
                }
                function pointMouseOver(){
                    if (typeof map === "undefined")
                        return;

                    clearOverlays();

                    var routePoints = ' . json_encode($routePoints) . ';

                    var myLatlng = new google.maps.LatLng(routePoints[this.id]["lat"], routePoints[this.id]["lng"]);
                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map
                    });
                    markersArray.push(marker);
                }
                jQuery(document).ready(function () {
                    jQuery("#mmf-route-map-elevation-'.$routeId.'").highcharts({
                        chart: {
                            type: "line",
                            spacing: [10,0,0,0]
                        },
                        title: {
                            text: " ",
                            x: -20 //center
                        },
                        credits: {
                            enabled:false
                        },
                        plotOptions:{
                            area:{
                               fillColor:"#e6e5d6",
                               lineColor:"#db0b0e",
                               marker:{
                                    enabled:false
                                }
                            },
                            series:{
                                turboThreshold: 0,
                                point:{
                                    events:{
                                        mouseOver:pointMouseOver
                                    }
                                }
                            }
                        },
                        xAxis: {
                            title: {
                                text: ""
                            }
                        },
                        yAxis: {
                            title: {
                                text: ""
                            },
                            min: ' . floor($minElevation) . ',
                            max: ' . ceil($maxElevation) . ',
                            tickInterval: 91
                        },
                        tooltip: {
                            formatter: function() {
                                return  parseFloat(this.y).toFixed(2) + " ft at " + parseFloat(this.x).toFixed(2) + "mi";
                            }
                        },
                        legend: {
                            enabled:false
                        },
                        series: [{
                            type: "area",
                            data: ' . json_encode($highChartSeries) . '
                        }]
                    });
                });
            </script>
            <div class="mmf-evelation-label">Elevation (ft)</div>
            <div id="mmf-route-map-elevation-'.$routeId.'" style="height: 120px;"></div>
            ';
        }

        $output .= "</section>";

		return $output;
	}

	function mmf_route_shortcode($atts, $content = null) {
		// First verify the stored access tokens
		if (!verifyAccessTokens()) {
			return msgInvalidAccessTokens();
		}

		// Extract the shortcode attributes
//		extract(shortcode_atts(array(
//	        "id" => 123456,
//	        "height" => 400,
//			"static" => false
//	    ), $atts));

        // Extract the shortcode attributes
        $args = shortcode_atts(array(
            "id" => "",
            "showelevation"=>"false",
            "staticmap"=>"false",
            "height"=>400), $atts);
        $id = $args['id'];
        $showelevation = $args['showelevation'];
        $height = $args['height'];
        $static = $args['staticmap'];


        // Render the widget
		return mmf_route($id, $height, $static, $showelevation);
	}

	add_shortcode('mmf-route', 'mmf_route_shortcode');

?>