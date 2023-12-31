jQuery("#start-crawler").click(function() {
	jQuery("#links-section").html('');
	jQuery(".loader").show();
	jQuery.ajax({
		type : "POST",
		url : myAjax.ajaxurl,
		data : {action: "start_crawler"},
		success: function(response) {
			jQuery(".loader").hide();
		   jQuery("#links-section").html(response);
		   jQuery("#start-crawler").attr("disabled","disabled");
		},
		error: function (response) {
			jQuery(".loader").hide();
		},
	}) 
});

jQuery("#view-links").click(function() {
	jQuery("#links-section").html('');
	jQuery(".loader").show();
	jQuery.ajax({
		type : "POST",
		url : myAjax.ajaxurl,
		data : {action: "view_links"},
		success: function(response) {
			jQuery(".loader").hide();
			jQuery("#links-section").html(response);
		},
		error: function (response) {
			jQuery(".loader").hide();
		},
	}) 
});

jQuery("#reset-crawler").click(function() {
	jQuery("#links-section").html('');
	jQuery(".loader").show();
	jQuery.ajax({
		type : "POST",
		url : myAjax.ajaxurl,
		data : {action: "reset_crawler"},
		success: function(response) {
			jQuery(".loader").hide();
			jQuery("#links-section").html(response);
			jQuery("#start-crawler").removeAttr("disabled");
			jQuery("#view-links").hide();
		},
		error: function (response) {
			jQuery(".loader").hide();
		},
	}) 
});