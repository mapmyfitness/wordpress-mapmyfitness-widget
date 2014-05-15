<?php
	include_once('../../../wp-load.php');
	include_once('mmf-sdk/MMF.php');
?>

<div class="mmf-admin-leftmenu">
	<ul>
		<li><a href="javascript:void(0);" data-formid="mmf_admin_form_recentworkouts">Recent Workouts</a></li>
		<li><a href="javascript:void(0);" data-formid="mmf_admin_form_coursemap">Course Map</a></li>
		<li><a href="javascript:void(0);" data-formid="mmf_admin_form_routemap">Route Map</a></li>
	</ul>
</div>

<div class="mmf-admin-title">Add MapMyFitness Widget</div>

<div class="mmf-admin-rightcontent">
	<div id="mmf_admin_form_pleaseselect" class="mmf-admin-form" data-title="Recent Workouts" data-widgetname="pleaseselect">
		<p>Please select a widget to add from the list on the side.</p>
	</div>

	<div id="mmf_admin_form_recentworkouts" class="mmf-admin-form" data-title="Recent Workouts" data-widgetname="recentworkouts">
		<p>The recent workouts widget does not require any special parameters. Click "Add Widget" to add it to your post.</p>
	</div>

	<div id="mmf_admin_form_coursemap" class="mmf-admin-form" data-title="Course Map" data-widgetname="coursemap">
		<p>How would you like to find the Course to map?</p>
		<div class="mmf-tabs">
			<div class="mmf-tabs-inner">
				<div class="tab-selectors">
					<ul>
						<li><a class="tab-selector" data-tabid="course-tab-1">URL</a></li>
						<li><a class="tab-selector" data-tabid="course-tab-2">ID</a></li>
					</ul>
					<div style="clear:both;height:0;"></div>
				</div>
				<div class="tab-content">
					<div data-tabid="course-tab-1">
						<p>
							<input style="width: 100%" id="mmf_coursemap_url" type="text" placeholder="Enter/Paste Course URL here" />
						</p>
					</div>
					<div data-tabid="course-tab-2">
						<p>
							<input style="width: 100%" id="mmf_coursemap_id" type="text" placeholder="Enter/Paste Course ID here" />
						</p>
					</div>
				</div>
			</div>
		</div>
		<p>
			Map Type:
			<select id="mmf_coursemap_type">
				<option value="dynamic">Dynamic</option>
				<option value="static">Static</option>
			</select>
		</p>
		<p>
			Leaderboard:
			<select id="mmf_coursemap_leaderboard">
				<option value="hide">Hide</option>
				<option value="show">Show</option>
			</select>
		</p>
		<p style="display:none;">
			Leaderboard Activity Type:
			<select style="width: 250px;" id="mmf_coursemap_leaderboard_activitytype" disabled="true">
				<option value="0">Loading Activity Types...</option>
			</select>
		</p>
	</div>

	<div id="mmf_admin_form_routemap" class="mmf-admin-form" data-title="Route Map" data-widgetname="routemap">
		<p>How would you like to find the Route to map?</p>
		<div class="mmf-tabs">
			<div class="mmf-tabs-inner">
				<div class="tab-selectors">
					<ul>
						<li><a class="tab-selector" data-tabid="route-tab-1">Bookmarked</a></li>
						<li><a class="tab-selector" data-tabid="route-tab-2">URL</a></li>
						<li><a class="tab-selector" data-tabid="route-tab-3">ID</a></li>
					</ul>
					<div style="clear:both;height:0;"></div>
				</div>
				<div class="tab-content">
					<div data-tabid="route-tab-1">
						<p>
							<select style="width: 472px;" id="mmf_routemap_bookmarkselect" disabled="true">
								<option value="0">Loading bookmarked routes...</option>
							</select>
							&nbsp;<a id="mmf_routemap_bookmark_details_link" href="javascript:void(0);" class="disabled">View Details</a>
						</p>
					</div>
					<div data-tabid="route-tab-2">
						<p>
							<input style="width: 100%" id="mmf_routemap_url" type="text" placeholder="Enter/Paste Route URL here" />
						</p>
					</div>
					<div data-tabid="route-tab-3">
						<p>
							<input style="width: 100%" id="mmf_routemap_id" type="text" placeholder="Enter/Paste Route ID here" />
						</p>
					</div>
				</div>
			</div>
		</div>
		<p>
			Map Type:
			<select id="mmf_routemap_type">
				<option value="dynamic">Dynamic</option>
				<option value="static">Static</option>
			</select>
		</p>
		<p>
			Show Elevation:
			<select id="mmf_routemap_elevation">
				<option value="no">No</option>
				<option value="yes">Yes</option>
			</select>
		</p>
	</div>
</div>

<div class="mmf-admin-toolbar">
	<div class="mmf-admin-toolbar-buttons">
		<a href="javascript:void(0);" class="button button-large" id="mmf_popup_cancel_button">Cancel</a>
		<a href="javascript:void(0);" class="button button-primary button-large" id="mmf_popup_add_button">Add Widget</a>
	</div>
</div>

<div class="mmf-admin-logo"></div>

<div id="mmf-admin-popup-loader"><div class="mmf_loading"></div></div>

<script>mmf_init_admin_popup(); mmf_init_popup_tabs();</script>