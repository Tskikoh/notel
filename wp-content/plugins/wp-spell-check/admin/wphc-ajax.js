function wphc_init_scan() {
	jQuery.ajax({
		url: ajax_object.ajax_url,
		type: "POST",
		data: {
			action: 'results_hc',
		},
		dataType: 'html',
		success: function(response) {
			if (response == 'true') { wphc_recheck_scan(); console.log(response); }
			else { window.setInterval( wphc_init_scan(),1000 ); console.log(response); }
		}
	});
}

function wphc_recheck_scan() {
	jQuery.ajax({
		url: ajax_object.ajax_url,
		type: "POST",
		data: {
			action: 'results_hc',
		},
		dataType: 'html',
		success: function(response) {
			if (response == 'true') { window.setInterval(wphc_recheck_scan(), 1000 ); console.log(response); }
			else { wphc_finish_scan(); console.log(response); }
		}
	});
}

function wphc_finish_scan() {
	jQuery.ajax({
		url: ajax_object.ajax_url,
		type: "POST",
		data: {
			action: 'finish_scan_hc',
		},
		dataType: 'html',
		success: function(response) {
			window.location.href = encodeURI("?page=wp-spellcheck-html.php&wpsc-script=noscript");
		}
	});
}

window.setInterval( wphc_recheck_scan(),500 );