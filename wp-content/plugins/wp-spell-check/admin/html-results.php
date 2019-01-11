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
class wphc_table extends WP_List_Table {

	function __construct() {
		global $status, $page;
		
		
		parent::__construct( array(
			'singular' => 'word',
			'plural' => 'words',
			'ajax' => true
		) );
	}
	
	function column_default($item, $column_name) {
		return print_r($item,true);
	}
	
	
	function column_word($item) {

		$actions = array (
			//'Ignore'      			=> sprintf('<input type="checkbox" class="wpsc-ignore-checkbox" name="ignore-word[]" value="' . $item['id'] . '" />Ignore'),
			'Edit'					=> sprintf('<a href="post.php?post=' . $item['page_id'] . '&action=edit" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">Edit</a>')
		);
		
		
		return sprintf('%1$s%3$s',
            stripslashes(stripslashes($item['word'])),
            $item['ID'],
            $this->row_actions($actions)
        );
	}
	
	
	function column_page_name($item) {
		global $wpdb;
		$link = urldecode ( get_permalink( $item['page_id'] ) );

		$actions = array (
			'View'      			=> sprintf('<a href="' . $link . '" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>'),
		);

		return sprintf('%1$s <span style="color:silver"></span>%3$s',
            $item['page_name'],
            $item['ID'],
            $this->row_actions($actions)
        );
	}

	
	function column_page_type($item) {
		
		$actions = array ();
		
		
		return sprintf('%1$s <span style="color:silver"></span>%3$s',
            $item['page_type'],
            $item['ID'],
            $this->row_actions($actions)
        );
	}

	
	function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'word' => 'Misspelled Words',
			'page_name' => 'Page',
			'page_type' => 'Page Type'
		);
		return $columns;
	}
	
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'word' => array('word',false),
			'page_name' => array('page_name',false),
			'page_type' => array('page_type',false)
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
		global $ent_included;
		
		$per_page = 20;
		
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		
		$table_name = $wpdb->prefix . 'spellcheck_html';
		$dictionary_table = $wpdb->prefix . 'spellcheck_dictionary';
		if ($_GET['submit'] == 'Find Broken Shortcodes' && $ent_included) {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND word LIKE "[%]"', OBJECT);
		} elseif ($_GET['s'] != '') {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND word LIKE "%' . $_GET['s'] . '%"', OBJECT); 
		} elseif ($_GET['s-top'] != '') {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND word LIKE "%' . $_GET['s-top'] . '%"', OBJECT); 
		} else {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false', OBJECT);
		}
		$data = array();
		foreach($results as $word) {
			if ($word->word != '') {
				array_push($data, array('id' => $word->id, 'word' => $word->word, 'page_name' => $word->page_name, 'page_type' => $word->page_type, 'page_url' => $word->page_url, 'page_id' => $word->page_id));
			}
		}
		
		function usort_reorder($a, $b) {
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'word'; 
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; 
			
			$result = strcmp($a[$orderby], $b[$orderby]); 
			return ($order==='asc') ? $result : -$result;
		}
		usort($data, 'usort_reorder');
		
		
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

function wphc_admin_render() {
	$start = round(microtime(true),5);
	ini_set('memory_limit','8192M'); 
	set_time_limit(600); 
	global $wpdb;
	global $ent_included;
	$table_name = $wpdb->prefix . "spellcheck_grammar";
	$options_table = $wpdb->prefix . "spellcheck_options";
	$error_table = $wpdb->prefix . "spellcheck_html";
	$post_table = $wpdb->prefix . "posts";
	$time_estimate = 0;
	$pro_scan_msg = "";
	
	if (!isset($_GET['action'])) $_GET['action'] = '';
	if (!isset($_GET['submit'])) $_GET['submit'] = '';
	if (!isset($_GET['wpsc-script'])) $_GET['wpsc-script'] = '';
	
	wpsc_set_global_vars();
	global $wpsc_settings;
	
	$message = '';
	
	$options_list = $wpsc_settings;
	$total_pages = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type = 'page'");
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type = 'post'");
	
	$post_scan_count = $options_list[144]->option_value;
	if ($post_scan_count > $total_posts) $post_scan_count = $total_posts;
	
	$scan_message = "No scan currently running";
	
	$scan_progress = $wpdb->get_results("SELECT * FROM $options_table WHERE option_name='html_scan_running'");
	
	if ($scan_progress[0]->option_value == "true") $scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan is currently in progress for <span class="sc-message" style="color: rgb(0, 150, 255); font-weight: bold;">Entire site</span>. <a href="/wp-admin/admin.php?page=wp-spellcheck-html.php">Click here</a> to see scan results.';
	
	$check_scan = wphc_check_scan_progress();
	
	$post_status = array("publish", "draft");
	
	$post_count = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='post' AND (post_status='draft' OR post_status='publish')");
	$page_count = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='page' AND (post_status='draft' OR post_status='publish')");
	$error_count = $wpdb->get_var("SELECT COUNT(*) FROM $error_table WHERE ignore_word = 0");
	
	$max_pages = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name = 'pro_max_pages'");
	$max_pages = intval($max_pages[0]->option_value);
	
	$estimated_time = intval((($total_pages + $total_posts) / 3.5) + 3);
	
	$estimated_time = time_elapsed($estimated_time);

	if (!$ent_included) $max_pages = 500;
	
	if ($check_scan && $_GET['wpsc-script'] != 'noscript') {
		wp_enqueue_script( 'wphc-results-ajax', plugin_dir_url( __FILE__ ) . '/wphc-ajax.js', array('jquery') );
		wp_localize_script( 'wphc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
	
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Entire Site') {
		wphc_clear_results(); //Clear out results table in preparation for a new scan
		$rng_seed = rand(0,999999999);
		$time_estimate = intval($total_posts / 8);
		$time_estimate= time_elapsed($time_estimate);
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'html_scan_running'));
		$wpdb->update($options_table, array("option_value" => time()), array("option_name" => "html_scan_start_time"));
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Entire Site</span>. Estimated time for completion is ' . $time_estimate. ' seconds. The page will automatically refresh when the scan has finished.';
		
		wp_enqueue_script( 'wphc-results-ajax', plugin_dir_url( __FILE__ ) . '/wphc-ajax.js', array('jquery') );
		wp_localize_script( 'wphc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_schedule_single_event(time(), 'admincheckcode', array ($rng_seed, true));
	} elseif ($_GET['action'] == 'check' && $_GET['submit'] == 'Broken HTML') {
		wphc_clear_results(); //Clear out results table in preparation for a new scan
		$rng_seed = rand(0,999999999);
		$time_estimate = intval($total_posts / 8);
		$time_estimate= time_elapsed($time_estimate);
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'html_scan_running'));
		$wpdb->update($options_table, array("option_value" => time()), array("option_name" => "html_scan_start_time"));
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Broken HTML</span>. Estimated time for completion is ' . $time_estimate. ' seconds. The page will automatically refresh when the scan has finished.';
		
		wp_enqueue_script( 'wphc-results-ajax', plugin_dir_url( __FILE__ ) . '/wphc-ajax.js', array('jquery') );
		wp_localize_script( 'wphc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_schedule_single_event(time(), 'admincheckhtml', array ($rng_seed, true));
	} elseif ($_GET['action'] == 'check' && $_GET['submit'] == 'Broken Shortcodes') {
		wphc_clear_results(); //Clear out results table in preparation for a new scan
		$rng_seed = rand(0,999999999);
		$time_estimate = intval($total_posts / 8);
		$time_estimate= time_elapsed($time_estimate);
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'html_scan_running'));
		$wpdb->update($options_table, array("option_value" => time()), array("option_name" => "html_scan_start_time"));
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Broken Shortcodes</span>. Estimated time for completion is ' . $time_estimate. ' seconds. The page will automatically refresh when the scan has finished.';
		
		wp_enqueue_script( 'wphc-results-ajax', plugin_dir_url( __FILE__ ) . '/wphc-ajax.js', array('jquery') );
		wp_localize_script( 'wphc-results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_schedule_single_event(time(), 'admincheckshortcode', array ($rng_seed, true));
	} elseif ($_GET['action'] == 'check' && $_GET['submit'] == 'Scan Site') {
		$pro_error_count = check_broken_code_free(0, true);
		$pro_error_msg = "<h3 style='color: red;'>" . $pro_error_count . " Broken code errors were found on your website.</h3>";
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Clear Results') {
		$scan_message = 'All spell check results have been cleared';
		wphc_clear_results();
	}
	if ($_GET['submit'] == "Stop Scans") {
		$scan_message = "All current spell check scans have been stopped.";
		wphc_clear_scan();
	}
		
	$list_table = new wphc_table();
	$list_table->prepare_items();
	?>
		<?php show_feature_window(); ?>
		<?php check_install_notice(); ?>
		
	<style>.search-box input[type=submit] { color: white; background-color: #00A0D2; border-color: #0073AA; } #cb-select-all-1,#cb-select-all-2 { display: none; } td.word { font-size: 15px; } p.submit { display: inline-block; margin-left: 8px; } h3.sc-message { width: 49%; display: inline-block; padding-left: 8px; font-weight: normal; } .wpsc-mouseover-text-page,.wpsc-mouseover-text-post,.wpsc-mouseover-text-refresh { color: black; font-size: 12px; width: 225px; display: inline-block; position: absolute; margin: -13px 0 0 -270px; padding: 3px; border: 1px solid black; border-radius: 10px; opacity: 0; background: white; z-index: -100; } .wpsc-row .row-actions, .wpsc-row .row-actions *{ visibility: visible!important; left: 0!important; } #current-page-selector { width: 12%; } .hidden { display: none; } .wpsc-scan-nav-bar { border-bottom: 1px solid #BBB; margin-botton: 15px; } .wpsc-scan-nav-bar a { text-decoration: none; margin: 5px 5px -1px 5px; padding: 8px; border: 1px solid #BBB; display: inline-block; font-weight: bold; color: black; font-size: 14px; } .wpsc-scan-nav-bar a.selected { border-bottom: 1px solid white; background: white; } #wpsc-empty-fields-tab .button-primary { background: #73019a; border-color: #51006E; text-shadow: 1px 1px #51006d; box-shadow: 0 1px 0 #51006d; } #wpsc-empty-fields-tab .button-primary:hover { background: #9100c3 } #wpsc-empty-fields-tab .button-primary:active { background: #51006d; }.wpsc-scan-buttons input#submit:active { margin-top: -7px; } #wpgc-scan-results-tab .wp-list-table th { text-align: center; } .wpgc-desc .wpgc-desc-content { display: none; } .wpgc-desc-hover { display: block!important; position: relative; top: -125px; left: -55px; width: 125px; padding: 0px; margin: 0px; z-index: 100; height: 0px!important; } .wpgc-desc { position: absolute; bottom: 0px; margin-right: -4px; width: 125px; }</style>
<div id="wpsc-dialog-confirm" title="Are you sure?" style="display: none;">
  <p>Would you like to Proceed with the changes?</p>
</div>
		<div class="wrap wpsc-table">
			<h2><a href="admin.php?page=wp-spellcheck-grammar.php"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/logo.png'; ?>" alt="WP Spell Check" /></a> <span style="position: relative; top: -15px;">Broken Code Scan Results</span></h2>
			<div class="wpsc-scan-nav-bar">
				<a href="/wp-admin/admin.php?page=wp-spellcheck.php" id="wpsc-scan-results" name="wpsc-scan-results">Spelling Errors</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-grammar.php" id="wpsc-grammar" name="wpsc-grammar">Grammar</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-seo.php" id="wpsc-empty-fields" name="wpsc-empty-fields">SEO Empty Fields</a>
				<a href="#" class="selected" id="wpsc-html" name="wpsc-html">Broken Code</a>
			</div>
			<?php if ($ent_included) { ?>
			<div id="wpgc-scan-results-tab" <?php if ($_GET['wpsc-scan-tab'] == 'empty') echo 'class="hidden"';?>>
			<form action="<?php echo admin_url('admin.php'); ?>" method='GET'>
				<div class="wpsc-scan-buttons" style="background: white; padding-left: 8px; padding-top: 5px;">
				<h3 style="margin-bottom: 0px;">This function shows all the broken shortcodes and HTML code displaying on pages. </h3>
				<h3 style="margin-bottom: 0px;">Make sure you go to your <a href="/wp-admin/admin.php?page=wp-spellcheck-options.php">Options page</a> to set up automatic reports to be notified if broken code is found</h3>
				<h3 style="display: inline-block;">Scan:</h3>
				<p class="submit"><input style="background-color: #ffb01f; border-color: #ffb01f; box-shadow: 0px 1px 0px #ffb01f; text-shadow: 1px 1px 1px #ffb01f; font-weight: bold;" type="submit" name="submit" id="submit" class="button button-primary" value="Entire Site"></p>
				<p class="submit"><input style="background-color: #ffb01f; border-color: #ffb01f; box-shadow: 0px 1px 0px #ffb01f; text-shadow: 1px 1px 1px #ffb01f; font-weight: bold;" type="submit" name="submit" id="submit" class="button button-primary" value="Broken HTML"></p>
				<p class="submit"><input style="background-color: #ffb01f; border-color: #ffb01f; box-shadow: 0px 1px 0px #ffb01f; text-shadow: 1px 1px 1px #ffb01f; font-weight: bold;" type="submit" name="submit" id="submit" class="button button-primary" value="Broken Shortcodes"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Clear Results"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="See Scan Results"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="Stop Scans"></p>
				</div>
				<div style="padding: 5px; background: white; font-size: 12px;">
					<input type="hidden" name="page" value="wp-spellcheck-html.php">
					<input type="hidden" name="action" value="check">
					<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Last scan took " . $options_list[146]->option_value . "</h3>"; ?>
					<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>$scan_message</h3><br />"; ?>
					<?php if ((($post_count + $page_count) > $max_pages) & $ent_included) echo "<h3 class='sc-message' style='color: rgb(225, 0, 0);'>You have more than $max_pages Pages/Posts. <a href='https://www.wpspellcheck.com/features/?utm_source=baseplugin&utm_campaign=&utm_medium=bc_scan&utm_content=7.0.2' target='_blank'>Upgrade</a> to scan all of your website.</h3>" ?>
					<?php if (!$ent_included) echo "<h3 class='sc-message' style='color: rgb(225, 0, 0);'><a href='https://www.wpspellcheck.com/features/?utm_source=baseplugin&utm_campaign=upgradeBroken_code&utm_medium=bc_scan&utm_content=7.0.2' target='_blank'>Upgrade</a> to scan all parts of your website.</h3>"; ?>
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
				<a href="https://www.wpspellcheck.com/tutorials?utm_source=baseplugin&utm_campaign=toturial_rightside&utm_medium=html_check&utm_content=7.0.2" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/wp-spellcheck-tutorials.jpg'; ?>" style="max-width: 99%;" alt="Watch WP Spell Check Tutorials" /></a>
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
			<div style="padding: 15px; background: white;  clear: both; width: 72%; font-family: helvetica;">
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Errors found on <span style='color: rgb(0, 150, 255); font-weight: bold;'>Entire Site</span>: " . $error_count . "</h3>"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Pages scanned: " . $options_list[143]->option_value . "/" . $page_count . "</h3>"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Posts scanned: " . $post_scan_count . "/" . $total_posts . "</h3>"; ?>
			</div>
		</div>
		<?php } else { ?>
			<?php if ($pro_error_msg == "") { ?>
				<form action="<?php echo admin_url('admin.php'); ?>" method='GET'>
				<input type="hidden" name="page" value="wp-spellcheck-html.php">
				<input type="hidden" name="action" value="check">
				<h3>Click the button below to find out how many broken code errors are on your site</h3>
				<p class="submit" style="margin: 0px;"><input type="submit" name="submit" id="submit" class="button button-primary" value="Scan Site"></p>
				</form>
			<?php } else {
				echo $pro_error_msg;
			} ?>
			<h3><a href="https://www.wpspellcheck.com/features/?utm_source=baseplugin&utm_campaign=upgradeBroken_code&utm_medium=bc_scan&utm_content=7.0.2" target="_blank">Upgrade to pro</a> to find broken HTML and Shortcodes on your website.</h3>
			<h3 style="color: red;">Examples</h3>
			<h4>Broken Shortcode</h4>
			<div>[broken_shortcode setting=1]</div>
			<h4>Broken HTML</h4>
			<div>&lt;h1&gt;Broken Header Title&lt;/h1&gt;
		<?php } ?>
		</div>
	<?php 
	}
	
	
	
	
?>