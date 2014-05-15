jQuery(document).ready(function() {
	var $ = jQuery;

	//Remove button
	$(document).delegate(".mmf-aggregates-widget-admin .removeBtn", "click", function() {
		var $parent = $(this).parents(".widget-content");

		if($parent.find(".selectedSel").is(":empty")) {
			alert("There is no aggregate to remove!");
		} else {
			var selectedVal = $parent.find(".selectedSel option:selected").val();
			var currentVal = $parent.find(".selectedSelHidden").val();
			var currentValSplit = currentVal.split(",");

			var indexToSplice = -1;
			for (var i = 0; i < currentValSplit.length; i++) {
				if (currentValSplit[i] == selectedVal) indexToSplice = i;
			}

			if (indexToSplice > -1) currentValSplit.splice(indexToSplice, 1);

			$parent.find(".selectedSelHidden").val(currentValSplit.join(","));
			$parent.find(".selectedSel option:selected").remove();
		}
	});

	//Activity select
	$(document).delegate(".mmf-aggregates-widget-admin .activitySel", "change", function() {
		var $parent = $(this).parents(".widget-content");

		var val = $("option:selected", this).val();
		var text = $("option:selected", this).text();
		//alert(val + " " + text);
		if(val == "select") {
			$parent.find(".durationP").hide();
			$parent.find(".durationSel").val("select");
			$parent.find(".statP").hide();
			$parent.find(".statSel").val("select");
			$parent.find(".addP").hide();
		}
		else
			$parent.find(".durationP").show();
	});

	//Duration select
	$(document).delegate(".mmf-aggregates-widget-admin .durationSel", "change", function() {
		var $parent = $(this).parents(".widget-content");

		var val = $("option:selected", this).val();
		var text = $("option:selected", this).text();
		//alert(val + " " + text);
		if(val == "select") {
			$parent.find(".statP").hide();
			$parent.find(".statSel").val("select");
			$parent.find(".addP").hide();
		}
		else
			$parent.find(".statP").show();
	});

	//Stat select
	$(document).delegate(".mmf-aggregates-widget-admin .statSel", "change", function() {
		var $parent = $(this).parents(".widget-content");

		var val = $("option:selected", this).val();
		var text = $("option:selected", this).text();
		//alert(val + " " + text);
		if(val == "select")
			$parent.find(".addP").hide();
		else
			$parent.find(".addP").show();
	});

	//Add button
	$(document).delegate(".mmf-aggregates-widget-admin .addBtn", "click", function() {
		var $parent = $(this).parents(".widget-content");

		var val = $parent.find(".activitySel option:selected").val();
		val += "|" + $parent.find(".durationSel option:selected").val();
		val += "|" + $parent.find(".statSel option:selected").val();
		var text = $parent.find(".activitySel option:selected").text();
		text += " | " + $parent.find(".durationSel option:selected").text();
		text += " | " + $parent.find(".statSel option:selected").text();

		//alert(val + "\n" + text);
		if($parent.find(".selectedSel option[value=\"" + val + "\"]").length == 0) {
			$parent.find(".selectedSel").append("<option value=\"" + val + "\">" + text + "</option>");
			$parent.find(".removeP").show();
			$parent.find(".activitySel").val("select");
			$parent.find(".durationP").hide();
			$parent.find(".durationSel").val("select");
			$parent.find(".statP").hide();
			$parent.find(".statSel").val("select");
			$parent.find(".addP").hide();

			var currentVal = $parent.find(".selectedSelHidden").val();
			var newVal = currentVal;
			if (currentVal != "") newVal = newVal + ",";
			$parent.find(".selectedSelHidden").val(newVal + val);

		} else {
			alert("This aggregate has already been added.");
		}
	});
});
