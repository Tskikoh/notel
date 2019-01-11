<?php
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

	function check_page_title_empty($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		global $wpdb;
		global $base_page_max;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$page_table = $wpdb->prefix . 'posts';
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();

		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
		
		wpsc_set_global_vars();
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
		else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_title, ID FROM $page_table WHERE post_type='page'$post_status LIMIT $base_page_max"));
		
		$ignore_pages = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";'); $sql_count++;

		for ($x=0; $x<$page_list->getSize(); $x++) {
			$total_count++;
			$word_list = $page_list[$x]->post_title;
			
			if ($word_list == '') {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => "Empty Page Title", 'page_type' => 'Page Title', 'page_id' => $page_list[$x]->ID));
			}
		}
		
		wpsc_sql_insert($error_list, "Empty Field", $table_name);
		$wpdb->update($options_table, array('option_value' => $total_count), array('option_name' => 'empty_page_count'));  $sql_count++;
		
		$counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='empty_checked';"); $sql_count++;
		$total_count = $total_count + intval($counter[0]->option_value);
		$wpdb->update($options_table, array('option_value' => $total_count), array('option_name' => 'empty_checked')); $sql_count++;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_scan_in_progress'));  $sql_count++;
			$end_time = time();
			$total_time = time_elapsed($end_time - $start_time);
			$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'empty_start_time')); $sql_count++; 
		}
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_page_title_sip')); $sql_count++;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Page Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
	}
	add_action('admincheckpagetitlesemptybase', 'check_page_title_empty');
	
	function check_post_title_empty($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		global $wpdb;
		global $wpsc_settings;
		global $base_page_max;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		set_time_limit(6000);
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();

		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_scan_in_progress'));  $sql_count++;
			$start_time = time();
		}

		$ignore_pages = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";'); $sql_count++;

		$post_types = get_post_types();
		$post_type_list = array();
		foreach ($post_types as $type) {
			if ($type != 'revision' && $type != 'page' && $type != 'nav_menu_item' && $type != 'optionsframework' && $type != 'slider' && $type != 'attachment' && $type != 'oembed_cache')
				array_push($post_type_list, $type);
		}
		
		wpsc_set_global_vars();
		
		if ($wpsc_settings[137]->option_value == 'true') { $post_status = array('publish', 'draft'); }
		else { $post_status = array('publish'); }

		$posts_list = SplFixedArray::fromArray(get_posts(array('posts_per_page' => $base_page_max, 'post_type' => $post_type_list, 'post_status' => $post_status))); $sql_count++;

		for ($x = 0;$x < $posts_list->getSize();$x++) {
			$ignore_flag = 'false';
			foreach($ignore_pages as $ignore_check) {
				if (strtoupper(trim($page->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$total_count++;
			$word_list = $posts_list[$x]->post_title;
			if ($word_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;

				array_push($error_list, array('word' => "Empty Field", 'page_name' => "Empty Post Title", 'page_type' => 'Post Title', 'page_id' => $posts_list[$x]->ID));
			}
		}
		
		wpsc_sql_insert($error_list, "Empty Field", $table_name);
		$wpdb->update($options_table, array('option_value' => $total_count), array('option_name' => 'empty_post_count'));  $sql_count++;
		
		$counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='empty_checked';"); $sql_count++;
		$total_count = $total_count + intval($counter[0]->option_value);
		$wpdb->update($options_table, array('option_value' => $total_count), array('option_name' => 'empty_checked')); $sql_count++;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_scan_in_progress'));  $sql_count++;
			$end_time = time();
			$total_time = time_elapsed($end_time - $start_time);
			$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'empty_start_time'));  $sql_count++;
		}
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_post_title_sip')); $sql_count++;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Post Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
	}
	add_action('admincheckposttitlesemptybase', 'check_post_title_empty');
	
	function wpsc_check_author_empty($is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$user_table = $wpdb->prefix . 'usermeta';
		$username_table = $wpdb->prefix . 'users';
		
		set_time_limit(600);
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		
		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT a.meta_key, a.meta_value, b.user_login, b.post_author FROM $user_table a LEFT JOIN (SELECT a.post_author, b.user_login FROM $post_table a, $username_table b WHERE a.post_author = b.ID GROUP BY post_author) AS b ON b.post_author = a.user_id WHERE (a.meta_key = 'first_name' OR a.meta_key = 'last_name' OR a.meta_key = 'description' OR a.meta_key = 'twitter' OR a.meta_key = 'facebook' OR a.meta_key = 'googleplus');")); $sql_count++;

		for ($x = 0; $x < $posts_list->getSize(); $x++) {
			$total_count++;

			if ($posts_list[$x]->user_login != '') {
				if ($posts_list[$x]->meta_value == '') {
					$error_count++;
					
					if ($posts_list[$x]->meta_key == "first_name") { $post_type = "First Name";
					} elseif ($posts_list[$x]->meta_key == "last_name") { $post_type = "Last Name";
					} elseif ($posts_list[$x]->meta_key == "description") { $post_type = "Biography";
					} else { $post_type = $posts_list[$x]->meta_key; }
					
					array_push($error_list, array('word' => "Empty Field", 'page_name' => $posts_list[$x]->user_login, 'page_type' => 'Author ' . $post_type, 'page_id' => $posts_list[$x]->post_author));
				}
			}
		}
		
		wpsc_sql_insert($error_list, "Empty Field", $table_name);
		
		if ($post_count > $total_posts) $post_count = $total_posts;

		$counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='empty_checked';"); $sql_count++;
		$total_count = $total_count + intval($counter[0]->option_value);
		$wpdb->update($options_table, array('option_value' => $total_count), array('option_name' => 'empty_checked')); $sql_count++;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Author", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
	}
	
	function check_author_empty($rng_seed) {
		if (!$is_running) sleep(1);
		global $wpdb;
		global $wpsc_settings;
		global $ent_included;
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$start_time = time(); 

		wpsc_check_author_empty(true);
		if ($ent_included) {
			check_author_seotitle_empty_ent(true);
			check_author_seodesc_empty_ent(true);
		}
		
		if (!$is_running) sleep(1);
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_author_sip'));
		$end_time = time();
		$total_time = time_elapsed($end_time - $start_time + 6);
		$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'empty_start_time')); 
	}
	add_action ('admincheckauthorsempty', 'check_author_empty');
	
	function clear_results_empty() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'pro_word_count')); //$ Clear out the pro errors count
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'empty_checked')); //$ Clear out the total empty field count
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'page_count')); //$ Clear out the page count
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'post_count')); //$ Clear out the post count
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'media_count')); //$Clear out the media count

		$wpdb->delete($table_name, array('ignore_word' => false));
	}
	
	function wpsc_clear_empty_scan() {
		global $wpdb;
		global $wpsc_settings;
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'entire_empty_scan'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_author_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_page_title_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_post_title_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_menu_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_page_seo_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_post_seo_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_media_seo_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_media_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_ecommerce_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_tag_desc_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'empty_cat_desc_sip'));
	}
	
	function set_empty_scan_in_progress($rng_seed = 0) {
		global $wpdb;
		global $pro_included;
		global $ent_included;
		global $wpsc_settings;
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'entire_empty_scan'));
		
		$settings = $wpsc_settings;

		
		if ($settings[47]->option_value =='true') {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_author_sip'));
		if ($settings[49]->option_value =='true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_page_title_sip'));
		if ($settings[50]->option_value =='true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_post_title_sip'));
			
		if ($ent_included || $pro_included) {
		if ($settings[48]->option_value =='true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_menu_sip'));
		if ($settings[53]->option_value =='true') {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_page_seo_sip'));
		}
		if ($settings[54]->option_value =='true') {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_post_seo_sip'));
		}
		if ($settings[55]->option_value =='true') {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_media_seo_sip'));
		}
		if ($settings[56]->option_value =='true') {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_media_sip'));
		}
		if ($settings[57]->option_value =='true') {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_ecommerce_sip'));
		}
		if ($settings[51]->option_value =='true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_tag_desc_sip'));
		if ($settings[52]->option_value =='true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_cat_desc_sip'));
		}
		}
	}
	
	function wpsc_clear_events_empty() {
		$time = wp_next_scheduled('admincheckmenusempty_ent');
		wp_unschedule_event($time, 'admincheckmenusempty_ent');
		
		$time = wp_next_scheduled('admincheckpagetitlesempty_ent');
		wp_unschedule_event($time, 'admincheckpagetitlesempty_ent');
		
		$time = wp_next_scheduled('admincheckposttitlesempty_ent');
		wp_unschedule_event($time, 'admincheckposttitlesempty_ent');
		
		$time = wp_next_scheduled('admincheckpostseoempty_ent');
		wp_unschedule_event($time, 'admincheckpostseoempty_ent');
		
		$time = wp_next_scheduled('admincheckmediaseoempty_ent');
		wp_unschedule_event($time, 'admincheckmediaseoempty_ent');
		
		$time = wp_next_scheduled('admincheckmediaempty');
		wp_unschedule_event($time, 'admincheckmediaempty');
		
		$time = wp_next_scheduled('admincheckecommerceempty_ent');
		wp_unschedule_event($time, 'admincheckecommerceempty_ent');
		
		$time = wp_next_scheduled('admincheckposttagsdescempty_ent');
		wp_unschedule_event($time, 'admincheckposttagsdescempty_ent');
		
		$time = wp_next_scheduled('admincheckcategoriesdescempty_ent');
		wp_unschedule_event($time, 'admincheckcategoriesdescempty_ent');
		
		$time = wp_next_scheduled('admincheckauthorsempty');
		wp_unschedule_event($time, 'admincheckauthorsempty');
		
		$time = wp_next_scheduled('admincheckmenusempty');
		wp_unschedule_event($time, 'admincheckmenusempty');
		
		$time = wp_next_scheduled('admincheckpagetitlesempty');
		wp_unschedule_event($time, 'admincheckpagetitlesempty');
		
		$time = wp_next_scheduled('admincheckposttitlesempty');
		wp_unschedule_event($time, 'admincheckposttitlesempty');
		
		$time = wp_next_scheduled('admincheckpostseoempty');
		wp_unschedule_event($time, 'admincheckpostseoempty');
		
		$time = wp_next_scheduled('admincheckmediaseoempty');
		wp_unschedule_event($time, 'admincheckmediaseoempty');
		
		$time = wp_next_scheduled('admincheckmediaempty_pro');
		wp_unschedule_event($time, 'admincheckmediaempty_pro');
		
		$time = wp_next_scheduled('admincheckecommerceempty');
		wp_unschedule_event($time, 'admincheckecommerceempty');
		
		$time = wp_next_scheduled('admincheckposttagsdescempty');
		wp_unschedule_event($time, 'admincheckposttagsdescempty');
		
		$time = wp_next_scheduled('admincheckcategoriesdescempty');
		wp_unschedule_event($time, 'admincheckcategoriesdescempty');
		
		$time = wp_next_scheduled('admincheckpagetitlesemptybase');
		wp_unschedule_event($time, 'admincheckpagetitlesemptybase');
		
		$time = wp_next_scheduled('admincheckposttitlesemptybase');
		wp_unschedule_event($time, 'admincheckposttitlesemptybase');
	}
	
	
	function scan_site_empty($rng_seed = 0) {
		$start = round(microtime(true),5);
		$sql_count = 0;
		global $wpdb;
		global $pro_included;
		global $ent_included;
		
		//wpsc_clear_events_empty(); //Clear out the event scheduler of any previous empty field events
		
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		set_time_limit(600); //$ Set PHP timeout limit
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_scan_in_progress')); $sql_count++;
		$start_time = time(); 
		$wpdb->update($options_table, array('option_value' => $start_time), array('option_name' => 'scan_start_time'));  $sql_count++;

		$settings = $wpdb->get_results('SELECT option_value FROM ' . $options_table); //4 = Pages, 5 = Posts, 6 = Theme, 7 = Menus
		
		if ($ent_included) {
		if ($settings[48]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckmenusempty_ent', array ($rng_seed, true ));
		if ($settings[49]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckpagetitlesempty_ent', array ($rng_seed, true ));
		if ($settings[50]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckposttitlesempty_ent', array ($rng_seed, true ));
		if ($settings[53]->option_value =='true') {
			wp_schedule_single_event(time(), 'admincheckpageseoempty_ent', array ($rng_seed, true ));
		}
		if ($settings[54]->option_value =='true') {
			wp_schedule_single_event(time(), 'admincheckpostseoempty_ent', array ($rng_seed, true ));
		}
		if ($settings[55]->option_value =='true') {
			wp_schedule_single_event(time(), 'admincheckmediaseoempty_ent', array ($rng_seed, true ));
		}
		if ($settings[56]->option_value =='true') {
			wp_schedule_single_event(time(), 'admincheckmediaempty_ent', array ($rng_seed, true ));
		}
		if ($settings[57]->option_value =='true') {
			wp_schedule_single_event(time(), 'admincheckecommerceempty_ent', array ($rng_seed, true ));
		}
		if ($settings[51]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckposttagsdescempty_ent', array ($rng_seed, true ));
		if ($settings[52]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckcategoriesdescempty_ent', array ($rng_seed, true ));
		if ($settings[47]->option_value =='true') {
			wp_schedule_single_event(time(), 'admincheckauthorsempty', array ($rng_seed , false));
		}
		} else {
			if ($settings[47]->option_value =='true') {
				wp_schedule_single_event(time(), 'admincheckauthorsempty', array ($rng_seed, true ));
			}
			if ($settings[49]->option_value =='true')
				wp_schedule_single_event(time(), 'admincheckpagetitlesemptybase', array ($rng_seed, true ));
			if ($settings[50]->option_value =='true')
				wp_schedule_single_event(time(), 'admincheckposttitlesemptybase', array ($rng_seed, true ));
		}
		
		if (!$ent_included) check_empty_wpsc();
		
		$end = round(microtime(true),5);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, "Initialization Time: " . round($end - $start,5) . ".     SQL: " . $sql_count . "     Memory: " . round(memory_get_usage() / 1000,5) . " \r\n" );
		////////fclose($debug_file);
	}
	add_action ('adminscansiteempty', 'scan_site_empty');
	
	function check_empty_wpsc() {
		global $wpdb;
		global $pro_included;
		global $ent_included;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		set_time_limit(600); //$ Set PHP timeout limit
		$pro_errors = 0;
		$last_count = 0;
		
		$pro_errors += check_menus_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_yoast_page_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_seo_titles_page_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_yoast_post_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_seo_titles_post_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_yoast_media_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_seo_titles_media_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_media_descriptions_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_media_captions_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_media_alt_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_woocommerce_name_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_woocommerce_excerpt_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_wpecommerce_name_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_wpecommerce_excerpt_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_post_tag_descriptions_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_post_categories_description_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_author_seotitle_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		$pro_errors += check_author_seodesc_empty_free(true);
		//////$loc = dirname(__FILE__)."/../../../../debug.log";
		////////$debug_file = fopen($loc, 'a');
		////////$debug_var = fwrite( $debug_file, " Other Errors: : " . ($pro_errors - $last_count) . "\r\n" );
		////////fclose($debug_file);
		$last_count = $pro_errors;

		
		$wpdb->update($options_table, array('option_value' => $pro_errors), array('option_name' => 'pro_empty_count'));
	}
	add_action ('admincheckemptywpsc', 'check_empty_wpsc');
	
	//$Functions to check for errors found on other parts of your site
	
	function check_menus_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'posts';
		$empty_table = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_scan_in_progress')); 
			$start_time = time();
		}

		

		$menus = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE post_type ="nav_menu_item" LIMIT 10000;');
		
		foreach($menus as $menu) {
			$total_count++;
			$word_list = $menu->post_title;
			if ($word_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $menu->post_title, 'page_type' => 'Menu Item', 'page_id' => $menu->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Menu", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_post_tag_descriptions_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		
		global $wpdb;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'empty_scan_in_progress')); 
			$start_time = time();
		}

		$tags_list = get_tags();
		
		foreach ($tags_list as $tag) {	
			$total_count++;
			$word = $tag->description;
			if ($word == '') {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $tag->name, 'page_type' => 'Tag Description', 'page_id' => $tag->term_id));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Tag Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_post_categories_description_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		$timer_start = round(microtime(true),5);
		//
		global $wpdb;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$sql_count = 0;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();
		
		$cats_list = get_categories(); $sql_count++;

		foreach ($cats_list as $cat) {
			$words = $cat->description;
			if ($words == '') {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $cat->name, 'page_type' => 'Category Description', 'page_id' => $cat->term_id));
			}
		}
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Category Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_media_descriptions_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$sql_count = 1;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'attachment'));

		foreach ($posts_list as $post) {
			$total_count++;
			$words_list = $post->post_content;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'Media Description', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Media Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);	
	}

	function check_media_captions_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'attachment'));
		
		foreach ($posts_list as $post) {
			$total_count++;
			$words_list = $post->post_excerpt;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'Media Caption', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Media Caption", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);	
	}

	function check_media_alt_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		
		global $wpdb;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'attachment'));

		foreach ($posts_list as $post) {
			$total_count++;
			$word_list = get_post_meta ($post->ID, '_wp_attachment_image_alt', true );
			$word_list = $word_list;
			if ($word_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'Media Alternate Text', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Media Alt", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);	
	}

	function check_woocommerce_name_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');

		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'product', 'post_status' => array('publish', 'draft')));

		foreach ($posts_list as $post) {
			$total_count++;
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$words_list = $post->post_title;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'WooCommerce Product Name', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty WooCommerce Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_woocommerce_excerpt_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');

		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'product', 'post_status' => array('publish', 'draft')));

		foreach ($posts_list as $post) {
			$total_count++;
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$words_list = $post->post_excerpt;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'WooCommerce Product Excerpt', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty WooCommerce Excerpt", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_wpecommerce_name_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'wpsc-product', 'post_status' => array('publish', 'draft')));

		foreach ($posts_list as $post) {
			$total_count++;
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$words_list = $post->post_title;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'WP eCommerce Product Name', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty WPeCommerce Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_wpecommerce_excerpt_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$sql_count = 1;;
		set_time_limit(6000); 
		$error_count = 0;
		$total_count = 0;
		$error_list = array();


		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');

		$posts_list = get_posts(array('posts_per_page' => 10000, 'post_type' => 'wpsc-product', 'post_status' => array('publish', 'draft')));

		foreach ($posts_list as $post) {
			$total_count++;
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$words_list = $post->post_excerpt;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1) {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $post->post_title, 'page_type' => 'WP eCommerce Product Excerpt', 'page_id' => $post->ID));
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty WPeCommerce Excerpt", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_author_seotitle_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$user_table = $wpdb->prefix . 'usermeta';
		$username_table = $wpdb->prefix . 'users';
		set_time_limit(600);
		$error_count = 0;
		$total_count = 0;
		$error_list = array();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');

		$posts_list = $wpdb->get_results("SELECT * FROM $post_table GROUP By post_author LIMIT 10000");

		foreach ($posts_list as $post) {
			$total_count++;
			$author = $wpdb->get_results("SELECT * FROM $user_table WHERE meta_key='wpseo_title' AND user_id='$post->post_author'");
			$author_name = $wpdb->get_results("SELECT * FROM $username_table WHERE id='$post->post_author'");

			if (!is_object($author_name) || !is_object($author)) continue;
			$words_list = $author[0]->meta_value;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1 && $author_name->user_login != '') {
				$error_count++;
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $author_name->user_login, 'page_type' => 'Author SEO Title', 'page_id' => $post->post_author));	
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Author SEO Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_author_seodesc_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$user_table = $wpdb->prefix . 'usermeta';
		$username_table = $wpdb->prefix . 'users';
		set_time_limit(600);
		$total_count = 0;
		$error_count = 0;
		$error_list = array();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');

		$posts_list = $wpdb->get_results("SELECT * FROM $post_table GROUP By post_author LIMIT 10000");

		foreach ($posts_list as $post) {
			if ($ignore_flag == 'true') { continue; }
			$total_count++;
			$author = $wpdb->get_results("SELECT * FROM $user_table WHERE meta_key='wpseo_metadesc' AND user_id='$post->post_author'");
			$author_name = $wpdb->get_results("SELECT * FROM $username_table WHERE id='$post->post_author'");
			
			if (!is_object($author_name) || !is_object($author)) continue;
			$words_list = $author[0]->meta_value;
			if ($words_list == '' && sizeof((array)$ignore_word) < 1 && $author_name->user_login != '') {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $author_name->user_login, 'page_type' => 'Author SEO Description', 'page_id' => $post->post_author));	
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Author SEO Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_yoast_page_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		$haystack = array();
		set_time_limit(6000); 
		
		$words_table = $wpdb->prefix . 'spellcheck_empty';
		$posts_table = $wpdb->prefix . 'posts';
		
		$seo_check = $wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE (meta_key="_yoast_wpseo_metadesc" OR meta_key="_aioseop_description" OR meta_key="_su_description") GROUP BY post_id'); $sql_count++;
		
		foreach($seo_check as $value) {
			if ($value->meta_value != '') $haystack[$value->post_id] = "true";
		}
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $posts_table WHERE post_type='page'$post_status"));
		
		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($haystack[$page_list[$x]->ID] != "true") {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $page_list[$x]->post_title, 'page_type' => 'SEO Page Description', 'page_id' => $page_list[$x]->ID)); 
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Page SEO Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_seo_titles_page_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		$haystack = array();
		set_time_limit(6000); 
		
		$words_table = $wpdb->prefix . 'spellcheck_empty';
		$posts_table = $wpdb->prefix . 'posts';
		
		$seo_check = $wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE (meta_key="_yoast_wpseo_title" OR meta_key="_aioseop_title" OR meta_key="_su_title") GROUP BY post_id'); $sql_count++;
		
		foreach($seo_check as $value) {
			if ($value->meta_value != '') $haystack[$value->post_id] = "true";
		}
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $posts_table WHERE post_type='page'$post_status"));
		
		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($haystack[$page_list[$x]->ID] != "true") {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $page_list[$x]->post_title, 'page_type' => 'SEO Page Title', 'page_id' => $page_list[$x]->ID)); 
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Page SEO Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_yoast_post_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		$haystack = array();
		set_time_limit(6000); 
		
		$words_table = $wpdb->prefix . 'spellcheck_empty';
		$posts_table = $wpdb->prefix . 'posts';
		
		$seo_check = $wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE (meta_key="_yoast_wpseo_metadesc" OR meta_key="_aioseop_description" OR meta_key="_su_description") GROUP BY post_id'); $sql_count++;
		
		foreach($seo_check as $value) {
			if ($value->meta_value != '') $haystack[$value->post_id] = "true";
		}
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $posts_table WHERE post_type='post'$post_status"));
		
		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($haystack[$page_list[$x]->ID] != "true") {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $page_list[$x]->post_title, 'page_type' => 'SEO Post Description', 'page_id' => $page_list[$x]->ID)); 
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Post SEO Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_seo_titles_post_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		$haystack = array();
		set_time_limit(6000); 
		
		$words_table = $wpdb->prefix . 'spellcheck_empty';
		$posts_table = $wpdb->prefix . 'posts';
		
		$seo_check = $wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE (meta_key="_yoast_wpseo_title" OR meta_key="_aioseop_title" OR meta_key="_su_title") GROUP BY post_id'); $sql_count++;
		
		foreach($seo_check as $value) {
			if ($value->meta_value != '') $haystack[$value->post_id] = "true";
		}
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $posts_table WHERE post_type='post'$post_status"));
		
		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($haystack[$page_list[$x]->ID] != "true") {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $page_list[$x]->post_title, 'page_type' => 'SEO Post Title', 'page_id' => $page_list[$x]->ID)); 
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Post SEO Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_yoast_media_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		$haystack = array();
		set_time_limit(6000); 
		
		$words_table = $wpdb->prefix . 'spellcheck_empty';
		$posts_table = $wpdb->prefix . 'posts';
		
		$seo_check = $wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE (meta_key="_yoast_wpseo_metadesc" OR meta_key="_aioseop_description" OR meta_key="_su_description") GROUP BY post_id'); $sql_count++;
		
		foreach($seo_check as $value) {
			if ($value->meta_value != '') $haystack[$value->post_id] = "true";
		}
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $posts_table WHERE post_type='attachment'"));
		
		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($haystack[$page_list[$x]->ID] != "true") {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $page_list[$x]->post_title, 'page_type' => 'SEO Media Description', 'page_id' => $page_list[$x]->ID)); 
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Media SEO Desc", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
	
	function check_seo_titles_media_empty_free($rng_seed, $is_running = false, $log_debug = true) {	
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M');
		
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$error_count = 0;
		$total_count = 0;
		$sql_count = 0;
		$error_list = array();
		$haystack = array();
		set_time_limit(6000); 
		
		$words_table = $wpdb->prefix . 'spellcheck_empty';
		$posts_table = $wpdb->prefix . 'posts';
		
		$seo_check = $wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE (meta_key="_yoast_wpseo_title" OR meta_key="_aioseop_title" OR meta_key="_su_title") GROUP BY post_id'); $sql_count++;
		
		foreach($seo_check as $value) {
			if ($value->meta_value != '') $haystack[$value->post_id] = "true";
		}
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $posts_table WHERE post_type='attachment'"));
		
		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($haystack[$page_list[$x]->ID] != "true") {
				array_push($error_list, array('word' => "Empty Field", 'page_name' => $page_list[$x]->post_title, 'page_type' => 'SEO Media Description', 'page_id' => $page_list[$x]->ID)); 
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Empty Media SEO Title", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return sizeof((array)$error_list);
	}
?>