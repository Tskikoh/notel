<?php
/* Admin Classes */
/*
	Works in the background: yes
	Pro version scans the entire website: yes
	Sends email reminders: yes
	Finds place holder text: yes
	Custom Dictionary for unusual words: yes
	Scans Password Protected membership Sites: yes
	Unlimited scans on my website: Yes


	Scans Categories: Yes WP Spell Check Pro
	Scans SEO Titles: Yes WP Spell Check Pro
	Scans SEO Descriptions: Yes WP Spell Check Pro
	Scans WordPress Menus: Yes WP Spell Check Pro
	Scans Page Titles: Yes WP Spell Check Pro
	Scans Post Titles: Yes WP Spell Check Pro
	Scans Page slugs: Yes WP Spell Check Pro
	Scans Post Slugs: Yes WP Spell Check Pro
	Scans Post categories: Yes WP Spell Check Pro

	Privacy URI: https://www.wpspellcheck.com/privacy-policy/
	Pro Add-on / Home Page: https://www.wpspellcheck.com/
	Pro Add-on / Prices: https://www.wpspellcheck.com/features/
*/
class wpgc_table extends WP_List_Table {

	function __construct() {
		global $status, $page;
		
		
		parent::__construct( array(
			'singular' => 'result',
			'plural' => 'results',
			'ajax' => true
		) );
	}
	
	function column_default($item, $column_name) {
		return print_r($item,true);
	}
	
	
	/*function column_page($item) {
		set_time_limit(600); 
		global $wpdb;
		global $wpgc_options;


		$actions = array (
			'Ignore'      			=> sprintf('<input type="checkbox" class="wpgc-ignore-checkbox" name="ignore-word[]" value="' . $item['id'] . '" />Ignore'),
			'Edit'					=> sprintf('<a href="#" class="wpsc-edit-button-grammar" page_type="' . $item['page_type'] . '" id="wpsc-word-' . $item['word'] . '">Edit</a>'),
		);
		
		
		return sprintf('%1$s<span style="background-color:#0096ff; float: left; margin: 3px 5px 0 -30px; display: block; width: 12px; height: 12px; border-radius: 16px; opacity: 1.0;"></span>%3$s',
            stripslashes(stripslashes($item['word'])),
            $item['ID'],
            $this->row_actions($actions)
        );
	}*/
	
	function column_page ($item) {
		
		$actions = array ();
		
		$page_name = get_the_title($item['page_id']);
		
		$actions = array (
			'Edit'					=> sprintf('<a href="/wp-admin/post.php?post=' . $item['page_id'] . '&action=edit" class="wpsc-edit-button-grammar" target="_blank">Edit</a>'),
		);
		
		return sprintf('%1$s <span style="color:silver"></span>%3$s',
            $page_name,
            $item['ID'],
            $this->row_actions($actions)
        );
	}
	
	function column_grammar($item) {
		
		$actions = array ();
		
		
		return sprintf('%1$s <span style="color:silver"></span>%3$s',
            $item['grammar'],
            $item['ID'],
            $this->row_actions($actions)
        );
	}

	
	function get_columns() {
		global $wpdb;
		global $pro_included;
		global $ent_included;
		wpsc_set_global_vars();
		global $wpgc_settings;
		
		$options_list = $wpgc_settings;
		$grammar = '<div style="position: relative; height: 100%;"># of Grammar Errors</div>';
		
	
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'page' => 'Page',
			'grammar' => $grammar,
		);
		return $columns;
	}
	
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'page' => array('page',false)
		);
		return $sortable_columns;
	}

	
	function single_row( $item ) {
		static $row_class = 'wpsc-row';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr class="wpsc-row" id="wpsc-row-' . $item['id'] . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
	
	function prepare_items() {
		error_reporting(0);
		global $wpdb;
		
		$per_page = 20;
		
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		
		$table_name = $wpdb->prefix . 'spellcheck_grammar';
		if ($_GET['s'] != '') {
			//$results = $wpdb->get_results('SELECT * FROM ' . $table_name . 'WHERE page_name LIKE "%' . $_GET['s-top'] . '%"', OBJECT);
			$results = $wpdb->get_results('SELECT * FROM ' . $table_name . ' ORDER BY grammar DESC', OBJECT);
		} elseif ($_GET['s-top'] != '') {
			$results = $wpdb->get_results('SELECT * FROM ' . $table_name . ' ORDER BY grammar DESC', OBJECT);
			//$results = $wpdb->get_results('SELECT * FROM ' . $table_name . 'WHERE page_name LIKE "%' . $_GET['s-top'] . '%"', OBJECT);
		} else {
			$results = $wpdb->get_results('SELECT * FROM ' . $table_name . ' ORDER BY grammar DESC', OBJECT);
		}

		$data = array();

		foreach($results as $word) {
				array_push($data, array('id' => $word->id, 'page_id' => $word->page_id, 'passive_voice' => $word->passive_voice, 'wordiness' => $word->wordiness, 'sentences' => $word->sentences, 'transitions' => $word->transitions, 'academic_style' => $word->academic_style, 'grammar' => $word->grammar, 'eggcorns' => $word->eggcorns, 'duplicate_spaces' => $word->duplicate_spaces));
		}
		
		function usort_reorder($a, $b) {
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'word'; 
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; 
			
			$result = strcmp($a[$orderby], $b[$orderby]); 
			return ($order==='asc') ? $result : -$result;
		}
		//usort($data, 'usort_reorder');
		
		
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		$this->items = $data;
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		) );		
	}
}

function wpgc_render_results() {
	$start = round(microtime(true),5);
	ini_set('memory_limit','8192M'); 
	set_time_limit(600); 
	global $wpdb;
	global $ent_included;
	global $base_page_max;
	$table_name = $wpdb->prefix . "spellcheck_grammar";
	$options_table = $wpdb->prefix . "spellcheck_grammar_options";
	$sc_options_table = $wpdb->prefix . "spellcheck_options";
	$post_table = $wpdb->prefix . "posts";
	$time_estimate = 0;
	
	if (!isset($_GET['action'])) $_GET['action'] = '';
	if (!isset($_GET['submit'])) $_GET['submit'] = '';
	
	wpsc_set_global_vars();
	$wpgc_settings = $wpdb->get_results("SELECT option_value FROM $options_table;");
	
	$message = '';
	
	$options_list = $wpgc_settings;
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type = 'post'");
	
	$pro_word_count = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='pro_error_count';");
	$pro_words = $pro_word_count[0]->option_value;
	
	$scan_message = "No scan currently running";
	
	$scan_progress = $wpdb->get_results("SELECT * FROM $options_table WHERE option_name='scan_running'");
	
	if ($scan_progress[0]->option_value == "true") $scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan is currently in progress for <span class="sc-message" style="color: rgb(0, 150, 255); font-weight: bold;">' . $options_list[7]->option_value . '</span>. <a href="/wp-admin/admin.php?page=wp-spellcheck-grammar.php">Click here</a> to see scan results.';
	
	$check_scan = wpgc_check_scan_progress();
	
	$post_types = get_post_types();
	$post_type_list = array();
	foreach ($post_types as $type) {
		if ($type != 'revision' && $type != 'page' && $type != 'slider' && $type != 'attachment' && $type != 'optionsframework' && $type != 'product' && $type != 'wpsc-product' && $type != 'wpcf7_contact_form' && $type != 'nav_menu_item' && $type != 'gal_display_source' && $type != 'lightbox_library' && $type != 'wpcf7s')
			array_push($post_type_list, $type);
	}
	
	$post_status = array("publish", "draft");

	$post_count = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='post' AND (post_status='draft' OR post_status='publish')");
	$page_count = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='page' AND (post_status='draft' OR post_status='publish')");
	$total_pages = $page_count;
	
	$post_scan_count = $options_list[5]->option_value;
	if ($post_scan_count > $post_count) $post_scan_count = $post_count;
	$total_posts = $post_count;
	
	$max_pages = $wpdb->get_results("SELECT option_value FROM $sc_options_table WHERE option_name = 'pro_max_pages'");
	$max_pages = intval($max_pages[0]->option_value);

	if (!$ent_included) $max_pages = $base_page_max;
	
	if ($check_scan && $_GET['wpsc-script'] != 'noscript') {
		wp_enqueue_script( 'wpgc-results-ajax', plugin_dir_url( __FILE__ ) . '/wpgc-ajax.js', array('jquery') );
		wp_localize_script( 'wpgc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Posts') {
		wpgc_clear_results(); //Clear out results table in preparation for a new scan
		$rng_seed = rand(0,999999999);
		$time_estimate = intval($total_posts / 8);
		$time_estimate= time_elapsed($time_estimate);
		$wpdb->update($options_table, array('option_value' => 0), array('option_name' => 'pro_error_count'));
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_running'));
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'post_running'));
		$wpdb->update($options_table, array('option_value' => 'Posts'), array('option_name' => 'last_scan_type'));
		$wpdb->update($options_table, array("option_value" => '0'), array("option_name" => "last_scan_errors"));
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Posts</span>. Estimated time for completion is ' . $time_estimate. ' seconds. The page will automatically refresh when the scan has finished.';
		
		wp_enqueue_script( 'wpgc-results-ajax', plugin_dir_url( __FILE__ ) . '/wpgc-ajax.js', array('jquery') );
		wp_localize_script( 'wpgc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_schedule_single_event(time(), 'wpgc_check_posts', array ($rng_seed, true));
	} elseif ($_GET['action'] == 'check' && $_GET['submit'] == 'Pages') {
		wpgc_clear_results(); //Clear out results table in preparation for a new scan
		$rng_seed = rand(0,999999999);
		$time_estimate = intval($total_posts / 8);
		$time_estimate= time_elapsed($time_estimate);
		$wpdb->update($options_table, array('option_value' => 0), array('option_name' => 'pro_error_count'));
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_running'));
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'page_running'));
		$wpdb->update($options_table, array('option_value' => 'Pages'), array('option_name' => 'last_scan_type'));
		$wpdb->update($options_table, array("option_value" => '0'), array("option_name" => "last_scan_errors"));
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Pages</span>. Estimated time for completion is ' . $time_estimate. ' seconds. The page will automatically refresh when the scan has finished.';
		
		wp_enqueue_script( 'wpgc-results-ajax', plugin_dir_url( __FILE__ ) . '/wpgc-ajax.js', array('jquery') );
		wp_localize_script( 'wpgc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_schedule_single_event(time(), 'wpgc_check_pages', array ($rng_seed, true));
	} elseif ($_GET['action'] == 'check' && $_GET['submit'] == 'Entire Site') {	
		$time_estimate = intval(($total_posts + $total_pages) / 8);	
		$time_estimate= time_elapsed($time_estimate);
		
		$wpdb->update($options_table, array('option_value' => 0), array('option_name' => 'pro_error_count'));
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Entire Site</span>. Estimated time for completion is ' . $time_estimate. ' seconds. The page will automatically refresh when the scan has finished.';
			
		wp_enqueue_script( 'wpgc-results-ajax', plugin_dir_url( __FILE__ ) . '/wpgc-ajax.js', array('jquery') );
		wp_localize_script( 'wpgc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		$wpdb->update($options_table, array("option_value" => '0'), array("option_name" => "last_scan_errors"));
		$wpdb->update($options_table, array('option_value' => 'Entire Site'), array('option_name' => 'last_scan_type'));
		sleep(1);;		
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_running'));
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'post_running'));
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'page_running'));
		
		wpgc_scan_site();
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Clear Results') {
		$scan_message = 'All spell check results have been cleared';
		wpgc_clear_results();
	}
	if ($_GET['submit'] == "Stop Scans") {
		$scan_message = "All current spell check scans have been stopped.";
		wpgc_clear_scan();
	}
	
	if ($_GET['submit'] == "Create Pages") {
	
		for ($x=5001;$x<=10000;$x++) {
			$post_args = array(
				'post_title'	=> 'Post-' . $x,
				'post_content'	=> 'Grammark helps improve writing style & grammar and teaches students to self-edit. Basically, it finds things that grammarians consider bad, highlights them, and suggests improvements. So writers can measure progress, it gives a "score" based on problems per document length, updated whenever the writer fixes a problem.',
				'post_status'	=> 'publish',
				'post_type'		=> 'post',
				'post_author'	=> get_current_user_id()
			);
		}
		
	}
		
	$list_table = new wpgc_table();
	$list_table->prepare_items();
	?>
		<?php show_feature_window(); ?>
		<?php check_install_notice(); ?>
		
	<style>.search-box input[type=submit] { color: white; background-color: #00A0D2; border-color: #0073AA; } #cb-select-all-1,#cb-select-all-2 { display: none; } td.word { font-size: 15px; } p.submit { display: inline-block; margin-left: 8px; } h3.sc-message { width: 49%; display: inline-block; padding-left: 8px; font-weight: normal; } .wpsc-mouseover-text-page,.wpsc-mouseover-text-post,.wpsc-mouseover-text-refresh { color: black; font-size: 12px; width: 225px; display: inline-block; position: absolute; margin: -13px 0 0 -270px; padding: 3px; border: 1px solid black; border-radius: 10px; opacity: 0; background: white; z-index: -100; } .wpsc-row .row-actions, .wpsc-row .row-actions *{ visibility: visible!important; left: 0!important; } #current-page-selector { width: 12%; } .hidden { display: none; } .wpsc-scan-nav-bar { border-bottom: 1px solid #BBB; margin-botton: 15px; } .wpsc-scan-nav-bar a { text-decoration: none; margin: 5px 5px -1px 5px; padding: 8px; border: 1px solid #BBB; display: inline-block; font-weight: bold; color: black; font-size: 14px; } .wpsc-scan-nav-bar a.selected { border-bottom: 1px solid white; background: white; } #wpsc-empty-fields-tab .button-primary { background: #73019a; border-color: #51006E; text-shadow: 1px 1px #51006d; box-shadow: 0 1px 0 #51006d; } #wpsc-empty-fields-tab .button-primary:hover { background: #9100c3 } #wpsc-empty-fields-tab .button-primary:active { background: #51006d; }.wpsc-scan-buttons input#submit:active { margin-top: -7px; } #wpgc-scan-results-tab .wp-list-table th { text-align: center; } .wpgc-desc .wpgc-desc-content { display: none; } .wpgc-desc-hover { display: block!important; position: relative; top: -125px; left: -55px; width: 125px; padding: 0px; margin: 0px; z-index: 100; height: 0px!important; } .wpgc-desc { position: absolute; bottom: 0px; margin-right: -4px; width: 125px; } #wpgc-scan-results-tab .wp-list-table td:not(.column-page) { text-align: center; }</style>
<div id="wpsc-dialog-confirm" title="Are you sure?" style="display: none;">
  <p>Would you like to Proceed with the changes?</p>
</div>
		<div class="wrap wpsc-table">
			<h2><a href="admin.php?page=wp-spellcheck-grammar.php"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/logo.png'; ?>" alt="WP Spell Check" /></a> <span style="position: relative; top: -15px;">Grammar Scan Results</span></h2>
			<div class="wpsc-scan-nav-bar">
				<a href="/wp-admin/admin.php?page=wp-spellcheck.php" id="wpsc-scan-results" name="wpsc-scan-results">Spelling Errors</a>
				<a href="#" class="selected" id="wpsc-grammar" name="wpsc-grammar">Grammar</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-seo.php" id="wpsc-empty-fields" name="wpsc-empty-fields">SEO Empty Fields</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-html.php" id="wpsc-grammar" name="wpsc-grammar">Broken Code</a>
			</div>
			<div id="wpgc-scan-results-tab" <?php if ($_GET['wpsc-scan-tab'] == 'empty') echo 'class="hidden"';?>>
			<form action="<?php echo admin_url('admin.php'); ?>" method='GET'>
				<div class="wpsc-scan-buttons" style="background: white; padding-left: 8px;">
				<h3 style="display: inline-block;">Scan:</h3>
				<p class="submit"><input style="background-color: #ffb01f; border-color: #ffb01f; box-shadow: 0px 1px 0px #ffb01f; text-shadow: 1px 1px 1px #ffb01f; font-weight: bold;" type="submit" name="submit" id="submit" class="button button-primary" value="Entire Site" <?php if (false) echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Pages" <?php if ($options_list[0]->option_value == "false") echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Posts" <?php if ($options_list[1]->option_value == "false") echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Clear Results"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="See Scan Results"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="Stop Scans"></p>
				<!--<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="Create Pages"></p>-->
				</div>
				<div style="padding: 5px; background: white; font-size: 12px;">
					<input type="hidden" name="page" value="wp-spellcheck-grammar.php">
					<input type="hidden" name="action" value="check">
					<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Last scan took " . $options_list[3]->option_value . "</h3>"; ?>
					<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>$scan_message</h3><br />"; ?>
					<?php if (!$ent_included) {
						if ($options_list[6]->option_value > 0 && !$ent_included) {
							echo "<h3 class='sc-message' style='color: rgb(225, 0, 0);'>Errors were found on other parts of your website. <a href='https://www.wpspellcheck.com/features/?utm_source=baseplugin&utm_campaign=upgradegram&utm_medium=grammar_scan&utm_content=7.0.2' target='_blank'>Click here</a> to upgrade to find and fix all errors.</h3>";
						} else {
							//echo "<h3 class='sc-message' style='color: rgb(225, 0, 0);'><a href='https://www.wpspellcheck.com/features' target='_blank'>Upgrade</a> to scan all parts of your website.</h3>";
						}
					} ?>
				</div>
			</form>
			<div style="float: right; width:23%; margin-left: 2%; margin-top: 50px">
				<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script type="text/javascript">
//<![CDATA[
if (typeof newsletter_check !== "function") {
window.newsletter_check = function (f) {
    var re = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-]{1,})+\.)+([a-zA-Z0-9]{2,})+$/;
    if (!re.test(f.elements["ne"].value)) {
        alert("The email is not correct");
        return false;
    }
    for (var i=1; i<20; i++) {
    if (f.elements["np" + i] && f.elements["np" + i].value == "") {
        alert("");
        return false;
    }
    }
    if (f.elements["ny"] && !f.elements["ny"].checked) {
        alert("You must accept the privacy statement");
        return false;
    }
    return true;
}
}
//]]>
</script>

<!--<div style="padding: 5px 5px 10px 5px; border: 3px solid #73019A; border-radius: 5px; background: white;">
<h2>Get on Our Priority Notification List</h2>
<form method="post" action="https://www.wpspellcheck.com/?na=s" onsubmit="return newsletter_check(this)">

<table cellspacing="0" cellpadding="3" border="0">

<tr>
	<th>Email</th>
	<td align="left"><input class="newsletter-email" type="email" name="ne" style="width: 100%;" size="30" required></td>
</tr>

<tr>
	<td colspan="2" class="newsletter-td-submit">
		<input class="newsletter-submit" type="submit" value="Sign me up"/>
	</td>
</tr>

</table>
</form>
</div>
<hr>-->
<div class="newsletter newsletter-subscription" style="padding: 5px 5px 10px 5px; border: 3px solid #008200; border-radius: 5px; background: white;">
<div class="wpsc-sidebar" style="margin-bottom: 15px;"><h2>Help to improve this plugin!</h2><center>Enjoyed this plugin? You can help by <a class="review-button" href="https://www.facebook.com/pg/wpspellcheck/reviews/" target="_blank">rating this plugin</a></center></div>
</div>
<hr>
<div style="padding: 5px 5px 10px 5px; border: 3px solid #0096FF; border-radius: 5px; background: white;">
				<a href="https://www.wpspellcheck.com/tutorials?utm_source=baseplugin&utm_campaign=toturial_rightside&utm_medium=grammar_check&utm_content=7.0.2" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/wp-spellcheck-tutorials.jpg'; ?>" style="max-width: 99%;" alt="Watch WP Spell Check Tutorials" /></a>
</div>
<hr>
<div style="padding: 5px 5px 10px 5px; border: 3px solid #D60000; border-radius: 5px; background: white; text-align: center;">
				<h2>Follow us on Facebook</h2>
				<div class="fb-page" data-href="https://www.facebook.com/wpspellcheck/" data-width="180px" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/wpspellcheck/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/wpspellcheck/">WP Spell Check</a></blockquote></div>
</div>
<hr>

<?php if (!$ent_included && !$pro_included) { ?>
<!--<div style="padding: 5px 5px 10px 5px; border: 1px solid #00BBC1; border-radius: 5px; background: white;">
				<div class="wpsc-sidebars" style="margin-bottom: 15px;"><h2>Want your entire website scanned?</h2>
					<p><a href="https://www.wpspellcheck.com/features/" target="_blank">Upgrade to WP Spell Check Pro<br />
					See Benefits and Features here »</a></p>
				</div>
</div>-->
<?php } ?>
			</div>
			<form id="words-list" method="get" style="width: 75%; float: left; margin-top: 10px;">
				<p class="search-box" style="position: relative; margin-top: 0.5em;">
					<label class="screen-reader-text" for="search_id-search-input">search:</label>
					<input type="search" id="search_id-search-input-top" name="s-top" value="" placeholder="Search for Page Names">
					<input type="submit" id="search-submit-top" class="button" value="search">
				</p>
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php 
	
	
	
	 ?>
				<?php $list_table->display() ?>
				<?php 
	
	
	
	 ?>
				<p class="search-box" style="margin-top: 0.7em;">
					<label class="screen-reader-text" for="search_id-search-input">search:</label>
					<input type="search" id="search_id-search-input" name="s" value="" placeholder="Search for Page Names">
					<input type="submit" id="search-submit" class="button" value="search">
				</p>
			</form>
			
			<div style="padding: 15px; background: white; clear: both; width: 72%; font-family: helvetica;">
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Errors found on <span style='color: rgb(0, 150, 255); font-weight: bold;'>".$options_list[7]->option_value."</span>: " . $options_list[6]->option_value . "</h3>"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Pages scanned: " . $options_list[4]->option_value . "/" . $total_pages . "</h3>"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Posts scanned: " . $options_list[5]->option_value . "/" . $total_posts . "</h3>"; ?>
			</div>
		</div>
		</div>
		<!-- Quick Edit Clone Field -->
		<table style="display: none;">
			<tbody>
				<tr id="wpsc-editor-row" class="wpsc-editor">
					<td colspan="4">
						<div class="wpsc-edit-content">
							<h4 style="display: inline-block;">Edit %Word%</h4>
							<input type="text" size="60" name="word_update[]" style="margin-left: 3em;" value class="wpsc-edit-field edit-field">
							<input type="hidden" name="edit_page_name[]" value>
							<input type="hidden" name="edit_page_type[]" value>
							<input type="hidden" name="edit_old_word[]" value>
							<input type="hidden" name="edit_old_word_id[]" value>
						</div>
						<div class="wpsc-buttons">
							<input type="button" class="button-secondary cancel alignleft wpsc-cancel-button" value="Cancel">
							<!--<input type="checkbox" name="global-edit" value="global-edit"> Apply changes to entire website-->
							<div style="clear: both;"></div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Suggested Spellings Clone Field -->
		<table style="display: none;">
			<tbody>
				<tr id="wpsc-suggestion-row" class="wpsc-editor">
					<td colspan="4">
						<div class="wpsc-suggestion-content">
							<label><span>Suggested Spellings</span>
							<select class="wpsc-suggested-spelling-list" name="suggested_word[]">
								<option id="wpsc-suggested-spelling-1" value></option>
								<option id="wpsc-suggested-spelling-2" value></option>
								<option id="wpsc-suggested-spelling-3" value></option>
								<option id="wpsc-suggested-spelling-4" value></option>
							</select>
							<input type="hidden" name="suggest_page_name[]" value>
							<input type="hidden" name="suggest_page_type[]" value>
							<input type="hidden" name="suggest_old_word[]" value>
							<input type="hidden" name="suggest_old_word_id[]" value>
						</div>
						<div class="wpsc-buttons">
							<input type="button" class="button-secondary cancel alignleft wpsc-cancel-suggest-button" value="Cancel">
							<!--<input type="checkbox" name="global-suggest" value="global-suggest"> Apply changes to entire website-->
							<div style="clear: both;"></div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(".wpgc-desc").click(function() {
					console.log(jQuery(this).find(".wpgc-desc-content").html());
					jQuery(this).find(".wpgc-desc-content").toggleClass( "wpgc-desc-hover" );
				});
			});
		</script>
	<?php 
	}
	
	
	
	
?>