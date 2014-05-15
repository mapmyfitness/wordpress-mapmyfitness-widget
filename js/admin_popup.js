var mmf_load_admin_popup = function(plugin_url) {
	// Get HTML by Ajax
	jQuery.ajax({
		type: "GET",
		url: plugin_url + "mmf_admin_popup.php"
	}).done(function(result) {
		jQuery("#mmf_admin_content").html(result);
	});
}

var mmf_init_admin_popup = function() {
	// Set selected form variable
	mmf_selected_form = "pleaseselect";

	// Show the please select text
	jQuery("#mmf_admin_form_pleaseselect").show();

	// Bind sidebar links
	jQuery(".mmf-admin-leftmenu a").each(function() {
		jQuery(this).click(function() {
			// Make clicked on link selected
			jQuery(".mmf-admin-leftmenu a").removeClass('selected');
			jQuery(this).addClass("selected");

			// Call init function for tab, if exists
			if (window[jQuery(this).data("formid") + "_init"]) {
				window[jQuery(this).data("formid") + "_init"]();
			}

			// Display the form we want
			var $form = jQuery("#" + jQuery(this).data("formid"));
			jQuery(".mmf-admin-title").html($form.data("title"));
			jQuery(".mmf-admin-form").fadeOut(50);
			setTimeout(function() {$form.fadeIn(200);}, 100);

			// Set the variable for selected mmf-form
			mmf_selected_form = $form.data("widgetname");
		});
	});

	// Bind close button
	jQuery("#mmf_popup_cancel_button").click(function() {
		jQuery("#mmf_admin_popup").trigger('close');
	});

	// Bind add widget button
	jQuery("#mmf_popup_add_button").click(function() {
		var $ = jQuery;

		$('#mmf-admin-popup-loader').show();

		switch(mmf_selected_form) {
			case "pleaseselect":
				alert("Please select a widget to add");
				$('#mmf-admin-popup-loader').hide();
				break;

			case "recentworkouts":
				send_to_editor(" [mmf-recent-workouts] ");
				jQuery("#mmf_admin_popup").trigger('close');
				break;

			case "coursemap":
				var id = "";

				switch(jQuery('#mmf_admin_form_coursemap .tab-selectors li.active a').data("tabid")) {
					case "course-tab-1":
						var url = jQuery('#mmf_coursemap_url').val();

						if (url.indexOf("courses/") < 0) {
							alert("Missing or incorrect URL form. Please try again.");
							$('#mmf-admin-popup-loader').hide();
							return;
						}

						if (url.indexOf("?") < 0) {
							id = url.substring(url.indexOf("courses/") + 8);
						} else {
							id = url.substring(url.indexOf("courses/") + 8, url.indexOf("?"));
						}
						break;

					case "course-tab-2":
						id = jQuery('#mmf_coursemap_id').val();
						if (id == "" || id == undefined) {
							alert("No Course ID was entered. Please try again.");
							$('#mmf-admin-popup-loader').hide();
							return;
						} else if (id % 1 != 0) {
							alert("Invalid Course ID. Please try again.");
							$('#mmf-admin-popup-loader').hide();
							return;
						}
						break;
				}

				$.ajax({
					url: window.mmf_plugin_url + "helpers/get_course_details.php",
					data: {courseid: id},
					success: function(data) {
						var type = jQuery("#mmf_coursemap_type").val();
						var leaderboard = jQuery("#mmf_coursemap_leaderboard").val();
						var activitytype = jQuery("#mmf_coursemap_leaderboard_activitytype").val();

						var output = ' [mmf-course id="' + id + '"';

						if (type == "static") output += ' staticmap="true"';

						if (leaderboard == "show") output += ' leaderboard="true"' + ' activitytype="' + activitytype + '"';

						output += ']';

						send_to_editor(output);
						jQuery("#mmf_admin_popup").trigger('close');
					},
					error: function(data) {
						alert("This course could not be found! Perhaps privacy settings are preventing you from accessing it? Or it simply doesn't exist. Please try again.");
						$('#mmf-admin-popup-loader').hide();
					}
				});

				break;

			case "routemap":

				var id = "";

				switch(jQuery('#mmf_admin_form_routemap .tab-selectors li.active a').data("tabid")) {
					case "route-tab-1":
						id = jQuery('#mmf_routemap_bookmarkselect').val();
						if (id == 0) {
							alert("Woah... slow down there! Please wait for the bookmarked route to load first!");
							$('#mmf-admin-popup-loader').hide();
							return;
						}
						break;

					case "route-tab-2":
						var url = jQuery('#mmf_routemap_url').val();

						if (url.indexOf("route-") < 0) {
							alert("Missing or incorrect URL form. Please try again.");
							$('#mmf-admin-popup-loader').hide();
							return;
						}

						if (url.indexOf("?") < 0) {
							id = url.substring(url.indexOf("route-") + 6);
						} else {
							id = url.substring(url.indexOf("route-") + 6, url.indexOf("?"));
						}
						break;

					case "route-tab-3":
						id = jQuery('#mmf_routemap_id').val();
						if (id == "" || id == undefined) {
							alert("No Route ID was entered. Please try again.");
							$('#mmf-admin-popup-loader').hide();
							return;
						} else if (id % 1 != 0) {
							alert("Invalid Route ID. Please try again.");
							$('#mmf-admin-popup-loader').hide();
							return;
						}
						break;

					case "route-tab-4":
						alert("I haven't implemented the Nearby tab yet... Try a different method :)");
						return;
						break;
				}

				// Check if route id works
				$.ajax({
					url: window.mmf_plugin_url + "helpers/get_route_details.php",
					data: {routeid: id},
					success: function(data) {
						var type = jQuery("#mmf_routemap_type").val();
						var showElevation = jQuery("#mmf_routemap_elevation").val()

						var output = ' [mmf-route id="' + id + '"';

						if (type == "static") output += ' staticmap="true"';

		                if (showElevation == "yes") output += ' showElevation="true"';

						output += ']';

						send_to_editor(output);
						jQuery("#mmf_admin_popup").trigger('close');
					},
					error: function(data) {
						alert("This route could not be found! Perhaps privacy settings are preventing you from accessing it? Or it simply doesn't exist. Please try again.");
						$('#mmf-admin-popup-loader').hide();
					}
				});

				break;
		}
	});

	// bind showing/hiding of the leaderboards activity type
	jQuery('#mmf_coursemap_leaderboard').change(function() {
		if (jQuery(this).val() == "show")
			jQuery("#mmf_coursemap_leaderboard_activitytype").parent("p").show();
		else
			jQuery("#mmf_coursemap_leaderboard_activitytype").parent("p").hide();
	});

}

var mmf_close_admin_popup = function() {
	jQuery("#mmf_admin_content").html('<div class="mmf_loading"></div>');
}


// Initialize tabs interfaces
var mmf_init_popup_tabs = function() {
	var $ = jQuery;

	$('#mmf_admin_content .mmf-tabs').each(function(){
		var $tabs = $(this);
		$tabs.find('div.tab-content > div:first').show();
		$tabs.find('ul li:first').addClass('active');

		$tabs.find('ul li a').click(function() {
			$tabs.find('ul li').removeClass('active');
			$(this).parent('li').addClass('active');

			$tabs.find('div.tab-content > div').hide();

			var currentTab = 'div[data-tabid="' + $(this).data('tabid') + '"]';
			$tabs.find(currentTab).show();
		});
	});
}

// Individual Tab init functions

var mmf_admin_form_routemap_init = function() {
	var $ = jQuery;

	//Reset
	$('#mmf_routemap_bookmarkselect').html('<option value="0">Loading bookmarked routes...</option>');
	$('#mmf_routemap_bookmarkselect').attr('disabled', 'true');
	$('#mmf_routemap_bookmark_details_link').addClass("disabled");
	$('#mmf_routemap_bookmark_details_link').unbind();

	$.ajax({
		url: window.mmf_plugin_url + "helpers/get_routes_bookmarked.php"
	})
	.done(function(result) {
		$('#mmf_routemap_bookmarkselect').html('');
		$('#mmf_routemap_bookmarkselect').removeAttr('disabled');

		$('#mmf_routemap_bookmark_details_link').removeClass("disabled");
		$('#mmf_routemap_bookmark_details_link').click(function() {
			window.open('http://www.mapmyfitness.com/routes/view/' + $('#mmf_routemap_bookmarkselect').val() , '_blank')
		});

		for (var i = 0; i < result.length; i++) {
			var miles = result[i].distance * 0.000621371;

			$('#mmf_routemap_bookmarkselect')
				.append('<option value="' + result[i]._links.self[0].id + '">' + result[i].name + ' - ' + miles.toFixed(2) + ' mi' + ' (' + result[i].city + ', ' + result[i].state + ')' + '</option>');
		}
	});


}

var mmf_admin_form_coursemap_init = function() {
	var $ = jQuery;

	if ($('#mmf_coursemap_leaderboard_activitytype').val() == "0") {
		$.ajax({
			url: window.mmf_plugin_url + "helpers/get_activity_types.php"
		})
		.done(function(result) {
			$('#mmf_coursemap_leaderboard_activitytype').html('');
			$('#mmf_coursemap_leaderboard_activitytype').removeAttr('disabled');

			for (var i = 0; i < result.length; i++) {
				$('#mmf_coursemap_leaderboard_activitytype')
					.append('<option value="' + result[i]._links.self[0].id + '">' + result[i].name + '</option>');
			}
		});
	}
}