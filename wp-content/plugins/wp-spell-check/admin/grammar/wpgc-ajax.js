function wpgc_init_scan() {
	jQuery.ajax({
		url: ajax_object.ajax_url,
		type: "POST",
		data: {
			action: 'results_gc',
		},
		dataType: 'html',
		success: function(response) {
			if (response == 'true') { wpgc_recheck_scan(); console.log(response); }
			else { window.setInterval( wpgc_init_scan(),1000 ); console.log(response); }
		}
	});
}

function wpgc_recheck_scan() {
	jQuery.ajax({
		url: ajax_object.ajax_url,
		type: "POST",
		data: {
			action: 'results_gc',
		},
		dataType: 'html',
		success: function(response) {
			if (response == 'true') { window.setInterval(wpgc_recheck_scan(), 1000 ); console.log(response); }
			else { wpgc_finish_scan(); console.log(response); }
		}
	});
}

function wpgc_finish_scan() {
	jQuery.ajax({
		url: ajax_object.ajax_url,
		type: "POST",
		data: {
			action: 'finish_scan_gc',
		},
		dataType: 'html',
		success: function(response) {
			window.location.href = encodeURI("?page=wp-spellcheck-grammar.php&wpsc-script=noscript");
		}
	});
}

window.setInterval( wpgc_recheck_scan(),500 );