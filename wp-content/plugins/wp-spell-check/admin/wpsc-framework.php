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
	/* WP Spell Check classes */
		
	/* Main WP Spell Check Functions */
	
	
	/*function check_word($word, $dict_list) {
		ini_set('memory_limit','256M'); //Sets the PHP memory limit
		if (strlen($word) <= 2) { return true; }
		if (preg_replace('/[^A-Za-z0-9]/', '', $word) == '') { return true; }
		global $wpdb;
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$words_table = $wpdb->prefix . 'spellcheck_words';
		
		if (is_numeric($word)) { return true; }
		if (preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $word)) { return true; }
		
		$ignore_word = $wpdb->get_results("SELECT word FROM $words_table WHERE word='" . addslashes($word) . "' AND ignore_word!=0");
		if (sizeof((array)$ignore_word) >= 1) return true;

		return false;
	}*/
	
	function wpsc_print_debug($scan, $time, $sql, $memory, $error) {
		//global $wpsc_settings;
		//$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "$scan     Time: $time.     SQL: $sql     Memory: $memory KB.     Errors: $error     Number of Options Loaded: " . sizeof((array)$wpsc_settings) . "\r\n" );
		//fclose($debug_file);
	}
	
	function wpsc_print_debug_end($scan_type, $total_time) {
		//$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "-------------------------$scan_type | " . date( 'd-M-Y H:i:s', current_time( 'timestamp', 0 ) ) . "------------------------------\r\n\r\n\r\n" );
		//fclose($debug_file);
	}
	
	function wpsc_construct_url($type, $id) {
		$blog = get_site_url();
		
		$url = $blog . '/wp-admin/post.php?post=' . $id . '&action=edit';
		
		if ($type == 'Menu Item') {
			$url = $blog . '/wp-admin/nav-menus.php?action=edit&menu='.$id;
		} elseif ($type == 'Contact Form 7') {
			$url = $blog . '"admin.php?page=wpcf7&post='.$id.'&action=edit';
		} elseif ($type == 'Post Title' || $type == 'Page Title' || $type == 'Yoast SEO Description' || $type == 'All in One SEO Description' || $type == 'Ultimate SEO Description' || $type == 'SEO Description' || $type == 'Yoast SEO Title' || $type == 'All in One SEO Title' || $type == 'Ultimate SEO Title' || $type == 'SEO Title' || $type == 'Post Slug' || $type == 'Page Slug') {
			$url = $blog . '/wp-admin/post.php?post=' . $id . '&action=edit';
		} elseif ($type == 'Slider Title' || $type == 'Slider Caption' || $type == 'Smart Slider Title' || $type == 'Smart Slider Caption') {
			$url = $blog . '/wp-admin/post.php?post=' . $id . '&action=edit';
		} elseif ($type == 'Huge IT Slider Title' || $type == 'Huge IT Slider Caption') {
			$url = $blog . '/wp-admin/admin.php?page=sliders_huge_it_slider&task=edit_cat&id=' . $id;
		} elseif ($type == 'Media Title' || $type == 'Media Description' || $type == 'Media Caption' || $type == 'Media Alternate Text') {
			$url = $blog . '/wp-admin/post.php?post=' . $id . '&action=edit';
		} elseif ($type == 'Tag Title' || $type == 'Tag Description' || $type == 'Tag Slug') {
			$url = $blog . '/wp-admin/term.php?taxonomy=post_tag&tag_ID=' . $id . '&post_type=post';
		} elseif ($type == 'Post Category' || $type == 'Category Description' || $type == 'Category Slug') {
			$url = $blog . '/wp-admin/term.php?taxonomy=category&tag_ID=' . $id . '&post_type=post';
		} elseif($type == 'Author Nickname' || $type == 'Author First Name' || $type == 'Author Last Name' || $type == 'Author Biography' || $type == 'Author SEO Title' || $type == 'Author SEO Description' || $type == 'Author twitter' || $type == 'Author facebook') {
			$url = $blog . '/wp-admin/user-edit.php?user_id=' . $id;
		} elseif($type == "Site Name" || $type == "Site Tagline") {
			$url = $blog . '/wp-admin/options-general.php';
		} elseif (($item['page_type'] == "WP eCommerce Product Excerpt" || $item['page_type'] == "WP eCommerce Product Name" || $item['page_type'] == "WooCommerce Product Excerpt" || $item['page_type'] == "WooCommerce Product Name" || $item['page_type'] == "Page Title" || $item['page_type'] == "Post Title" || $item['page_type'] == 'Yoast SEO Page Description' || $item['page_type'] == 'All in One SEO Page Description' || $item['page_type'] == 'Ultimate SEO Page Description' || $item['page_type'] == 'SEO Page Description' || $item['page_type'] == 'Yoast SEO Page Title' || $item['page_type'] == 'All in One SEO Page Title' || $item['page_type'] == 'Ultimate SEO Page Title' || $item['page_type'] == 'SEO Page Title' || $item['page_type'] == 'Yoast SEO Post Description' || $item['page_type'] == 'All in One SEO Post Description' || $item['page_type'] == 'Ultimate SEO Post Description' || $item['page_type'] == 'SEO Post Description' || $item['page_type'] == 'Yoast SEO Post Title' || $item['page_type'] == 'All in One SEO Post Title' || $item['page_type'] == 'Ultimate SEO Post Title' || $item['page_type'] == 'SEO Post Title' || $item['page_type'] == 'Yoast SEO Media Description' || $item['page_type'] == 'All in One SEO Media Description' || $item['page_type'] == 'Ultimate SEO Media Description' || $item['page_type'] == 'SEO Media Description' || $item['page_type'] == 'Yoast SEO Media Title' || $item['page_type'] == 'All in One SEO Media Title' || $item['page_type'] == 'Ultimate SEO Media Title' || $item['page_type'] == 'SEO Media Title') && $item['word'] == "Empty Field") {
				$url = $blog . '/wp-admin/post.php?post=' . $id . '&action=edit';
		}
		
		return $url;
	}
	
	function wpsc_finalize($start_time) {
		global $wpdb;
		$options_table = $wpdb->prefix . "spellcheck_options";
	
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$end_time = time();
		$total_time = time_elapsed($end_time - $start_time + 6);
		$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'last_scan_finished')); $sql_count++;
	}
	
	function wpsc_sql_insert($error_list, $page_type, $table_name = '') {
		global $wpdb;
		if ($table_name == '') $table_name = $wpdb->prefix . 'spellcheck_words';
		if ($page_type == 'Empty Field') $table_name = $wpdb->prefix . 'spellcheck_empty';
	
		if ($page_type == "Multi") {
			$sql = "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES ";
			if($error_list->getSize() > 0) {			
				for ($x = 0; $x < $error_list->getSize(); $x++) {
					if ($error_list[$x][0] != '') $sql .= "('" . addslashes($error_list[$x][0]) . "', '" . addslashes($error_list[$x][1]) . "', '" . $error_list[$x][3] . "', " . $error_list[$x][2] . "), ";
					if ($x % 100 == 0) {
						$sql = trim($sql, ", ");
						if ($sql != "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES") $wpdb->query($sql);
						$sql = "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES ";
					}
				}
				$sql = trim($sql, ", ");
				if ($sql != "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES") $wpdb->query($sql);
			
			}
		} elseif ($page_type == "Empty Field") {
			if(sizeof((array)$error_list) > 0) {
			$sql = '';
			
			foreach ($error_list as $error) {
				if ($sql == '' && $error['word'] != '') $sql = "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES ";
				if ($error['word'] != '') $sql .= "('" . addslashes($error['word']) . "', '" . addslashes($error['page_name']) . "', '" . $error['page_type'] . "', " . $error['page_id'] . "), ";
			}
			$sql = trim($sql, ", ");
			if ($sql != '') $wpdb->query($sql);
		}
		} else {
			$sql = "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES ";
			if($error_list->getSize() > 1) {
				for ($x = 0; $x < ($error_list->getSize() - 1); $x++) {
					
					if ($error_list[$x][0] != '') $sql .= "('" . addslashes($error_list[$x][0]) . "', '" . addslashes($error_list[$x][1]) . "', '" . $page_type . "', " . $error_list[$x][2] . "), ";
					if ($x % 100000 == 0) {
						$sql = trim($sql, ", ");
						if ($sql != "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES") $wpdb->query($sql);
						$sql = "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES ";
					}
				}
				$sql = trim($sql, ", ");
				if ($sql != "INSERT INTO $table_name (word, page_name, page_type, page_id) VALUES") $wpdb->query($sql);
			}
		}
	}
	
	function wpgc_sql_insert($error_list) {
		global $wpdb;
		$results_table = $wpdb->prefix . "spellcheck_grammar";
		
		$wpdb->insert($results_table, $error_list);
	}
	
	function wpsc_clean_text($content, $debug = false) {	
		$content = str_replace("’","'", $content);
		$content = str_replace("`","'", $content);
		$content = str_replace("'''","'", $content);
		$content = str_replace("'s"," ", $content);
		$content = preg_replace("/[0-9]/u", "", $content);
		//Spanish characters: áÁéÉíÍñÑóÓúÚüÜ¿¡«»
		//French Characters: ÀàÂâÆæÈèÉéÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿ
		$content = preg_replace("/[^a-zA-Z'’`ÀàÂâÆæÈèÉéÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿüáÁéÉíÍñÑóÓúÚüÜ¿¡«»€]/iu", " ", $content);
		//$content = preg_replace("/\s[^a-zA-Z'’`ÀàÂâÆæÈèÉéÊêËëÎîÏïÔôŒœÙùÛûÜüŸÿüáÁéÉíÍñÑóÓúÚüÜ¿¡«»€$]+\s/iu", "", $content);
		
		$content = str_replace("§"," ", $content);
		$content = str_replace("¢"," ", $content);
		$content = str_replace("¨"," ", $content);
		$content = str_replace('\\',' ', $content);
		
		return $content;
	}
	
	function wpsc_ignore_caps($wpsc_settings, $word) {
		return (strtoupper($word) != $word || $wpsc_settings[3]->option_value == 'false');
	}
	
	function wpsc_dictionary_init($dict_file) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$wpsc_haystack = null;
		
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");

		foreach ($dict_file as $value) {
			$wpsc_haystack[strtoupper(stripslashes($value))] = 1;
		}
		
		foreach ($dict_list as $value) {
			$wpsc_haystack[strtoupper(stripslashes($value->word))] = 1;
		}
		
		foreach ($ignore_list as $value) {
			$wpsc_haystack[strtoupper(stripslashes($value->word))] = 1;
		}
		
		return $wpsc_haystack;
	}
	
	function wpsc_content_filter($content) {
		$divi_check = wp_get_theme();
		if ($divi_check->name == "Divi" || $divi_check->parent_name == "Divi" || $divi_check->parent_name == "Bridge" || $divi_check->name == "Bridge") {
			global $wp_query;
			$wp_query->is_singular = true;

			$content =  apply_filters( 'the_content', $content );
		
			return $content;
		} else {
			return $content;
		}
	}
	
	function wpsc_divi_check($content) {
		$divi_check = wp_get_theme();
		if ($divi_check->name == "Divi" || $divi_check->parent_name == "Divi" || $divi_check->parent_name == "Bridge" || $divi_check->name == "Bridge") {
			global $wp_query;
			$wp_query->is_singular = true;

			$content =  apply_filters( 'the_content', $content );
			
			$return_content;
		} else {
			return $content;
		}
	}
	
	function wpsc_script_cleanup($content) {
		$content = preg_replace("@<style[^>]*?>.*?</style>@siu",' ',$content);
		$content = preg_replace("@<script[^>]*?>.*?</script>@siu",' ',$content);
		$content = preg_replace("/(\<.*?\>)/",' ',$content);
		$content = preg_replace("/<iframe.+<\/iframe>/", " ", $content);
		
		return $content;
	}
	
	function wpsc_clean_shortcode($content) {
		return preg_replace('/(\[.*?\])/', ' ', $content);
	}
	
	function wpsc_html_cleanup($content) {
		return html_entity_decode(strip_tags($content), ENT_QUOTES, 'utf-8');
	}
	
	function wpsc_email_cleanup($content) {
		return preg_replace('/\S+\@\S+\.\S+/', ' ', $content);
	}
	
	function wpsc_website_cleanup($content) {
		$content = preg_replace('/((http|https|ftp)\S+)/', '', $content);
		$content = preg_replace('/www\.\S+/', '', $content);
		$content = preg_replace('/(\S+\.(COM|NET|ORG|INFO|XYZ|US|TOP|LOAN|BIZ|WANG|WIN|CLUB|ONLINE|VIP|MOBI|BID|SITE|MEN|TECH|PRO|SPACE|SHOP|WEBSITE|ASIA|KIWI|XIN|LINK|PARTY|TRADE|LIFE|STORE|NAME|CLOUD|STREAM|CAT|LIVE|TEL|XXX|ACCOUNTANT|DATE|DOWNLOAD|BLOG|WORK|RACING|REVIEW|TODAY|CLICK|ROCKS|NYC|WORLD|EMAIL|SOLUTIONS|NEWS|TOKYO|DESIGN|GURU|LONDON|LTD|ONE|PUB|REALTY|COMPANY|BERLIN|WEBCAM|HOST|PHOTOGRAPHY|PRESS|SCIENCE|FAITH|JOBS|REALTOR|REN|CITY|OVH|RED|AGENCY|SERVICES|MEDIA|GROUP|CENTER|STUDIO|GLOBAL|NINJA|TECHNOLOGY|TIPS|BAYERN|EXPERT|SALE|AMSTERDAM|DIGITAL|ACADEMY|NETWORK|HAMBURG|gdn|DE|CN|UK|NL|EU|RU|TK|AR|BR|IT|PL|FR|AU|CH|CA|ES|JP|KR|DK|BE|SE|AT|CZ|IN|HU|NO|TW|NZ|MX|PT|CL|FI|HK|TR|TRAVEL|AERO|COOP|MUSEUM)[^a-zA-Z])/i', ' ', $content);
		
		return $content;
	}
	
	function wpsc_clean_all($content, $wpsc_settings, $debug = false) {	
		$content = wpsc_script_cleanup($content);
		$content = wpsc_clean_shortcode($content);
		$content = wpsc_html_cleanup($content);
		
		if ($wpsc_settings[23]->option_value == 'true') {
			$content = wpsc_email_cleanup($content);
		}
		
		if ($wpsc_settings[24]->option_value == 'true') {
			$content = wpsc_website_cleanup($content);
		}
		
		$content = wpsc_clean_text($content, $debug);
		
		return $content;
	}
	
	function wpgc_clean_all($content, $wpsc_settings) {
		$content = wpsc_script_cleanup($content);
		$content = wpsc_clean_shortcode($content);
		$content = wpsc_html_cleanup($content);
		
		if ($wpsc_settings[23]->option_value == 'true') {
			$content = wpsc_email_cleanup($content);
		}
		
		if ($wpsc_settings[24]->option_value == 'true') {
			$content = wpsc_website_cleanup($content);
		}
		
		return $content;
	}
	
	function wpbc_clean_all($content, $wpsc_settings) {
		$content = wpsc_script_cleanup($content);
		
		if ($wpsc_settings[23]->option_value == 'true') {
			$content = wpsc_email_cleanup($content);
		}
		
		if ($wpsc_settings[24]->option_value == 'true') {
			$content = wpsc_website_cleanup($content);
		}
		
		return $content;
	}
	
	function check_broken_code($rng_seed = 0, $is_running = false, $log_errors = true, $log_debug = true) {
		$start = round(microtime(true),5);
		$sql_count = 0;
		$page_list = null;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(600); 
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'spellcheck_html';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$page_table = $wpdb->prefix . 'posts';
		
		$max_pages = intval($wpsc_settings[138]->option_value);

		$total_words = 0;
		$page_count = 0;
		$post_count = 0;
		$word_count = 0;
		$error_count = 0;
		
		wpsc_set_global_vars();
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
		else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, ID, post_type FROM $page_table WHERE (post_type='page' OR post_type='post')$post_status")); $sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$divi_check = wp_get_theme();

		for ($x = 0;$x < $page_list->getSize();$x++) {
			if ($page_list[$x]->post_type == "page" ) { $page_count++; } else { $post_count++; }
			
			if ($page_list[$x]->ID == 10348) continue;
			
			$words_content = $page_list[$x]->post_content;
			
			$words_content = wpsc_content_filter($words_content);
			
			$words_content = do_shortcode($words_content);
			$words_content = wpbc_clean_all($words_content, $wpsc_settings);
			
			$debug_msg = preg_match_all('/&lt;.+&gt;/', $words_content, $html_errors);

			if (sizeof((array)$html_errors) != 0) {
				foreach($html_errors as $html_error) {
					if (isset($html_error[0])) {
						$hold = new SplFixedArray(4);
						$hold[0] = $html_error[0];
						$hold[1] = $page_list[$x]->post_title;
						$hold[2] = $page_list[$x]->ID;
						$hold[3] = 'Broken HTML';
					
						$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
						$error_list[$error_count] = $hold;
						
						$error_count++;
					}
				}
			}
			
			preg_match_all('/\[.*?\]/', $words_content, $shortcode_errors);

			if (sizeof((array)$shortcode_errors) != 0) {
				foreach($shortcode_errors as $shortcode_error) {		
					if (isset($shortcode_error[0]) && strpos($shortcode_error[0], 'vc') === false) {
						$hold = new SplFixedArray(4);
						$hold[0] = $shortcode_error[0];
						$hold[1] = $page_list[$x]->post_title;
						$hold[2] = $page_list[$x]->ID;
						$hold[3] = 'Broken Shortcode';
						
						$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
						$error_list[$error_count] = $hold;
						$error_count++;
					}
				}
			}
			unset($page_list[$x]);
		}
	
			
		wpsc_sql_insert($error_list, "Multi", $table_name);
		
		$time = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='html_scan_start_time'");
		$time = $time[0]->option_value;
		$end_time = time();
		$total_time = time_elapsed($end_time - $time);
		
		$wpdb->update($options_table, array('option_value' => $error_list->getSize()), array('option_name' => 'html_last_scan_errors')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'html_scan_running'));  $sql_count++;
		$wpdb->update($options_table, array('option_value' => $page_count), array('option_name' => 'html_page_count'));  $sql_count++;
		$wpdb->update($options_table, array('option_value' => $post_count), array('option_name' => 'html_post_count')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'html_last_scan_time'));	$sql_count++;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Broken Code", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		if ($log_debug) wpsc_print_debug_end("Broken Code Pro",$end_time);
	}
	add_action ('admincheckcode', 'check_broken_code', 10, 2);
	
	function check_broken_html($rng_seed = 0, $is_running = false, $log_errors = true, $log_debug = true) {
		$start = round(microtime(true),5);
		$page_list = null;
		$sql_count = 0;
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(600); 
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		
		$table_name = $wpdb->prefix . 'spellcheck_html';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		
		$max_pages = intval($wpsc_settings[138]->option_value);

		$total_words = 0;
		$page_count = 0;
		$post_count = 0;
		$word_count = 0;
		$error_count = 0;
		
		wpsc_set_global_vars();

		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, ID, post_type FROM $post_table WHERE (post_type='page' OR post_type='post')$post_status")); $sql_count++;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		for ($x = 0;$x < $page_list->getSize();$x++) {
			if ($page_list[$x]->post_type == "page" ) { $page_count++; } else { $post_count++; }
			
			if ($page_list[$x]->ID == 10348) continue;
			
			$words_content = $page_list[$x]->post_content;
			
			$words_content = wpsc_content_filter($words_content);
			
			$words_content = do_shortcode($words_content);
			$words_content = wpbc_clean_all($words_content, $wpsc_settings);
			
			$debug_msg = preg_match_all('/&lt;.+&gt;/', $words_content, $html_errors);

			if (sizeof((array)$html_errors) != 0) {
				foreach($html_errors as $html_error) {
					if ($html_error[0] != '') {
						$hold = new SplFixedArray(4);
						$hold[0] = $html_error[0];
						$hold[1] = $page_list[$x]->post_title;
						$hold[2] = $page_list[$x]->ID;
						$hold[3] = 'Broken HTML';
					
						$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
						$error_list[$error_count] = $hold;
						
						$error_count++;
					}
				}
			}
			unset($page_list[$x]);
		}
	
			
		wpsc_sql_insert($error_list, "Broken HTML", $table_name);
		
		$time = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='html_scan_start_time'");
		$time = $time[0]->option_value;
		$end_time = time();
		$total_time = time_elapsed($end_time - $time);
		
		$wpdb->update($options_table, array('option_value' => $error_count), array('option_name' => 'html_last_scan_errors'));$sql_count++;
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'html_scan_running')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => $page_count), array('option_name' => 'html_page_count')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => $post_count), array('option_name' => 'html_post_count'));$sql_count++;
		$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'html_last_scan_time'));		$sql_count++;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Broken HTML", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
	}
	add_action ('admincheckhtml', 'check_broken_html', 10, 2);
	
	function check_broken_shortcode($rng_seed = 0, $is_running = false, $log_errors = true, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(600); 
		global $wpdb;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		$sql_count = 0;
		
		$table_name = $wpdb->prefix . 'spellcheck_html';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		
		$total_words = 0;
		$page_count = 0;
		$post_count = 0;
		$word_count = 0;
		$error_count = 0;

		$divi_check = wp_get_theme();
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, ID, post_type FROM $post_table WHERE (post_type='page' OR post_type='post')$post_status")); $sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		for ($x = 0;$x < $page_list->getSize();$x++) {
			if ($page_list[$x]->post_type == "page" ) { $page_count++; } else { $post_count++; }
			
			if ($page_list[$x]->ID == 10348) continue;
			
			$words_content = $page_list[$x]->post_content;
			
			$words_content = wpsc_content_filter($words_content);
			
			$words_content = do_shortcode($words_content);
			$words_content = wpbc_clean_all($words_content, $wpsc_settings);
			
			preg_match_all('/\[.*?\]/', $words_content, $shortcode_errors);

			if (sizeof((array)$shortcode_errors) != 0) {
				foreach($shortcode_errors as $shortcode_error) {
					if ($shortcode_error[0] != '' && strpos($shortcode_error[0], 'vc') === false) {
						$hold = new SplFixedArray(4);
						$hold[0] = $shortcode_error[0];
						$hold[1] = $page_list[$x]->post_title;
						$hold[2] = $page_list[$x]->ID;
						$hold[3] = 'Broken Shortcode';
						
						$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
						$error_list[$error_count] = $hold;
						$error_count++;
					}
				}
			}
			unset($page_list[$x]);
		}
	
			
		wpsc_sql_insert($error_list, "Broken Shortcode", $table_name);
		
		$time = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='html_scan_start_time'");$sql_count++;
		$time = $time[0]->option_value;
		$end_time = time();
		$total_time = time_elapsed($end_time - $time);
		
		$wpdb->update($options_table, array('option_value' => $error_count), array('option_name' => 'html_last_scan_errors'));$sql_count++;
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'html_scan_running'));$sql_count++;
		$wpdb->update($options_table, array('option_value' => $page_count), array('option_name' => 'html_page_count'));$sql_count++;
		$wpdb->update($options_table, array('option_value' => $post_count), array('option_name' => 'html_post_count'));$sql_count++;
		$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'html_last_scan_time'));$sql_count++;

		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Broken Shortcodes", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
	}
	add_action ('admincheckshortcode', 'check_broken_shortcode', 10, 2);
	
	function wpsc_scan_single( $post_id ) {
		//Initialization
		ini_set('memory_limit', '512M'); //Sets the PHP memory limit
		global $wpdb;
		wpsc_set_global_vars();
		global $wpsc_settings;
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$error_list = array();
		
		//Set up Dictionary haystack based on language settings
		$language_setting = $wpdb->get_results('SELECT option_value from ' . $options_table . ' WHERE option_name="language_setting";');
		$wpsc_settings = $wpsc_settings;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);
		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);

		$wpsc_haystack = wpsc_dictionary_init($dict_file);

		$page = get_page( $post_id ); //Get the page/post
		
		$page_content = $page->post_content; //Get the content from the page/post
		
		//Cleanup the content for scanning
		$page_content = do_shortcode($page_content);
		$page_content = wpsc_content_filter($page_content);
		$page_content = wpsc_clean_all($page_content, $wpsc_settings);
		$words = explode(" ", $page_content);
		
		foreach($words as $word) {
			$word = trim($word, "'`”“");
			$word = preg_replace("/[^A-Z'’`éàèùâêîôûçëïü]/i", "", $word);
			
			//Check the word against the dictionary haystack
			if ($wpsc_haystack[strtoupper($word)] != 1) {
				if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && !is_numeric($word)) {
					array_push($error_list, array('word' => $word, 'page_type' => 'Page Content'));
				}
			}
		}
		
		return $error_list; //Return the error list to the on page editor for highlighting
	}
	
	function check_pages($rng_seed = 0, $is_running = false, $wpsc_haystack = null, $log_errors = false, $log_debug = true) {
		$end = round(microtime(true),5);
		////$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Page Content Start Time: " . date("g:i:sA") . "\r\n" );
		//fclose($debug_file);

		$start = round(microtime(true),5);
		$start_debug = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		global $wpdb;
		//global $wpsc_haystack;
		global $ignore_list;
		global $wpsc_settings;
		global $base_page_max;
		$timer_init = 0; //Initialization
		$timer_ignore = 0; //Ignore Page
		$timer_email = 0; //Ignore Emails if needed
		$timer_website = 0; //Ignore websites if needed
		$timer_upper = 0; //Ignore uppercase words if needed
		$timer_spellcheck = 0; //Spellcheck the word
		$timer_cleanup = 0; //Cleanup words before checking them
		$timer_errors = 0; //Add errors to database
		$timer_final = 0; //Finalization
		
		$start_time = time();
		wpsc_set_global_vars();
		
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$page_table = $wpdb->prefix . 'posts';
		$max_pages = $base_page_max;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
			$contents = file_get_contents($loc);
	
			$contents = str_replace("\r\n", "\n", $contents);
			$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);

		global $pro_included;
		$total_pages = $max_pages;
		$total_words = 0;
		$page_count = 0;
		$word_count = 0;
		$error_count = 0;
		
			if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $page_table WHERE post_type='page'$post_status"));
		$sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		$ignore_pages = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		$sql_count++;
		
		global $ignore_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$timer_init = round(microtime(true),5) - $start;

		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($page_list[$x]->ID == 10348) continue;
		
			$start_timer = round(microtime(true),5);
			$ignore_flag = 'false';
			foreach($ignore_pages as $ignore_check) {
				if (strtoupper(trim($page_list[$x]->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$page_count++;
			
			$timer_ignore += round(microtime(true),5) - $start_timer;
			
			$words_content = $page_list[$x]->post_content;
			$words_content = do_shortcode($words_content);
			$words_content = wpsc_content_filter($words_content);
			
			$words_content = wpsc_clean_all($words_content, $wpsc_settings);
			
			$words = explode(" ", $words_content);
				
			//$start_debug = round(microtime(true),5);
			
			$timer_page_cleanup += round(microtime(true),5) - $start_timer;

			foreach($words as $word) {
				$start_timer = round(microtime(true),5);
				
				$total_words++;
				$word = trim($word, "'`”“");
				
				if ($word == "") continue;
				if ($wpsc_haystack[strtoupper($word)] != 1) {
					$in_dictionary = false;
					
					if (!$in_dictionary) {
						$start_timer = round(microtime(true),5);
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && !is_numeric($word)) {
							$timer_upper += round(microtime(true),5) - $start_timer;
							if ($page_count <= $total_pages) {
								//$word = addslashes($word);
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $page_list[$x]->post_title;
								$hold[2] = $page_list[$x]->ID;
								$hold[3] = "Page Content";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							} else {
								$pro_error_count++;
							}
						} else { $timer_upper += round(microtime(true),5) - $start_timer; }
					}
				} else { $timer_spellcheck += round(microtime(true),5) - $start_timer; }
			}
			unset($page_list[$x]);
		}
		
		if ($log_errors) {
			$end = round(microtime(true),5);
			if ($log_debug) wpsc_print_debug("Page Content EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), $pro_error_count);
			return $pro_error_count;
		}
		
		
		
		
		if (!$end_task) {
			if ($page_count > $max_pages) $counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='pro_word_count';");
			$word_count = $word_count + intval($counter[0]->option_value);
			
			$counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='total_word_count';");
			$total_words = $total_words + intval($counter[0]->option_value);
			$wpdb->update($options_table, array('option_value' => $total_words), array('option_name' => 'total_word_count'));
			if ($page_count > $total_pages) $page_count = $total_pages;
			$wpdb->update($options_table, array('option_value' => $page_count), array('option_name' => 'page_count'));
			$sql_count += 4;
			
			
			wpsc_sql_insert($error_list, 'Page Content');
			
				if ($is_running != true) wpsc_finalize($start_time);
			}
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'page_sip'));
		$sql_count++;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Page Content", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
	}
	add_action ('admincheckpages', 'check_pages', 10, 2);

	function check_posts($rng_seed = 0, $is_running = false, $wpsc_haystack = null, $log_errors = false, $log_debug = true) {
		$start = round(microtime(true),5);
		$start_debug = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		global $wpdb;
		//global $wpsc_haystack;
		global $ignore_list;
		global $wpsc_settings;
		global $base_page_max;
		$timer_init = 0; //Initialization
		$timer_ignore = 0; //Ignore Page
		$timer_email = 0; //Ignore Emails if needed
		$timer_website = 0; //Ignore websites if needed
		$timer_upper = 0; //Ignore uppercase words if needed
		$timer_spellcheck = 0; //Spellcheck the word
		$timer_cleanup = 0; //Cleanup words before checking them
		$timer_errors = 0; //Add errors to database
		$timer_final = 0; //Finalization
		
		$start_time = time();
		wpsc_set_global_vars();
		
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$page_table = $wpdb->prefix . 'posts';
		
		$max_pages = $base_page_max;
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$divi_check = wp_get_theme();

		global $pro_included;
		$total_pages = $max_pages;
		$total_words = 0;
		$page_count = 0;
		$word_count = 0;
		$pro_word_count = 0;
		$error_count = 0;

		$post_types = get_post_types();
			$post_type_list = "AND (";
			foreach ($post_types as $type) {
				if ($type != 'revision' && $type != 'page' && $type != 'slider' && $type != 'attachment' && $type != 'optionsframework' && $type != 'product' && $type != 'wpsc-product' && $type != 'wpcf7_contact_form' && $type != 'nav_menu_item' && $type != 'gal_display_source' && $type != 'lightbox_library' && $type != 'wpcf7s')
					$post_type_list .= "post_type='$type' OR ";
			}
			$post_type_list = trim($post_type_list, " OR ");
			$post_type_list .= ")";
		
			if ($wpsc_settings[137]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $page_table WHERE post_type='post'$post_status"));
		$sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		$ignore_pages = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		$sql_count++;
		
		global $ignore_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$timer_init = round(microtime(true),5) - $start;

		for ($x = 0; $x < $page_list->getSize(); $x++) {
			if ($page_list[$x]->ID == 10348) continue;
		
			$start_timer = round(microtime(true),5);
			$ignore_flag = 'false';
			foreach($ignore_pages as $ignore_check) {
				if (strtoupper(trim($page_list[$x]->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$page_count++;
			
			$timer_ignore += round(microtime(true),5) - $start_timer;
			
			$words_content = $page_list[$x]->post_content;
			$words_content = do_shortcode($words_content);
			$words_content = wpsc_content_filter($words_content);
			
			$words_content = wpsc_clean_all($words_content, $wpsc_settings);
			$words = explode(" ", $words_content);
			
			$timer_page_cleanup += round(microtime(true),5) - $start_timer;

			foreach($words as $word) {
				$total_words++;
				$word = trim($word, "'`”“");
				
				if ($word == "") continue;
				if ($wpsc_haystack[strtoupper($word)] != 1) {
					$in_dictionary = false;
					if (!$in_dictionary) {
						$start_timer = round(microtime(true),5);
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && !is_numeric($word)) {
							$timer_upper += round(microtime(true),5) - $start_timer;
							if ($page_count <= $total_pages) {
							
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $page_list[$x]->post_title;
							$hold[2] = $page_list[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
							} else {
								$pro_error_count++;
							}
						} else { $timer_upper += round(microtime(true),5) - $start_timer; }
					}
				} else { $timer_spellcheck += round(microtime(true),5) - $start_timer; }
			}
			unset($page_list[$x]);
		}
		
		if ($log_errors) {
			$end = round(microtime(true),5);
			if ($log_debug) wpsc_print_debug("Post Content EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), $pro_error_count);
			return $pro_error_count;
		}
		
		
		if (!$end_task) {
			if ($page_count > $max_pages) { $counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='pro_word_count';");$sql_count++; 
			$word_count = $word_count + intval($counter[0]->option_value); }
			
			$counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='total_word_count';");$sql_count++;
			$total_words = $total_words + intval($counter[0]->option_value);
			$wpdb->update($options_table, array('option_value' => $total_words), array('option_name' => 'total_word_count'));$sql_count++;
			if ($page_count > $total_pages) $page_count = $total_pages;
			$wpdb->update($options_table, array('option_value' => $page_count), array('option_name' => 'post_count'));$sql_count++;
			
			$start_timer = round(microtime(true),5);

			wpsc_sql_insert($error_list, 'Post Content');
			
			$timer_errors += round(microtime(true),5) - $start_timer;
			$start_timer = round(microtime(true),5);
			
				if ($is_running != true) wpsc_finalize($start_time);
			}
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'post_sip'));
		$sql_count++;
		
		$timer_final += round(microtime(true),5) - $start_timer;
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Post Content", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		unset($sql); unset($error_list);
		
		if ($rng_seed == 10 && $wpsc_settings[0]->option_value == 'true') email_admin();
	}
	add_action ('admincheckposts', 'check_posts',10,2);
	
function wpsc_check_author_spelling($wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		global $ent_included;
		error_reporting(0);
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$user_table = $wpdb->prefix . 'usermeta';
		$username_table = $wpdb->prefix . 'users';
		$sql_count = 0;
		$total_words = 0;
		$word_count = 0;
		$error_count = 0;
		
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$options_settings = SplFixedArray::fromArray($wpdb->get_results("SELECT option_value FROM $options_table;")); $sql_count++;
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT a.meta_key, a.user_id, a.meta_value, b.user_login, b.post_author FROM $user_table a LEFT JOIN (SELECT a.post_author, b.user_login FROM $post_table a, $username_table b WHERE a.post_author = b.ID GROUP BY post_author) AS b ON b.post_author = a.user_id WHERE (a.meta_key = 'first_name' OR a.meta_key = 'last_name' OR a.meta_key = 'description' OR a.meta_key = 'twitter' OR a.meta_key = 'facebook' OR a.meta_key = 'wpseo_metadesc' OR a.meta_key='wpseo_title');")); $sql_count++;
		
		for ($x = 0; $x < $posts_list->getSize(); $x++) {
			
			if ($posts_list[$x]->user_login == '') continue;
			$words_list  = $posts_list[$x]->meta_value;
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
			
			foreach($words as $word) {
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word)))$ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && preg_match('/[^a-zA-Z]/', $word) != true) { 
							//$word = addslashes($word);
							$to_add = true;
							if ($posts_list[$x]->meta_key == "first_name") { $post_type = "Author First Name";
							} elseif ($posts_list[$x]->meta_key == "last_name") { $post_type = "Author Last Name";
							} elseif ($posts_list[$x]->meta_key == "description") { $post_type = "Author Biography";
							} elseif ($posts_list[$x]->meta_key == 'wpseo_metadesc') { $post_type = "Author SEO Description";
								if (!$ent_included) $to_add = false;
							} elseif ($posts_list[$x]->meta_key == 'wpseo_title') { $post_type = "Author SEO Title";
								if (!$ent_included) $to_add = false;
							} else { $post_type = $posts_list[$x]->meta_key; }
							
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->user_login;
							$hold[2] = $posts_list[$x]->user_id;
							$hold[3] = $post_type;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
		}
		
		if ($post_count > $total_posts) $post_count = $total_posts;
		
		wpsc_sql_insert($error_list, 'Multi');

		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Author", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
}

function check_site_name($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $wpdb;
		global $end_included;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$opt_table = $wpdb->prefix . 'options';
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(600); 
		$sql_count = 0;
		
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		if (!$ent_included) $max_pages = 500;
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);

		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		$error_count = 0;
$word_count = 0;
		$max_time = ini_get('max_execution_time'); 
		if ($is_running != true) {
			wpsc_set_global_vars();
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
			$start_time = time();
		}
		$ind_start_time = time();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');$sql_count++;

		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT * FROM $opt_table WHERE option_name='blogname'"));$sql_count++;
		
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
	

		for ($x = 0; $x < $posts_list->getSize(); $x++) {
			if (!isset($posts_list[$x]->post_title)) continue;
			$words_list = $posts_list[$x]->option_value;
			
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$total_words++;				
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_check = str_replace("'", "\'", $ignore_check);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$dict_word = str_replace("'", "\'", $dict_word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && preg_match('/[^a-zA-Z]/', $word) != true) {
							//$word = addslashes($word);							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = 0;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			$end_task = false;
			if (((time() - $ind_start_time) >= $max_time - 3) && count($page_list) > 0) {
				$end_task = true;
				wp_schedule_single_event(time() - 10 + 1, 'admincheckposts', array(true, $posts_list));
				break;
			}
			if($end_task) break;
		}
		
		
			
			
			
			
		
		if (!$end_task) {

		if ($post_count > $total_posts) $post_count = $total_posts;
		
		
		
		wpsc_sql_insert($error_list, 'Sitename');
			
			if ($is_running != true) wpsc_finalize($start_time);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Sitename", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
}

function check_site_tagline($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $wpdb;
		global $ent_included;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$opt_table = $wpdb->prefix . 'options';
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(600); 
		$sql_count = 0;
		
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		if (!$ent_included) $max_pages = 500;
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		$error_count = 0;
$word_count = 0;
		$max_time = ini_get('max_execution_time'); 
		if ($is_running != true) {
			wpsc_set_global_vars();
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
			$start_time = time();
		}
		$ind_start_time = time();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');$sql_count++;

		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT * FROM $opt_table WHERE option_name='blogdescription'"));$sql_count++;
		
		//global $ignore_list;
		//global $dict_list;
		//global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		for ($x = 0; $x < $posts_list->getSize(); $x++) {
			$words_list = $posts_list[$x]->option_value;
			
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && preg_match('/[^a-zA-Z]/', $word) != true) {
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = 0;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			$end_task = false;
			if (((time() - $ind_start_time) >= $max_time - 3) && count($page_list) > 0) {
				$end_task = true;
				wp_schedule_single_event(time() - 10 + 1, 'admincheckposts', array(true, $posts_list));
				break;
			}
			if($end_task) break;
		}
		
		
			
			
			
			
		
		if (!$end_task) {

		if ($post_count > $total_posts) $post_count = $total_posts;
		
		
		
		wpsc_sql_insert($error_list, 'Site Tagline');
			
			if ($is_running != true) {
				$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'scan_in_progress')); $sql_count++;
			$end_time = time();
			$total_time = time_elapsed($end_time - $start_time + 6);
			$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'last_scan_finished')); $sql_count++;
		}
		}
		
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Site Tagline", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		
}

function check_authors($rng_seed = 0, $wpsc_haystack = null, $log_debug = true) {

	global $scan_delay;
	sleep($scan_delay);
	
	ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
	
	global $wpdb;
	global $ent_included;
	global $pro_included;
	$table_name = $wpdb->prefix . 'spellcheck_words';
	$options_table = $wpdb->prefix . 'spellcheck_options';
	$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
	$start_time = time(); 
	
	$post_table = $wpdb->prefix . 'posts';
	$posts_list = $wpdb->get_results("SELECT * FROM $post_table GROUP BY post_author");

	wpsc_check_author_spelling($wpsc_haystack);
	check_site_tagline(true, $wpsc_haystack);
	check_site_name(true, $wpsc_haystack);
	if ($ent_included) {
		//check_author_seotitle_ent(true);
		//check_author_seodesc_ent(true);
	}
	
	$end_time = time();
	$total_time = time_elapsed($end_time - $start_time + 6);
	$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'last_scan_finished')); 
	$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'author_sip'));
}
add_action ('admincheckauthors', 'check_authors');

	function check_cf7($rng_seed = 0, $is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		if (!$is_running) sleep($scan_delay);
		global $wpdb;
		global $ent_included;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		global $pro_included;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		$sql_count = 0;
		
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		if (!$ent_included) $max_pages = 500;
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);

		global $pro_included;
		$total_posts = 100;
		if ($pro_included) $total_posts = 1000;
		if ($ent_included) $total_posts = PHP_INT_MAX;
		$total_words = 0;
		$post_count = 0;
		$error_count = 0;
$word_count = 0;
		$word_count = 0;
		if ($is_running != true) {
			wpsc_set_global_vars();
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$start_time = time();
		}

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');$sql_count++;
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = array('publish', 'draft'); }
		else { $post_status = array('publish'); }

		$posts_list = SplFixedArray::fromArray(get_posts(array('posts_per_page' => $total_posts, 'post_type' => 'wpcf7_contact_form', 'post_status' => $post_status)));$sql_count++;
		
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		for ($x = 0; $x < $posts_list->getSize(); $x++) {
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($posts_list[$x]->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$post_count++;
			$words_list = $posts_list[$x]->post_content;
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if (isset($wpsc_haystack[strtoupper($word)])) { if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && preg_match('/[^a-zA-Z]/', $word) != true) {
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							
							$error_count++;
						}
					}
				} }	
			}
		}
		
		
		$counter = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name ='total_word_count';");$sql_count++;
		$total_words = $total_words + intval($counter[0]->option_value);
		$wpdb->update($options_table, array('option_value' => $total_words), array('option_name' => 'total_word_count'));$sql_count++;
		
		$word_count = $word_count + intval($counter[0]->option_value);
		
		wpsc_sql_insert($error_list, 'Contact Form 7');
			
			if ($is_running != true) {
				$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'scan_in_progress')); $sql_count++;
			$end_time = time();
			$total_time = time_elapsed($end_time - $start_time + 6);
			$wpdb->update($options_table, array('option_value' => $total_time), array('option_name' => 'last_scan_finished')); $sql_count++;
		}
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'cf7_sip'));$sql_count++;
		
		
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Contact Form 7", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
	}
	add_action ('admincheckcf7', 'check_cf7');
	
	function wphc_clear_results($clear_type = '') {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spellcheck_html';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'html_page_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'html_post_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'html_media_count')); 

		$wpdb->delete($table_name, array('ignore_word' => false));
		$wpdb->get_results("ALTER TABLE $table_name AUTO_INCREMENT = 1");
	}

	function clear_results($clear_type = '') {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'total_word_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'page_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'post_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'media_count')); 

		$wpdb->delete($table_name, array('ignore_word' => false));
		$wpdb->get_results("ALTER TABLE $table_name AUTO_INCREMENT = 1");
		if ($clear_type == 'full') {
			$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'pro_word_count')); 
		}
	}
	
	function clear_empty_results($clear_type = '') {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'empty_page_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'empty_post_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'empty_media_count')); 
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'pro_empty_count')); 

		$wpdb->delete($table_name, array('ignore_word' => false));
		$wpdb->get_results("ALTER TABLE $table_name AUTO_INCREMENT = 1");
		
		if ($clear_type == 'full') {
			$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'empty_factor')); 
			$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'pro_empty_count')); 
		} 
	}
	
	function set_scan_in_progress($rng_seed = 0) {
		global $wpdb;
		global $pro_included;
		global $ent_included;
		global $wpsc_settings;
		$options_table = $wpdb->prefix . 'spellcheck_options';
		
		$settings = $wpdb->get_results('SELECT option_value FROM ' . $options_table);
		
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'entire_scan'));
		
		if ($settings[4]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'page_sip'));
		if ($settings[5]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'post_sip'));
		if ($settings[37]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'cf7_sip'));
		if ($settings[44]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'author_sip'));
			
		if ($ent_included || $pro_included) {
		if ($settings[7]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'menu_sip'));
		if ($settings[14]->option_value == 'true' || $settings[38]->option_value == 'true' || $settings[39]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'tag_title_sip'));
		if ($settings[15]->option_value == 'true' || $settings[40]->option_value == 'true' || $settings[41]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'cat_title_sip'));
		if ($settings[16]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'seo_desc_sip'));
		if ($settings[17]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'seo_title_sip'));
		if ($settings[30]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'slider_sip'));
		if ($settings[31]->option_value == 'true')
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'media_sip'));
		if ($settings[36]->option_value == 'true' && (is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active('wp-e-commerce/wp-shopping-cart.php')))
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'ecommerce_sip'));
		}
	}
	
	function wpsc_clear_scan() {
		global $wpdb;
		global $wpsc_settings;
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$settings = $wpsc_settings;
		
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'entire_scan'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'page_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'post_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'cf7_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'author_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'menu_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'tag_title_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'cat_title_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'seo_desc_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'seo_title_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'slider_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'media_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'tag_desc_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'tag_slug_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'cat_desc_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'cat_slug_sip'));
		$wpdb->update($options_table, array('option_value' => 'false'), array('option_name' => 'ecommerce_sip'));
	}
	
	function check_errors_wpsc($log_debug = false) {
		global $wpdb;
		global $ent_included;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		set_time_limit(600); 
		
		$settings = $wpdb->get_results('SELECT option_value FROM ' . $options_table);
		
		$wpdb->update($options_table, array('option_value' => '0'), array('option_name' => 'pro_word_count')); 
		
		$language_setting = $wpdb->get_results('SELECT option_value from ' . $options_table . ' WHERE option_name="language_setting";');
		$loc = dirname(__FILE__) . "/dict/" . $language_setting[0]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_list = explode("\n", $contents);

		foreach ($dict_list as $value) {
			$wpsc_haystack[strtoupper($value)] = 1;
		}
		
		$error_count = 0;
		$last_count = 0;
		
		$error_count += check_posts(0, false, null, true);
		$last_count = $error_count;
		
		$error_count += check_pages(0, false, null, true);
		$last_count = $error_count;
		
		$error_count += check_menus_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_page_title_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_post_title_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_post_tags_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_post_categories_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_yoast_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_seo_titles_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_slider_titles_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_slider_captions_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_media_titles_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$error_count += check_author_seodesc_free(true, $wpsc_haystack);
		$last_count = $error_count;
		
		$wpdb->update($options_table, array('option_value' => $error_count), array('option_name' => 'pro_word_count'));
		$wpdb->update($options_table, array('option_value' => "false"), array('option_name' => 'free_sip'));
	}
	add_action ('admincheckerrorswpsc', 'check_errors_wpsc');

	function scan_site_event($rng_seed = 0, $log_debug = true) {
		$start = round(microtime(true),5);
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(600);
		global $wpdb;
		global $pro_included;
		global $ent_included;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$page_list = null;
		$post_list = null;
		$sql_count = 0;
		
		if ($rng_seed = 10) clear_results();
		
		$wpsc_haystack = null;
		
		$start_time = time(); 
		$wpdb->update($options_table, array('option_value' => $start_time), array('option_name' => 'scan_start_time')); $sql_count++;

		$settings = $wpdb->get_results('SELECT option_value FROM ' . $options_table);$sql_count++;
		
		wpsc_set_global_vars();
		
		if (!$ent_included) check_errors_wpsc($log_debug);
		
		if ($ent_included) {
		if ($settings[4]->option_value == 'true' || $settings[12]->option_value == 'true' || $settings[18]->option_value == 'true')
			wp_schedule_single_event(time(), 'admincheckpages_ent', array ($rng_seed, true, null, $wpsc_haystack, $log_debug));
		if ($settings[5]->option_value =='true' || $settings[13]->option_value == 'true' || $settings[19]->option_value == 'true')
			wp_schedule_single_event(time(), 'admincheckposts_ent', array ($rng_seed, true, null, $wpsc_haystack, $log_debug));
		if ($settings[36]->option_value =='true' && (is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active('wp-e-commerce/wp-shopping-cart.php')))
			wp_schedule_single_event(time(), 'admincheckecommerce_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[7]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckmenus_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[14]->option_value =='true' || $settings[38]->option_value =='true' || $settings[39]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckposttags_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[15]->option_value =='true' || $settings[41]->option_value =='true' || $settings[40]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckcategories_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[16]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckseodesc_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[17]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckseotitles_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[30]->option_value =='true')
			wp_schedule_single_event(time(), 'adminchecksliders_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[31]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckmedia_ent', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[37]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckcf7', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[44]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckauthors', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[148]->option_value =='true')
			wp_schedule_single_event(time(), 'wpsccheckwidgets', array ($rng_seed, true, $log_debug));
		} else {
		if ($settings[4]->option_value == 'true')
			wp_schedule_single_event(time(), 'admincheckpages', array ($rng_seed, true, $wpsc_haystack, false, $log_debug ));
		if ($settings[5]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckposts', array ($rng_seed, true , $wpsc_haystack, false, $log_debug));
		if ($settings[44]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckauthors', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		if ($settings[37]->option_value =='true')
			wp_schedule_single_event(time(), 'admincheckcf7', array ($rng_seed, true, $wpsc_haystack, $log_debug));
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Initialization", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), "N/A");
	}
	add_action ('adminscansite', 'scan_site_event');

	function time_elapsed($secs){
		if ($secs > 300000000) $secs = 0;
		$secs += 3;
	    $bit = array(
	        ' year'        => $secs / 31556926 % 12,
	        ' week'        => $secs / 604800 % 52,
	        ' day'        => $secs / 86400 % 7,
	        ' hour'        => $secs / 3600 % 24,
	        ' minute'    => $secs / 60 % 60,
	        ' second'    => $secs % 60
	        );
        
	    foreach($bit as $k => $v){
	        if($v > 1)$ret[] = $v . $k . 's';
	        if($v == 1)$ret[] = $v . $k;
	        }
	    array_splice($ret, count($ret)-1, 0, ' ');
	    $ret[] = '';
    
	    return join(' ', $ret);
	    }

	function send_test_email() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
		set_time_limit(600); 

		$settings = $wpdb->get_results('SELECT option_value FROM ' . $table_name . ' WHERE option_name="email_address";');
		$words_list = $wpdb->get_results('SELECT word FROM ' . $words_table . ' WHERE ignore_word is false');
		
		$output = 'This is a test email sent from WP Spell Check on ' . get_option( 'blogname' );
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: " . get_option( 'admin_email' );
		

		$to_emails = explode(',', $settings[0]->option_value);
		$valid_email = false;
		foreach($to_emails as $email_test) {
			if (!filter_var($email_test, FILTER_VALIDATE_EMAIL) === false) {
				$valid_email = true;
			}
		}
		if (!$valid_email) {
			return 'Please enter a valid email address';
		}
		//array_walk($to_emails, 'trim_value');

		if (wp_mail($to_emails, 'Test Email from WP Spell Check', $output, $headers)) {
			return "<h3 style='color: rgb(0, 115, 0);'>A test email has been sent</h3>";
		} else {
			return "An error has occurring in sending the test email";
		}
	}

	function email_admin() {
		global $wpdb;
		global $pro_included;
		global $ent_included;
		$table_name = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$empty_table = $wpdb->prefix . 'spellcheck_empty';
		$html_table = $wpdb->prefix . 'spellcheck_html';
		set_time_limit(600); 
		sleep(2);

		$settings = $wpdb->get_results('SELECT option_value FROM ' . $table_name . ' WHERE option_name="email_address";');

		$words_list = $wpdb->get_var('SELECT COUNT(*) FROM ' . $words_table . ' WHERE ignore_word is false');
		$empty_list = $wpdb->get_var('SELECT COUNT(*) FROM ' . $empty_table . ' WHERE ignore_word is false');
		$html_list = $wpdb->get_var('SELECT COUNT(*) FROM ' . $html_table . ' WHERE ignore_word is false');
		$login_url = wp_login_url();
		
		$date = date('l jS') . " of " . date('F Y') . " at " . date('g:i:s A');
		$options_url = get_site_url() . '/wp-admin/admin.php?page=wp-spellcheck-options.php';

		$output = '<strong>This email was sent from your website "' . get_option( 'blogname' ) . '" by the WP Spell Check plugin on ' . $date . '</strong><br /><br />';
		 
		$output .= '<strong>We have finished the scan of your website and detected:</strong><br /><br />';
		
		$output .= '<strong>- ' . $words_list . ' Spelling Errors</strong><br />';
		
		$output .= '<strong>- ' . $empty_list . ' Empty Fields</strong> <br />';
		$output .= '<strong>- ' . $html_list . ' Broken Code</strong> <br /><br />';
		$output .= '<strong><a href="' . $login_url . '">Click here</a> to fix them now to improve your website Literacy Factor and SEO.</strong><br /><br />';
		
		$output .= '------------------------------------------------------------------------<br />';
		 
		if (!$pro_included && !$ent_included) $output .= 'NOTE: You are using the free version of WP Spell check. <a href="https://www.wpspellcheck.com/features/">Upgrade</a> to Premium today to scan your entire site';

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: " . get_option( 'admin_email' );

		$to_emails = explode(',', $settings[0]->option_value);
		//array_walk($to_emails, 'trim_value');

		wp_mail($to_emails, 'WP Spellcheck report for ' . get_option( 'blogname' ), $output, $headers);
	}

	
	function show_feature_window() {
		/*echo "<div class='request-feature-container'>";
		echo "<div class='request-feature-popup' style='display: none;'>";
		echo "<a href='' class='close-popup'>X</a>";
		echo "<img src='" . plugin_dir_url( __FILE__ ) . "images/logo.png' alt='WP Spell Check' /><br />";
		echo "<h3>We love hearing from you</h3>";
		echo "<p>Please report your problem to make the WP Spell Check plugin better</p>";
		echo "<a href='https://www.wpspellcheck.com/report-a-problem' target='_blank'><button>Report a Problem</button></a>";
		echo "<p>Please note: Support requests will not be handled through this form</p>";
		echo "</div>";
		echo "<div class='request-feature'><a href='' class='request-feature-link'>Report a Problem</a></div>";
		echo"</div>";*/
	}
	
	function check_author_seotitle_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
			global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$user_table = $wpdb->prefix . 'usermeta';
		$username_table = $wpdb->prefix . 'users';
		set_time_limit(600); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		$sql_count = 0;
		
		
		
		$max_pages = PHP_INT_MAX;
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		if ($wpsc_haystack == null) {
			$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
			$contents = file_get_contents($loc);
	
			$contents = str_replace("\r\n", "\n", $contents);
			$dict_file = explode("\n", $contents);

			foreach ($dict_file as $value) {
				$wpsc_haystack[strtoupper($value)] = 1;
			}
			unset($contents); unset($dict_file);
			
			foreach ($dict_list as $value) {
				$wpsc_haystack[strtoupper($value->word)] = 1;
			}
		}
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		$max_time = ini_get('max_execution_time'); 
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = array();
		error_reporting(0);
		
		$ind_start_time = time();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');$sql_count++;
		

		$posts_list = $wpdb->get_results("SELECT a.meta_key, a.meta_value, b.user_login, b.post_author FROM $user_table a LEFT JOIN (SELECT a.post_author, b.user_login FROM $post_table a, $username_table b WHERE a.post_author = b.ID GROUP BY post_author) AS b ON b.post_author = a.user_id WHERE a.meta_key='wpseo_title';"); $sql_count++;

		foreach ($posts_list as $post) {
			array_shift($posts_list);

			$words_list = $post->meta_value;
			
			$words_list = wpsc_clean_text($words_list);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							if ($post_count <= $total_posts) {
							//$word = addslashes($word);
							$error_count++; array_push($error_list, array('word' => $word, 'page_name' => $post->post_title, 'page_id' =>$post->ID, 'page_type' => 'Author SEO Title'));
							} else {
								
							}
						}
					}
				}	
			}
			$end_task = false;
			if (((time() - $ind_start_time) >= $max_time - 3) && count($page_list) > 0) {
				$end_task = true;
				wp_schedule_single_event(time() + 1, 'admincheckposts', array(true, $posts_list));
				break;
			}
			if($end_task) break;
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Author SEO Title EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return $error_count;
}

function check_author_seodesc_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
			global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$post_table = $wpdb->prefix . 'posts';
		$user_table = $wpdb->prefix . 'usermeta';
		$username_table = $wpdb->prefix . 'users';
		set_time_limit(600); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		$sql_count = 0;
		
		
		
		$max_pages = PHP_INT_MAX;
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		if ($wpsc_haystack == null) {
			$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
			$contents = file_get_contents($loc);
	
			$contents = str_replace("\r\n", "\n", $contents);
			$dict_file = explode("\n", $contents);

			foreach ($dict_file as $value) {
				$wpsc_haystack[strtoupper($value)] = 1;
			}
			unset($contents); unset($dict_file);
			
			foreach ($dict_list as $value) {
				$wpsc_haystack[strtoupper($value->word)] = 1;
			}
		}

$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		$max_time = ini_get('max_execution_time'); 
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = array();
		error_reporting(0);
		$ind_start_time = time();

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";'); $sql_count++;
		

		$posts_list = $wpdb->get_results("SELECT a.meta_key, a.meta_value, b.user_login, b.post_author FROM $user_table a LEFT JOIN (SELECT a.post_author, b.user_login FROM $post_table a, $username_table b WHERE a.post_author = b.ID GROUP BY post_author) AS b ON b.post_author = a.user_id WHERE a.meta_key = 'wpseo_metadesc';"); $sql_count++;

		foreach ($posts_list as $post) {
			array_shift($posts_list);

			$words_list = $post->meta_value;
			
			$words_list = wpsc_clean_text($words_list);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							if ($post_count <= $total_posts) {
							//$word = addslashes($word);
							$error_count++; array_push($error_list, array('word' => $word, 'page_name' => $post->post_title, 'page_id' =>$post->ID, 'page_type' => 'Author SEO Description'));
							} else {
								
							}
						}
					}
				}	
			}
		}	
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Author SEO Desc EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return $error_count;
	}
	

	function check_menus_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'posts';
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		global $wpsc_settings;
		wpsc_set_global_vars();
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $words_table WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$sql_count++;
			$start_time = time();
		
		}
		global $ignore_list;
		global $dict_list;
		
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		

		$menus = SplFixedArray::fromArray($wpdb->get_results('SELECT post_title, ID FROM ' . $table_name . ' WHERE post_type ="nav_menu_item" LIMIT ' . $max_pages . ';'));
		$sql_count++;
		
		for ($x = 0; $x < $menus->getSize(); $x++) {
			$word_list = html_entity_decode(strip_tags($menus[$x]->post_title), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				//$ignore_check = str_replace("'", "\'", $word);
				//$ignore_word = $wpdb->get_results("SELECT word FROM $words_table WHERE word='" . $ignore_check . "' AND ignore_word = true");
				if ($wpsc_haystack[strtoupper($word)] != 1) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						$table_name = $wpdb->prefix . 'spellcheck_words';						
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $menus[$x]->post_title;
							$hold[2] = $menus[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			unset($menus[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Menus EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return $error_list->getSize();
	}
	

	function check_page_title_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$end = round(microtime(true),5);
		////$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Page Content Start Time: " . date("g:i:sA") . "\r\n" );
		//fclose($debug_file);

		$start = round(microtime(true),5);
		$start_debug = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		global $wpdb;
		//global $wpsc_haystack;
		global $ignore_list;
		global $wpsc_settings;
		$timer_init = 0; //Initialization
		$timer_ignore = 0; //Ignore Page
		$timer_email = 0; //Ignore Emails if needed
		$timer_website = 0; //Ignore websites if needed
		$timer_upper = 0; //Ignore uppercase words if needed
		$timer_spellcheck = 0; //Spellcheck the word
		$timer_cleanup = 0; //Cleanup words before checking them
		$timer_errors = 0; //Add errors to database
		$timer_final = 0; //Finalization
		
		$start_time = time();
		wpsc_set_global_vars();
		
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$page_table = $wpdb->prefix . 'posts';
		
		//$language_setting = $wpdb->get_results('SELECT option_value from ' . $options_table . ' WHERE option_name="language_setting";');
		
		//$max_pages = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name = 'pro_max_pages'");
		$max_pages = intval($wpsc_settings[138]->option_value);
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
			$contents = file_get_contents($loc);
	
			$contents = str_replace("\r\n", "\n", $contents);
			$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);

		global $pro_included;
		$total_pages = $max_pages;
		$total_words = 0;
		$page_count = 0;
		$word_count = 0;
		$error_count = 0;
		
			if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $page_table WHERE post_type='page'$post_status"));
		$sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		$ignore_pages = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		$sql_count++;
		
		global $ignore_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$timer_init = round(microtime(true),5) - $start;

		for ($x = 0; $x < $page_list->getSize(); $x++) {
			$ignore_flag = 'false';
			foreach($ignore_pages as $ignore_check) {
				if (strtoupper(trim($page_list[$x]->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$page_count++;
		
			//Page Title
			$word_list = html_entity_decode(strip_tags($page_list[$x]->post_title), ENT_QUOTES, 'utf-8');
				
				$word_list = wpsc_clean_all($word_list, $wpsc_settings);

				$words = explode(' ', $word_list);

				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $page_list[$x]->post_title;
								$hold[2] = $page_list[$x]->ID;
								$hold[3] = "Page Title";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}
			
			//Page Slug
			$desc_title = $page_list[$x]->post_title;
				$desc_id = $page_list[$x]->ID;
				$desc = html_entity_decode(strip_tags($page_list[$x]->post_name), ENT_QUOTES, 'utf-8');
				
				$desc = wpsc_clean_all($desc, $wpsc_settings);
				
				$words = explode(' ', $desc);

				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
									
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $desc_title;
								$hold[2] = $desc_id;
								$hold[3] = "Page Slug";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}
				
			unset($page_list[$x]);
		}
		
		//Widgets
		$widget_instances = get_option('widget_text');
		foreach($widget_instances as $widget) {
			$text = $widget['text'];
			
			$text = do_shortcode($text);
			$text = wpsc_clean_all($text, $wpsc_settings, false);
			$words = explode(" ", $text);
			
			foreach($words as $word) {
				$total_words++;
				$word = trim($word, "'`”“");
				
				if ($wpsc_haystack[strtoupper($word)] != 1 && $word != '') {
					//Add the error to a new fixed holding array
					$hold = new SplFixedArray(4);
					$hold[0] = $word;
					$hold[1] = $widget['title'];
					$hold[2] = 0;
					$hold[3] = "Widget Content";
					
					$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
					$error_list[$error_count] = $hold;
					$error_count++;
				}
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Page EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();
	}
	

	function check_post_title_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$end = round(microtime(true),5);
		////$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Start Time: " . date("g:i:sA") . "\r\n" );
		//fclose($debug_file);
		
		
		$start = round(microtime(true),5);
		$start_debug = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		global $wpdb;
		//global $wpsc_haystack;
		global $ignore_list;
		global $wpsc_settings;
		$timer_init = 0; //Initialization
		$timer_ignore = 0; //Ignore Page
		$timer_email = 0; //Ignore Emails if needed
		$timer_website = 0; //Ignore websites if needed
		$timer_upper = 0; //Ignore uppercase words if needed
		$timer_spellcheck = 0; //Spellcheck the word
		$timer_cleanup = 0; //Cleanup words before checking them
		$timer_errors = 0; //Add errors to database
		$timer_final = 0; //Finalization
		
		$start_time = time();
		wpsc_set_global_vars();
		
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$page_table = $wpdb->prefix . 'posts';
		
		//$language_setting = $wpdb->get_results('SELECT option_value from ' . $options_table . ' WHERE option_name="language_setting";');
		
		//$max_pages = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name = 'pro_max_pages'");
		$max_pages = intval($wpsc_settings[138]->option_value);
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$divi_check = wp_get_theme();

		global $pro_included;
		$total_pages = $max_pages;
		$total_words = 0;
		$page_count = 0;
		$word_count = 0;
		$error_count = 0;

		$post_types = get_post_types();
			$post_type_list = "AND (";
			foreach ($post_types as $type) {
				if ($type != 'revision' && $type != 'page' && $type != 'slider' && $type != 'attachment' && $type != 'optionsframework' && $type != 'product' && $type != 'wpsc-product' && $type != 'wpcf7_contact_form' && $type != 'nav_menu_item' && $type != 'gal_display_source' && $type != 'lightbox_library' && $type != 'wpcf7s')
					$post_type_list .= "post_type='$type' OR ";
			}
			$post_type_list = trim($post_type_list, " OR ");
			$post_type_list .= ")";
		
			if ($wpsc_settings[137]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $page_table WHERE post_type='post'$post_status"));
		$sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 

		$ignore_pages = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		$sql_count++;
		
		global $ignore_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$timer_init = round(microtime(true),5) - $start;

		for ($x = 0; $x < $page_list->getSize(); $x++) {
		
			//Post Title
			if ($wpsc_settings[13]->option_value == 'true') {
				$word_list = html_entity_decode(strip_tags($page_list[$x]->post_title), ENT_QUOTES, 'utf-8');
				$word_list = wpsc_clean_all($word_list, $wpsc_settings);
				$words = explode(' ', $word_list);

				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $page_list[$x]->post_title;
								$hold[2] = $page_list[$x]->ID;
								$hold[3] = "Post Title";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}
			}
			
			//Post Slug
			$desc_title =  $page_list[$x]->post_title;
				$desc_id =  $page_list[$x]->ID;
				$desc = html_entity_decode(strip_tags( $page_list[$x]->post_name), ENT_QUOTES, 'utf-8');
				$desc = wpsc_clean_all($desc, $wpsc_settings);
				$words = explode(' ', $desc);

				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
									
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $desc_title;
								$hold[2] = $desc_id;
								$hold[3] = "Post Slug";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}
			unset($page_list[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Post(End) EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return $error_list->getSize();
	}
	

function check_post_tags_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		
		$tags_list = SplFixedArray::fromArray(get_tags()); $sql_count++;
		
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Options Array: " . print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);


		for($x = 0; $x < $tags_list->getSize(); $x++) {
			$words = array();
			
			if ($wpsc_settings[14]->option_value =='true') {
				$words = wpsc_clean_text(strip_tags(html_entity_decode($tags_list[$x]->name)));
				$words = wpsc_clean_all($words, $wpsc_settings);
			
				$words = explode(' ',$words);
				
				//Tag Titles
				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = trim($word, '"');
					$word = trim($word);
					if ($word == "") continue;
					
					
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}
						

						if (!$in_dictionary) {
						
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $tags_list[$x]->post_title;
								$hold[2] = $tags_list[$x]->term_id;
								$hold[3] = "Tag Title";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}
			}

			if ($wpsc_settings[38]->option_value =='true') {
				//Tag Descriptions
				$words = wpsc_clean_text(strip_tags(html_entity_decode($tags_list[$x]->description)));
				$words = wpsc_clean_all($words, $wpsc_settings);
				$words = explode(' ', $words);
				
				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					if ($word == "") continue;
					
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}
						

						if (!$in_dictionary) {
						
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $tags_list[$x]->post_title;
								$hold[2] = $tags_list[$x]->term_id;
								$hold[3] = "Tag Description";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}		
			}
			
			if ($wpsc_settings[39]->option_value =='true') {
				//Tag Slugs
				$words = explode('-', strip_tags(html_entity_decode($tags_list[$x]->slug)));
				
				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}
						

						if (!$in_dictionary) {
						
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $tags_list[$x]->post_title;
								$hold[2] = $tags_list[$x]->term_id;
								$hold[3] = "Tag Slug";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
							}
						}
					}
				}
			}
			
			unset($tags_list[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Tag EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();
	}

	function check_post_categories_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		

		
		$cats_list = SplFixedArray::fromArray(get_categories()); $sql_count++;

		for ($x = 0; $x < $cats_list->getSize(); $x++) {
			$words = array();
			
			if ($wpsc_settings[15]->option_value =='true') {
				//Cat Titles
				$words = strip_tags(html_entity_decode($cats_list[$x]->name));
				$words = wpsc_clean_all($words, $wpsc_settings);
				$words = explode(' ', $words);
			
				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $cats_list[$x]->post_title;
								$hold[2] = $cats_list[$x]->term_id;
								$hold[3] = "Category Title";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
						}
						}
					}	
				}
			}
			
			if ($wpsc_settings[40]->option_value =='true') {
				//Cat Descriptions
				$words = strip_tags(html_entity_decode($cats_list[$x]->description));
				$words = wpsc_clean_all($words, $wpsc_settings);
				$words = explode(' ', $words);
			
				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $cats_list[$x]->post_title;
								$hold[2] = $cats_list[$x]->term_id;
								$hold[3] = "Category Description";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
						}
						}
					}	
				}
			}
			
			if ($wpsc_settings[41]->option_value =='true') {
				//Cat Slugs
				$words = explode('-', strip_tags(html_entity_decode($cats_list[$x]->slug)));
			
				foreach($words as $word) {
					$word_count++;
					$total_words++;
					$word = str_replace(' ', '', $word);
					$word = str_replace('=', '', $word);
					$word = str_replace(',', '', $word);
					$word = trim($word, "?!.,'()`”:“@$#-%\=/");
					$word = trim($word, '"');
					$word = trim($word);
					$word = preg_replace("/[0-9]/", "", $word);
					$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
					if ($word == "") continue;
					$ignore_check = str_replace("'", "\'", $word);
					$ignore_word = false;
					if(isset($ignore_list)) { 
						foreach($ignore_list as $ignore) {
						if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
					}
						}
					if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
						$dict_word = str_replace("'", "\'", $word);
						$in_dictionary = false;
						if(isset($dict_list)) { 
						foreach($dict_list as $dict) {
							if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
						}
						}

						if (!$in_dictionary) {
							
							if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
								//Add the error to a new fixed holding array
								$hold = new SplFixedArray(4);
								$hold[0] = $word;
								$hold[1] = $cats_list[$x]->post_title;
								$hold[2] = $cats_list[$x]->term_id;
								$hold[3] = "Category Slug";
								
								$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
								$error_list[$error_count] = $hold;
								$error_count++;
						}
						}
					}	
				}
			}
			
			unset($cats_list[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Category EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();
	}

	function check_yoast_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);
		$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $words_table WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$posts_table = $wpdb->prefix . 'posts';
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		if ($wpsc_settings[136]->option_value == 'true') { $page_status = true; }
		else { $page_status = false; }
		
		if ($wpsc_settings[137]->option_value == 'true') { $post_status = true; }
		else { $post_status = false; }
		
		$ain_active = is_plugin_active("all-in-one-seo-pack/all_in_one_seo_pack.php");
		$su_active = is_plugin_active("seo-ultimate/seo-ultimate.php");
		$yoast_active = is_plugin_active("wordpress-seo/wp-seo.php");

		$results = SplFixedArray::fromArray($wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE meta_key="_yoast_wpseo_metadesc" OR meta_key="_aioseop_description" OR meta_key="_su_description" LIMIT ' . $max_pages)); $sql_count++;
		
		
		for($x = 0;$x < $results->getSize();$x++) {
			$desc = $results[$x];
			$post_store = $desc;
			$page_results = $wpdb->get_results('SELECT * FROM ' . $posts_table . ' WHERE ID=' . $desc->post_id);
			
			if ($page_results[0]->post_title == '') continue;
			if ($page_results[0]->post_status == 'draft' && $page_results[0]->post_type == 'page' && !$page_status) continue;
			if ($page_results[0]->post_status == 'draft' && $page_results[0]->post_type != 'page' && !$post_status) continue;
			
			$desc_type = $desc->meta_key;
			$desc = html_entity_decode(strip_tags($desc->meta_value), ENT_QUOTES, 'utf-8');
			$desc = wpsc_clean_all($desc, $wpsc_settings);
			$words = explode(' ', $desc);

			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = str_replace('–', ' ', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && ($yoast_active || $ain_active || $su_active)) {
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $page_results[0]->post_title;
							$hold[2] = $page_results[0]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							if ($desc_type == '_yoast_wpseo_metadesc' && $yoast_active) {
								$hold[3] =  'Yoast SEO Description';
							} elseif ($desc_type == '_aioseop_description' && $ain_active) {
								$hold[3] = 'All in One SEO Description'; 
							} elseif ($desc_type == '_su_description' && $su_active) {
								$hold[3] = 'Ultimate SEO Description'; 
							}
							$error_count++;
						}
					}
				}
			}
			unset($results[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Seo Desc EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();
	}
	

	function check_seo_titles_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'postmeta';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $words_table WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$posts_table = $wpdb->prefix . 'posts';
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		if ($wpsc_settings[136]->option_value == 'true') { $page_status = true; }
		else { $page_status = false; }
		
		if ($wpsc_settings[137]->option_value == 'true') { $post_status = true; }
		else { $post_status = false; }
		
		$ain_active = is_plugin_active("all-in-one-seo-pack/all_in_one_seo_pack.php");
		$su_active = is_plugin_active("seo-ultimate/seo-ultimate.php");
		$yoast_active = is_plugin_active("wordpress-seo/wp-seo.php");

		$results = SplFixedArray::fromArray($wpdb->get_results('SELECT post_id, meta_value, meta_key FROM ' . $table_name . ' WHERE meta_key="_yoast_wpseo_title" OR meta_key="_aioseop_title" OR meta_key="_su_title" LIMIT ' . $max_pages)); $sql_count++;
		
		
		for($x = 0;$x < $results->getSize();$x++) {
			$desc = $results[$x];
			$post_store = $desc;
			$page_results = $wpdb->get_results('SELECT ID, post_title, post_status FROM ' . $posts_table . ' WHERE ID=' . $desc->post_id);
			
			if ($page_results[0]->post_title == '') continue;
			if ($page_results[0]->post_status == 'draft' && $page_results[0]->post_type == 'page' && !$page_status) continue;
			if ($page_results[0]->post_status == 'draft' && $page_results[0]->post_type != 'page' && !$post_status) continue;
			
			$desc_type = $desc->meta_key;
			$desc = $desc->meta_value;
			
			$desc = wpsc_clean_all($desc, $wpsc_settings);
			
			$words = explode(' ', $desc);

			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = str_replace('–', ' ', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '' && ($yoast_active || $ain_active || $su_active)) {
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $page_results[0]->post_title;
							$hold[2] = $page_results[0]->ID;
							
							if ($desc_type == '_yoast_wpseo_title' && $yoast_active) {
								$hold[3] = 'Yoast SEO Title';
							} elseif ($desc_type == '_aioseop_title' && $ain_active) {
								$hold[3] = 'All in One SEO Title'; 
							} elseif ($desc_type == '_su_title' && $su_active) {
								$hold[3] = 'Ultimate SEO Title'; 
							} else {
								break;
							}
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}
			}
			unset($results[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("SEO Title EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();

	}
	

function check_post_slugs_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$post_table = $wpdb->prefix . 'posts';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $words_table WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$posts_table = $wpdb->prefix . 'posts';
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		

		
			$post_types = get_post_types();
			$post_type_list = "AND (";
			foreach ($post_types as $type) {
				if ($type != 'revision' && $type != 'page' && $type != 'slider' && $type != 'attachment' && $type != 'optionsframework' && $type != 'product' && $type != 'wpsc-product' && $type != 'wpcf7_contact_form' && $type != 'nav_menu_item' && $type != 'gal_display_source' && $type != 'lightbox_library' && $type != 'wpcf7s')
					$post_type_list .= "post_type='$type' OR ";
			}
			$post_type_list = trim($post_type_list, " OR ");
			$post_type_list .= ")";
		
			if ($wpsc_settings[137]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }

			//$posts_list = get_posts(array('posts_per_page' => $max_pages, 'post_type' => $post_type_list, 'post_status' => $post_status));
			$results = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $post_table WHERE post_type = 'post'" . $post_status . $post_type_list));
		$sql_count++;

		for ($x = 0;$x < $results->getSize();$x++) {
			$desc = $results[$x];
			$post_count++;
			$desc_title = $desc->post_title;
			$desc_id = $desc->ID;
			$desc = html_entity_decode(strip_tags($desc->post_name), ENT_QUOTES, 'utf-8');
			$desc = wpsc_clean_text($desc);
			$words = explode('-', $desc);

			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $desc_title;
							$hold[2] = $desc_id;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}
			}
			unset($results[$x]);
		}
		
		$end = round(microtime(true),5);
		//$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Slug     Time: " . round($end - $start,5) . "      SQL: " . $sql_count . "     Memory: " . round(memory_get_usage() / 1000,5) . " KB" );
		//fclose($debug_file);
		
		return $error_list->getSize();
	}
	
	
	function check_page_slugs_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$post_table = $wpdb->prefix . 'posts';
		$table_name = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		$words_table = $wpdb->prefix . 'spellcheck_words';
		$posts_table = $wpdb->prefix . 'posts';
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		
			if ($wpsc_settings[137]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
			else { $post_status = " AND post_status='publish'"; }

			//$posts_list = get_posts(array('posts_per_page' => $max_pages, 'post_type' => $post_type_list, 'post_status' => $post_status));
			$results = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_name, ID FROM $post_table WHERE post_type = 'page'" . $post_status));
		$sql_count++;

		for ($x = 0;$x < $results->getSize();$x++) {
			$desc = $results[$x];
			$post_count++;
			$desc_title = $desc->post_title;
			$desc_id = $desc->ID;
			$desc = html_entity_decode(strip_tags($desc->post_name), ENT_QUOTES, 'utf-8');
			$desc = wpsc_clean_text($desc);
			$words = explode('-', $desc);

			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
								
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $desc_title;
							$hold[2] = $desc_id;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}
			}
			unset($results[$x]);
		}
		
		$end = round(microtime(true),5);
		//$loc = dirname(__FILE__)."/../../../../debug.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Page Slugs     Time: " . round($end - $start,5) . "      SQL: " . $sql_count . "     Memory: " . round(memory_get_usage() / 1000,5) . " KB" );
		//fclose($debug_file);

		return $error_list->getSize();
	}
	

	function check_slider_titles_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$posts_list = SplFixedArray::fromArray(get_posts(array('posts_per_page' => $max_pages, 'post_type' => 'slider', 'post_status' => array('publish', 'draft')))); $sql_count++;

		for($x = 0;$x < $posts_list->getSize();$x++) {
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->post_title), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}
			}
		}
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Slider Title EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();	
	}

	function check_slider_captions_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$posts_list = SplFixedArray::fromArray(get_posts(array('posts_per_page' => $max_pages, 'post_type' => 'slider', 'post_status' => array('publish', 'draft')))); $sql_count++;

		for($x = 0;$x < $posts_list->getSize();$x++) {
			$word_list = get_post_meta ($posts_list[$x]->ID, 'my_slider_caption', true );
			$word_list = html_entity_decode(strip_tags($word_list), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Slider Caption EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		

		return $error_list->getSize();
	}

/* Slider Plugins */

function check_it_slider_titles_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$slider_table = $wpdb->prefix . 'hugeit_slider_slide';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$table_name = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT slider_id, title FROM $slider_table")); $sql_count++;

		for ($x = 0;$x < $posts_list->getSize();$x++) {
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->title), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				//$ignore_check = str_replace("'", "\'", $word);
				//$ignore_word = $wpdb->get_results("SELECT word FROM $words_table WHERE word='" . $ignore_check . "' AND ignore_word = true");
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->title;
							$hold[2] = $posts_list[$x]->slider_id;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
		}
		

		return $error_list->getSize();	
	}

	function check_it_slider_captions_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$slider_table = $wpdb->prefix . 'hugeit_slider_slide';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$table_name = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		set_time_limit(6000); 
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT slider_id, description, title FROM $slider_table"));

		for ($x = 0;$x < $posts_list->getSize();$x++) {
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->description), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				//$ignore_check = str_replace("'", "\'", $word);
				//$ignore_word = $wpdb->get_results("SELECT word FROM $words_table WHERE word='" . $ignore_check . "' AND ignore_word = true");
				if ($wpsc_haystack[strtoupper($word)] != 1) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->title;
							$hold[2] = $posts_list[$x]->slider_id;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
		}
		

		return $error_list->getSize();	
	}

/* Smart Slider 2 */

function check_smart_slider_titles_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'wp_nextend_smartslider_slides';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT slider, title FROM $table_name"));

		for ($x = 0;$x < $posts_list->getSize();$x++) {
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->title), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = $wpdb->get_results("SELECT word FROM $words_table WHERE word='" . $ignore_check . "' AND ignore_word = true");
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
		}
		

		return $error_list->getSize();	
	}

	function check_smart_slider_captions_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'wp_nextend_smartslider_slides';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$words_table = $wpdb->prefix . 'spellcheck_words';
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT slider, description, title FROM $table_name"));

		for ($x = 0;$x < $posts_list->getSize();$x++) {
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->description), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = $wpdb->get_results("SELECT word FROM $words_table WHERE word='" . $ignore_check . "' AND ignore_word = true");
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
		}
		
		
		return $error_list->getSize();	
	}


function check_media_titles_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$sql_count = 0;
		
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$post_table = $wpdb->prefix . 'posts';
		$word_count = 0;
		$total_words = 0;
		$media_count = 0;
		$error_count = 0;
		set_time_limit(6000);
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		
		$posts_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content, post_title, post_excerpt, ID from $post_table WHERE post_type='attachment'")); $sql_count++;
		
		for ($x = 0;$x < $posts_list->getSize();$x++) {
			$media_count++;
			
			//******CHECK MEDIA TITLES******
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->post_title), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
						
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							$hold[3] = 'Media Title';
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			
			//******CHECK MEDIA DESCRIPTION******
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->post_content), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							$hold[3] = 'Media Description';
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			
			//******CHECK MEDIA CAPTION******
			$word_list = html_entity_decode(strip_tags($posts_list[$x]->post_excerpt), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							$hold[3] = 'Media Caption';
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			
			//******CHECK MEDIA ALT TEXT******
			$word_list = html_entity_decode(strip_tags(get_post_meta ($posts_list[$x]->ID, '_wp_attachment_image_alt', true )), ENT_QUOTES, 'utf-8');
			$word_list = wpsc_clean_all($word_list, $wpsc_settings);
			$words = explode(' ', $word_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							
							$hold = new SplFixedArray(4);
							$hold[0] = $word;
							$hold[1] = $posts_list[$x]->post_title;
							$hold[2] = $posts_list[$x]->ID;
							$hold[3] = 'Media Alternate Text';
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
						}
					}
				}	
			}
			unset($posts_list[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Media EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_list->getSize();	
	}	
	
	function check_woocommerce_free($is_running = false, $wpsc_haystack = null, $log_debug = true) {
		$start = round(microtime(true),5);
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		
		$total_posts = $max_pages;

$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";'); $sql_count++;
		

		$posts_list = get_posts(array('posts_per_page' => $max_pages, 'post_type' => 'product', 'post_status' => array('publish', 'draft'))); $sql_count++;

		foreach ($posts_list as $post) {
			array_shift($posts_list);
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$post_count++;
			$words_list = $post->post_content;
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							if ($post_count <= $total_posts) {
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $post->post_title;
							$hold[2] = $post->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
							} else {
								
							}
						}
					}
				}	
			}
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("WooCommerce EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));

		return $error_count;
	}

	function check_woocommerce_coupon_free($is_running = false, $wpsc_haystack = null) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);

		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
$total_posts = $max_pages;
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";'); $sql_count++;
		

		$posts_list = get_posts(array('posts_per_page' => $max_pages, 'post_type' => 'shop_coupon', 'post_status' => array('publish', 'draft'))); $sql_count++;

		foreach ($posts_list as $post) {
			array_shift($posts_list);
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$post_count++;
			$words_list = $post->post_excerpt;
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							if ($post_count <= $total_posts) {
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $post->post_title;
							$hold[2] = $post->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
							} else {
								
							}
						}
					}
				}	
			}
		}
		
		return $error_count;

	}

	function check_woocommerce_excerpt_free($is_running = false, $wpsc_haystack = null) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		

$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		

		$posts_list = get_posts(array('posts_per_page' => $max_pages, 'post_type' => 'product', 'post_status' => array('publish', 'draft')));

		foreach ($posts_list as $post) {
			array_shift($posts_list);
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$post_count++;
			$words_list = $post->post_excerpt;
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);
		
			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							if ($post_count <= $total_posts) {
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $post->post_title;
							$hold[2] = $post->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
							} else {
								
							}
						}
					}
				}	
			}
		}
		

		return $error_count;
	}


	function check_wpecommerce_free($is_running = false, $wpsc_haystack = null) {
		global $scan_delay;
		$sql_count = 0;
		
		global $wpdb;
		global $wpsc_haystack;
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		set_time_limit(6000); 
		ini_set('memory_limit','512M'); //Sets the PHP memory limit
		
		
		$max_pages = intval($wpsc_settings[138]->option_value);	
$dict_list = $wpdb->get_results("SELECT * FROM $dict_table;");
		$ignore_list = $wpdb->get_results("SELECT * FROM $table_name WHERE ignore_word=true;");
		$loc = dirname(__FILE__)."/../../../../debug-var.log";
		//$debug_file = fopen($loc, 'a');
		//$debug_var = fwrite( $debug_file, "Post Content Ignore List: " . sizeof((array)$ignore_list) . "          Dictionary List: " . sizeof((array)$dict_list) . "          Options: " . sizeof((array)$wpsc_settings) . "          Grammar Options: " . sizeof((array)$wpgc_settings) . "\r\n" );
		//$debug_var = fwrite( $debug_file, print_r($wpsc_settings, true) . "\r\n" );
		//fclose($debug_file);
		
		wpsc_set_global_vars();
		global $wpsc_settings;
		
		$loc = dirname(__FILE__) . "/dict/" . $wpsc_settings[11]->option_value . ".pws";
		$contents = file_get_contents($loc);

		$contents = str_replace("\r\n", "\n", $contents);
		$dict_file = explode("\n", $contents);
		
		$wpsc_haystack = wpsc_dictionary_init($dict_file);
		
		
$word_count = 0;
		$error_count = 0;
		$total_words = 0;
		$post_count = 0;
		$word_count = 0;
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); 
			$start_time = time();
		wpsc_set_global_vars();
		}
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);
		error_reporting(0);
		

		$ignore_posts = $wpdb->get_results('SELECT keyword FROM ' . $ignore_table . ' WHERE type="page";');
		

		$posts_list = get_posts(array('posts_per_page' => $max_pages, 'post_type' => 'wpsc-product', 'post_status' => array('publish', 'draft')));

		foreach ($posts_list as $post) {
			array_shift($posts_list);
			$ignore_flag = 'false';
			foreach($ignore_posts as $ignore_check) {
				if (strtoupper(trim($post->post_title)) == strtoupper(trim($ignore_check->keyword))) {
					$ignore_flag = 'true';
				}
			}
			if ($ignore_flag == 'true') { continue; }
			$post_count++;
			$words_list = $post->post_content;
			$words_list = wpsc_clean_all($words_list, $wpsc_settings);
			$words = explode(' ', $words_list);

			foreach($words as $word) {
				$word_count++;
				$total_words++;
				$word = str_replace(' ', '', $word);
				$word = str_replace('=', '', $word);
				$word = str_replace(',', '', $word);
				$word = trim($word, "?!.,'()`”:“@$#-%\=/");
				$word = trim($word, '"');
				$word = trim($word);
				$word = preg_replace("/[0-9]/", "", $word);
				$word = preg_replace("/[^a-zA-z'’`éèùâêîôûçëïü]/i", "", $word);
				if ($word == "") continue;
				$ignore_check = str_replace("'", "\'", $word);
				$ignore_word = false;
				if(isset($ignore_list)) { 
					foreach($ignore_list as $ignore) {
					if (strtoupper($word) == stripslashes(strtoupper($ignore->word))) $ignore_word = true;
				}
					}
				if ($wpsc_haystack[strtoupper($word)] != 1 && !$ignore_word) {	
					$dict_word = str_replace("'", "\'", $word);
					$in_dictionary = false;
					if(isset($dict_list)) { 
					foreach($dict_list as $dict) {
						if (strtoupper($word) == strtoupper($dict->word)) $in_dictionary = true;
					}
					}

					if (!$in_dictionary) {
						
						if (wpsc_ignore_caps($wpsc_settings, $word) && $word != '') {
							if ($post_count <= $total_posts) {
							//$word = addslashes($word);
							
							//Add the error to a new fixed holding array
							$hold = new SplFixedArray(3);
							$hold[0] = $word;
							$hold[1] = $post->post_title;
							$hold[2] = $post->ID;
							
							$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
							$error_list[$error_count] = $hold;
							$error_count++;
							} else {
								
							}
						}
					}
				}	
			}
		}
		
		
		return $error_count;
	}
	
	function check_broken_code_free($rng_seed = 0, $is_running = false, $log_debug = true) {
		$start = round(microtime(true),5);
		$sql_count = 0;
		$page_list = null;
		global $scan_delay;
		global $ent_included;
		global $wpsc_settings;
		if (sizeof((array)$wpsc_settings) < 1) wpsc_set_global_vars();
		//if (!$is_running) sleep($scan_delay);
		
		ini_set('memory_limit','1024M'); //Sets the PHP memory limit
		set_time_limit(600); 
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'spellcheck_html';
		$options_table = $wpdb->prefix . 'spellcheck_options';
		$ignore_table = $wpdb->prefix . 'spellcheck_ignore';
		$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
		$page_table = $wpdb->prefix . 'posts';
		
		$max_pages = intval($wpsc_settings[138]->option_value);

		$total_words = 0;
		$page_count = 0;
		$post_count = 0;
		$word_count = 0;
		$error_count = 0;
		
		wpsc_set_global_vars();
		
		if ($wpsc_settings[136]->option_value == 'true') { $post_status = " AND (post_status='publish' OR post_status='draft')"; }
		else { $post_status = " AND post_status='publish'"; }
		
		$page_list = SplFixedArray::fromArray($wpdb->get_results("SELECT post_content FROM $page_table WHERE (post_type='page' OR post_type='post')$post_status")); $sql_count++;
		
		if ($is_running != true) {
			$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress'));  $sql_count++;
			$start_time = time();
		}
			$ind_start_time = time();
		
		$max_time = ini_get('max_execution_time'); 
		
		$divi_check = wp_get_theme();
		
		global $ignore_list;
		global $dict_list;
		global $wpsc_settings;
		$error_list = new SplFixedArray(1);

		for ($x = 0;$x < $page_list->getSize();$x++) {
			if ($page_list[$x]->post_type == "page" ) { $page_count++; } else { $post_count++; }
			
			//if ($page_list[$x]->ID == 2624) print_r("<code>" . $words_content . "</code>");
			
			$words_content = $page_list[$x]->post_content;
			$words_content = do_shortcode($words_content);
			$words_content = wpsc_content_filter($words_content);
			$words_content = wpbc_clean_all($words_content, $wpsc_settings);
			
			//if ($page_list[$x]->post_title == 'Resources') print_r($words_content);

			if (sizeof((array)$html_errors) != 0) {
				//print_r("<br>" . $page_list[$x]->post_title . " | " . print_r($html_errors));
				foreach($html_errors as $html_error) {
					if ($html_error[0] != '') {
						$hold = new SplFixedArray(1);
						$hold[0] = $html_error[0];
					
						$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
						$error_list[$error_count] = $hold;
						
						$error_count++;
					}
				}
			}
			
			preg_match_all('/\[.*?\]/', $words_content, $shortcode_errors);

			if (sizeof((array)$shortcode_errors) != 0) {
				//print_r("<br>" . $page_list[$x]->post_title . " | " . print_r($shortcode_errors));
				foreach($shortcode_errors as $shortcode_error) {		
					if ($shortcode_error[0] != '' && strpos($shortcode_error[0], 'vc') === false) {
						$hold = new SplFixedArray(1);
						$hold[0] = $shortcode_error[0];
						
						$error_list->setSize($error_list->getSize() + 1); //Increase the size of the main error array by 1
						$error_list[$error_count] = $hold;
						$error_count++;
					}
				}
			}
			unset($page_list[$x]);
		}
		
		$end = round(microtime(true),5);
		if ($log_debug) wpsc_print_debug("Broken Code EPS", round($end - $start,5), $sql_count, round(memory_get_usage() / 1000,5), sizeof((array)$error_list));
		
		return $error_list->getSize();
	}
	
	function wphc_check_scan_progress() {
		global $wpdb;
		global $wpsc_settings;
		
		$scan_in_progress = false;

		if ($wpsc_settings[141]->option_value == "true") $scan_in_progress = true;
		
		return $scan_in_progress;
	}
	
	function wpsc_check_scan_progress() {
		global $wpdb;
		global $wpsc_settings;
		
		$scan_in_progress = false;
		
		for($x = 66; $x <= 86; $x++) {
			if ($wpsc_settings[$x]->option_value == "true") $scan_in_progress = true;
		}
		
		return $scan_in_progress;
	}
	
	function wpsc_check_empty_scan_progress() {
		global $wpdb;
		global $wpsc_settings;
		
		$scan_in_progress = false;
		
		for($x = 87; $x <= 98; $x++) {
			if ($wpsc_settings[$x]->option_value == "true") $scan_in_progress = true;

		}
		
		return $scan_in_progress;
	}
?>