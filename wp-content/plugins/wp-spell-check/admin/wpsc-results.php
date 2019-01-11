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
class sc_table extends WP_List_Table {

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
		set_time_limit(600); 
		global $wpdb;
		global $dict_list;
		global $wpsc_settings;
		global $ent_included;
		$table_name = $wpdb->prefix . 'spellcheck_options';
		$dict_table = $wpdb->prefix . "spellcheck_dictionary";
		$language_setting = $wpsc_settings[11];
		$dict_words = $dict_list;
		
		if ($ent_included) {
			$loc = dirname(__FILE__) . "/../../wp-spell-check-pro/admin/dict/" . $language_setting->option_value . ".pws";
		} else {
			$loc = dirname(__FILE__) . "/dict/" . $language_setting->option_value . ".pws";
		}
		
		$file = fopen($loc, 'r');
		$contents = fread($file,filesize($loc));
		fclose($file);
		
		$word_list = array();
		foreach ($dict_words as $dict_word) {
			array_push($word_list,$dict_word->word);
		}
		
		$my_dictionary = $wpdb->get_results("SELECT * FROM $dict_table;");
		
		foreach($my_dictionary as $dict_word) {
			array_push($word_list,$dict_word->word);
		}
	
		$contents = str_replace("\r\n", "\n", $contents);
		$main_list = explode("\n", $contents);

		$word_list = array_merge($word_list,$main_list);
	
		$suggestions = array();
		$suggestions_holding = array();
		
		$start = round(microtime(true),5);
		$first_word = stripslashes($item['word']);
		foreach ($word_list as $words) {
			if (strlen($words) >= strlen($first_word) - 2 && strlen($words) <= strlen($first_word) + 2) {
				similar_text(strtoupper($first_word),strtoupper($words),$percentage);
				if ($percentage > 85.00) {
					if ($first_word[0] == strtoupper($first_word[0])) { array_push($suggestions_holding,array(ucfirst($words),$percentage));
					} else { array_push($suggestions_holding,array(lcfirst($words), $percentage)); }
				}
			}
		}

		
		for ($x = 0; $x < sizeof((array)$suggestions_holding); $x++ ) {
			$temp = '';
			$temp_per = 0;
			$temp_index = 0;
				for ($y = 0; $y < sizeof((array)$suggestions_holding); $y++ ) {
					if ($suggestions_holding[$y][1] > $temp_per) {
						$temp = $suggestions_holding[$y][0];
						$temp_per = $suggestions_holding[$y][1];
						$temp_index = $y;
					}
				}
			//if ($item['word'] == 'Havent') print_r($x);
			if ($temp != '') {
				array_push($suggestions, $temp);
				$suggestions_holding[$temp_index][1] = 0;
			}
			if (sizeof((array)$suggestions) >= 4) break;
		}
		/*if (sizeof((array)$suggestions) < 4) {
			foreach ($word_list as $words) {
				
				$first_word = stripslashes($item['word']);
				if (gettype($words) == 'string') similar_text(strtoupper($first_word),strtoupper($words),$percentage);
				if ($percentage > 60.00)
					array_push($suggestions,$words);
					
				if (sizeof((array)$suggestions) >= 4) break;
			}
		}*/

		$sorting = '';
		if ($_GET['orderby'] != '') $sorting .= '&orderby=' . $_GET['orderby'];
		if ($_GET['order'] != '') $sorting .= '&order=' . $_GET['order'];
		if ($_GET['paged'] != '') $sorting .= '&paged=' . $_GET['paged'];

		
		if ($item['word'] == "Empty Field") {
			if ($item['page_type'] == 'Page Slug' || $item['page_type'] == 'Post Slug' || $item['page_type'] == 'Tag Slug' || $item['page_type'] == 'Category Slug') {
				$actions = array (
					'Ignore'      			=> sprintf('<input type="checkbox" class="wpsc-ignore-checkbox" name="ignore-word[]" value="' . $item['id'] . '" />Ignore'),
				);
			} else {
				$actions = array (
					'Edit'					=> sprintf('<a href="#" class="wpsc-edit-button" page_type="' . $item['page_type'] . '" id="wpsc-word-' . $item['word'] . '">Edit</a>'),
					'Ignore'      			=> sprintf('<input type="checkbox" class="wpsc-ignore-checkbox" name="ignore-word[]" value="' . $item['id'] . '" />Ignore')
				);
			}
		} else {
			if ($item['page_type'] == 'Page Slug' || $item['page_type'] == 'Post Slug' || $item['page_type'] == 'Tag Slug' || $item['page_type'] == 'Category Slug') {
				$actions = array (
					'Ignore'      			=> sprintf('<input type="checkbox" class="wpsc-ignore-checkbox" name="ignore-word[]" value="' . $item['id'] . '" />Ignore'),
					'Add to Dictionary'		=> sprintf('<input type="checkbox" class="wpsc-add-checkbox" name="add-word[]" value="' . $item['id'] . '" />Add to Dictionary')
				);
			} else {
				$actions = array (
					'Suggested Spelling'	=> sprintf('<a href="#" class="wpsc-suggest-button" suggestions="' . $suggestions[0] . '-' . $suggestions[1] . '-' . $suggestions[2] . '-' . $suggestions[3] . '">Suggested Spelling</a>'),
					'Edit'					=> sprintf('<a href="#" class="wpsc-edit-button" page_type="' . $item['page_type'] . '" id="wpsc-word-' . $item['word'] . '">Edit</a>'),
					'Ignore'      			=> sprintf('<input type="checkbox" class="wpsc-ignore-checkbox" name="ignore-word[]" value="' . $item['id'] . '" />Ignore'),
					'Add to Dictionary'		=> sprintf('<br /><input type="checkbox" class="wpsc-add-checkbox" name="add-word[]" value="' . $item['id'] . '" />Add to Dictionary')
				);
			}
		}
		
		
		return sprintf('%1$s<span style="background-color:#0096ff; float: left; margin: 3px 5px 0 -30px; display: block; width: 12px; height: 12px; border-radius: 16px; opacity: 1.0;"></span>%3$s',
            stripslashes(stripslashes($item['word'])),
            $item['ID'],
            $this->row_actions($actions)
        );
	}
	
	
	function column_page_name($item) {
		$start = round(microtime(true),5);
		$sql_count = 0;
		
		global $wpdb;
		$link = urldecode ( get_permalink( $item['page_id'] ) );
		$handle = curl_init($url);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

		$response = curl_exec($handle);

		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if($httpCode == 404) {
			$output = '';
		} elseif ($item['page_type'] == 'Menu Item') {
			$output = '<a href="/wp-admin/nav-menus.php?action=edit&menu='.$item['page_id'].'" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Contact Form 7') {
			$output = '<a href="admin.php?page=wpcf7&post='.$item['page_id'].'&action=edit" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Post Title' || $item['page_type'] == 'Page Title' || $item['page_type'] == 'Yoast SEO Description' || $item['page_type'] == 'All in One SEO Description' || $item['page_type'] == 'Ultimate SEO Description' || $item['page_type'] == 'SEO Description' || $item['page_type'] == 'Yoast SEO Title' || $item['page_type'] == 'All in One SEO Title' || $item['page_type'] == 'Ultimate SEO Title' || $item['page_type'] == 'SEO Title' || $item['page_type'] == 'Post Slug' || $item['page_type'] == 'Page Slug') {
			$output = '<a href="/wp-admin/post.php?post=' . $item['page_id'] . '&action=edit" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Slider Title' || $item['page_type'] == 'Slider Caption' || $item['page_type'] == 'Smart Slider Title' || $item['page_type'] == 'Smart Slider Caption') {
			$output = '<a href="/wp-admin/post.php?post=' . $item['page_id'] . '&action=edit" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Huge IT Slider Title' || $item['page_type'] == 'Huge IT Slider Caption') {
			$output = '<a href="/wp-admin/admin.php?page=sliders_huge_it_slider&task=edit_cat&id=' . $item['page_id'] . '" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Media Title' || $item['page_type'] == 'Media Description' || $item['page_type'] == 'Media Caption' || $item['page_type'] == 'Media Alternate Text') {
			$output = '<a href="/wp-admin/post.php?post=' . $item['page_id'] . '&action=edit" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Tag Title' || $item['page_type'] == 'Tag Description' || $item['page_type'] == 'Tag Slug') {
			$output = '<a href="/wp-admin/term.php?taxonomy=post_tag&tag_ID=' . $item['page_id'] . '&post_type=post" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif ($item['page_type'] == 'Post Category' || $item['page_type'] == 'Category Description' || $item['page_type'] == 'Category Slug') {
			$output = '<a href="/wp-admin/term.php?taxonomy=category&tag_ID=' . $item['page_id'] . '&post_type=post" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif($item['page_type'] == 'Author Nickname' || $item['page_type'] == 'Author First Name' || $item['page_type'] == 'Author Last Name' || $item['page_type'] == 'Author Biography' || $item['page_type'] == 'Author SEO Title' || $item['page_type'] == 'Author SEO Description' || $item['page_type'] == 'Author twitter' || $item['page_type'] == 'Author facebook') {
			$output = '<a href="/wp-admin/user-edit.php?user_id=' . $item['page_id'] . ' " id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		} elseif($item['page_type'] == "Site Name" || $item['page_type'] == "Site Tagline") {
			$output = '<a href="/wp-admin/options-general.php" target="_blank">View</a>';
		} elseif($item['page_type'] == "Widget Content") {
			$output = '<a href="/wp-admin/widgets.php" id="wpsc-page-name" page="' . $item['page_name'] . '" target="_blank">View</a>';
		} else {
			$output = '<a href="' . $link . '" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		}
		if (($item['page_type'] == "WP eCommerce Product Excerpt" || $item['page_type'] == "WP eCommerce Product Name" || $item['page_type'] == "WooCommerce Product Excerpt" || $item['page_type'] == "WooCommerce Product Name" || $item['page_type'] == "Page Title" || $item['page_type'] == "Post Title" || $item['page_type'] == 'Yoast SEO Page Description' || $item['page_type'] == 'All in One SEO Page Description' || $item['page_type'] == 'Ultimate SEO Page Description' || $item['page_type'] == 'SEO Page Description' || $item['page_type'] == 'Yoast SEO Page Title' || $item['page_type'] == 'All in One SEO Page Title' || $item['page_type'] == 'Ultimate SEO Page Title' || $item['page_type'] == 'SEO Page Title' || $item['page_type'] == 'Yoast SEO Post Description' || $item['page_type'] == 'All in One SEO Post Description' || $item['page_type'] == 'Ultimate SEO Post Description' || $item['page_type'] == 'SEO Post Description' || $item['page_type'] == 'Yoast SEO Post Title' || $item['page_type'] == 'All in One SEO Post Title' || $item['page_type'] == 'Ultimate SEO Post Title' || $item['page_type'] == 'SEO Post Title' || $item['page_type'] == 'Yoast SEO Media Description' || $item['page_type'] == 'All in One SEO Media Description' || $item['page_type'] == 'Ultimate SEO Media Description' || $item['page_type'] == 'SEO Media Description' || $item['page_type'] == 'Yoast SEO Media Title' || $item['page_type'] == 'All in One SEO Media Title' || $item['page_type'] == 'Ultimate SEO Media Title' || $item['page_type'] == 'SEO Media Title') && $item['word'] == "Empty Field") {
			$output = '<a href="/wp-admin/post.php?post=' . $item['page_id'] . '&action=edit" id="wpsc-page-name" page="' . $item['page_id'] . '" target="_blank">View</a>';
		}

		curl_close($handle);
		$actions = array (
			'View'      			=> sprintf($output),
		);
		
		/*$end = round(microtime(true),5);
		$loc = dirname(__FILE__)."/../../../../results.log";
		$debug_file = fopen($loc, 'a');
		$debug_var = fwrite( $debug_file, "Page Name Column     Time: " . round($end - $start,5) . ".      SQL: " . $sql_count . ".     Memory: " . round(memory_get_usage() / 1000,5) . " KB\r\n" );
		fclose($debug_file);*/
		
		
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
	
	function column_count($item) {
		
		$actions = array ();
		
		
		return sprintf('%1$s <span style="color:silver"></span>%3$s',
            $item['count'],
            $item['ID'],
            $this->row_actions($actions)
        );
	}

	
	function get_columns() {
		global $ent_included;
		if ($ent_included) {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'word' => 'Misspelled Words',
				'page_name' => 'Page',
				'page_type' => 'Page Type',
				'count' => 'Count'
			);
		} else {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'word' => 'Misspelled Words',
				'page_name' => 'Page',
				'page_type' => 'Page Type'
			);
		}
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
		$start = round(microtime(true),5);
		error_reporting(0);
		global $wpdb;
		global $ent_included;
		
		$per_page = 20;
		
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		
		
		$table_name = $wpdb->prefix . 'spellcheck_words';
		$dictionary_table = $wpdb->prefix . 'spellcheck_dictionary';
		if ($_GET['s'] != '') {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND word LIKE "%' . $_GET['s'] . '%"', OBJECT); 
		} elseif ($_GET['s-top'] != '') {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND word LIKE "%' . $_GET['s-top'] . '%"', OBJECT); 
		} else {
			if ($ent_included) {
				$results = $wpdb->get_results('SELECT c.id, c.word, c.page_type, c.page_name, c.page_id, c2.cnt FROM ' . $table_name . ' AS c JOIN (SELECT word, COUNT(*) as cnt FROM ' . $table_name . ' GROUP BY word) as c2 ON (c2.word = c.word) WHERE ignore_word is false ORDER BY c2.cnt DESC;', OBJECT);
			} else {
				$results = $wpdb->get_results('SELECT c.id, c.word, c.page_type, c.page_name, c.page_id, c2.cnt FROM ' . $table_name . ' AS c JOIN (SELECT word, COUNT(*) as cnt FROM ' . $table_name . ' GROUP BY word) as c2 ON (c2.word = c.word) WHERE ignore_word is false ORDER BY c.id DESC;', OBJECT);
			}
		}
		
		$end = round(microtime(true),5);
		//echo "Get data: " . ($end - $start) . "<br>";
		$start = round(microtime(true),5);
		
		$counter = $wpdb->get_results('SELECT word, count(*) AS instances FROM ' . $table_name . ' GROUP BY word');
		
		$data = array();
		foreach($results as $word) {
				array_push($data, array('id' => $word->id, 'word' => $word->word, 'page_name' => $word->page_name, 'page_type' => $word->page_type, 'page_url' => $word->page_url, 'page_id' => $word->page_id, 'count' => $word->cnt));
		}
		
		$end = round(microtime(true),5);
		//echo "Get count data: " . ($end - $start) . "<br>";
		$start = round(microtime(true),5);
		
		function usort_reorder($a, $b) {
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'word'; 
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; 
			
			$result = strcmp($a[$orderby], $b[$orderby]); 
			return ($order==='asc') ? $result : -$result;
		}
		function usort_reorder_default($a, $b) {
			/*$orderby = 'count';
			$order = 'desc'; 
			
			$result = strcmp($a[$orderby], $b[$orderby]); 
			return ($order==='asc') ? $result : -$result;*/
			return intval($b['count']) - intval($a['count']);
		}

		if (!empty($_REQUEST['orderby']) && $_REQUEST['orderby'] != 'undefined') {
			usort($data, 'usort_reorder');
		} else {
			if ($ent_included) usort($data, 'usort_reorder_default');
		}
		
		$end = round(microtime(true),5);
		//echo "Handle table sorting: " . ($end - $start) . "<br>";
		$start = round(microtime(true),5);
		
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		$this->items = $data;
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		) );
		
		$end = round(microtime(true),5);
		//echo "Finalize data: " . ($end - $start) . "<br>";
	}

	function prepare_empty_items() {
		error_reporting(0);
		global $wpdb;
		
		$per_page = 20;
		
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		
		$table_name = $wpdb->prefix . 'spellcheck_empty';
		$dictionary_table = $wpdb->prefix . 'spellcheck_dictionary';
		if ($_GET['s'] != '') {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND page_name LIKE "%' . $_GET['s'] . '%"', OBJECT); 
		} elseif($_GET['s-top'] != '') {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false AND page_name LIKE "%' . $_GET['s-top'] . '%"', OBJECT); 
		} else {
			$results = $wpdb->get_results('SELECT id, word, page_name, page_type, page_id FROM ' . $table_name . ' WHERE ignore_word is false', OBJECT);
		}
		$data = array();
		foreach($results as $word) {
			if ($word->word != '') {
				array_push($data, array('id' => $word->id, 'word' => $word->word, 'page_name' => $word->page_name, 'page_type' => $word->page_type, 'page_url' => $word->page_url, 'page_id' => $word->page_id));
			}
		}
		
		function usort_empty_reorder($a, $b) {
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'word'; 
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; 
			
			$result = strcmp($a[$orderby], $b[$orderby]); 
			return ($order==='asc') ? $result : -$result;
		}
		usort($data, 'usort_empty_reorder');
		
		
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

/* Admin Functions */
function ignore_word($ids) {
	global $wpdb;
	global $ent_included;
	$word_list = array();
	$table_name = $wpdb->prefix . 'spellcheck_words';
	$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
	$show_error_ignore = false;
	$show_error_dict = false;
	$word_list[0] = '';
	$added = '';
	$dict_msg = '';
	$ignore_msg = '';
	foreach ($ids as $id) {
		$words = $wpdb->get_results('SELECT word FROM ' . $table_name . ' WHERE id='. $id . ';');
		$word = $words[0]->word;
		$ignore_word = str_replace("'","\'",$word);
		$ignore_word = str_replace("'","\'",$ignore_word);
		$check_word = $wpdb->get_results('SELECT * FROM ' . $table_name . ' WHERE word="' . $ignore_word . '" AND ignore_word = true');
		$check_dict = $wpdb->get_results('SELECT * FROM ' . $dict_table . ' WHERE word="' . $word . '"');
		if (sizeof((array)$check_word) <= 0 && sizeof((array)$check_dict) <= 0) {
			$wpdb->update($table_name, array('ignore_word' => true), array('id' => $id));
			$wpdb->query("DELETE FROM $table_name WHERE id != $id AND word='" . addslashes($word) . "'");
			$added .= stripslashes($word) . ", ";
			
		} else {
			if (sizeof((array)$check_dict) <= 0) {
				$ignore_msg .= stripslashes($word) . ", ";
				$show_error_ignore = true;
			} else {
				$dict_msg .= stripslashes($word) . ", ";
				$show_error_dict = true;
			}
		}
		if ($ent_included) wpsc_print_changelog_dict("ignore list", $word);
	}
	if ($show_error_ignore) {
		$ignore_msg =trim($dict_msg, ", ");
		$word_list[1] = "The following words were already found in the ignore list: " . $ignore_msg;
	}
	if ($show_error_dict) {
		$dict_msg =trim($dict_msg, ", ");
		$word_list[2] = "The following words were already found in the dictionary: " . $dict_msg;
	}
	$added =trim($added, ", ");
	if (strpos($added, ", ") !== false) {
		$word_list[0] = "The following words have been added to ignore list: " . $added;
	} else {
		$word_list[0] = "The following word has been added to ignore list: " . $added;
	}
	return $word_list;
}

function ignore_word_empty($ids) {
	global $wpdb;
	global $ent_included;
	$word_list = array();
	$table_name = $wpdb->prefix . 'spellcheck_empty';
	$dict_table = $wpdb->prefix . 'spellcheck_dictionary';
	$show_error_ignore = false;
	$show_error_dict = false;
	$word_list[0] = '';
	foreach ($ids as $id) {
		$words = $wpdb->get_results('SELECT word FROM ' . $table_name . ' WHERE id='. $id . ';');
		$word = $words[0]->word;
		$ignore_word = str_replace("'","\'",$word);
		$ignore_word = str_replace("'","\'",$ignore_word);
		
		$check_dict = $wpdb->get_results('SELECT * FROM ' . $dict_table . ' WHERE word="' . $word . '"');
		if (sizeof((array)$check_dict) <= 0) {
			$wpdb->update($table_name, array('ignore_word' => true), array('id' => $id));
			
			$word_list[0] .= stripslashes($word) . ", ";
			
		} else {
			if (sizeof((array)$check_dict) <= 0) {
				$word_list[1] .= stripslashes($word) . ", ";
				$show_error_ignore = true;
			} else {
				$word_list[2] .= stripslashes($word) . ", ";
				$show_error_dict = true;
			}
		}
		if ($ent_included) wpsc_print_changelog_dict("ignore list", $word);
	}
	if ($show_error_ignore) {
		$word_list[1] =trim($word_list[1], ", ");
		$word_list[1] = "The following words were already found in the ignore list: " . $word_list[1];
	}
	if ($show_error_dict) {
		$word_list[2] =trim($word_list[2], ", ");
		$word_list[2] = "The following words were already found in the dictionary: " . $word_list[2];
	}
	$word_list[0] =trim($word_list[0], ", ");
	if (strpos($word_list[0], ", ") !== false) {
		$word_list[0] = "The following words have been added to ignore list: " . $word_list[0];
	} else {
		$word_list[0] = "The following word has been added to ignore list: " . $word_list[0];
	}
	return $word_list;
}

function add_to_dictionary($ids) {
	global $wpdb;
	global $ent_included;
	$table_name = $wpdb->prefix . 'spellcheck_words';
	$dictionary_table = $wpdb->prefix . 'spellcheck_dictionary';
	$word_list = array();
	$show_error_ignore = false;
	$show_error_dict = false;
	foreach ($ids as $id) {
		$words = $wpdb->get_results('SELECT word FROM ' . $table_name . ' WHERE id='. $id . ';');
		$word = $words[0]->word;
		$word = str_replace('%28', '(', $word);
		$ignore_word = str_replace("'","\'",$word);
		$ignore_word = str_replace("'","\'",$ignore_word);
		$check = $wpdb->get_results('SELECT * FROM ' . $dictionary_table . ' WHERE word="' . $word . '"'); 
		$ignore_check = $wpdb->get_results('SELECT * FROM ' . $table_name . ' WHERE word="' . $ignore_word . '" AND ignore_word = true');

		if (sizeof((array)$check) < 1 && sizeof((array)$ignore_check) < 1) {
			$wpdb->insert($dictionary_table, array('word' => stripslashes($word))); 

			$wpdb->delete($table_name, array('word' => $word)); 
			$word_list[0] = $word_list[0] . stripslashes($word) . ", ";
			
		} else {
			if (sizeof((array)$check_dict) <= 0) {
				$word_list[1] .= stripslashes($word) . ", ";
				$show_error_ignore = true;
			} else {
				$word_list[2] .= stripslashes($word) . ", ";
				$show_error_dict = true;
			}
		}
		
		if ($ent_included) wpsc_print_changelog_dict("dictionary", $word);
	}
	if ($show_error_ignore) {
		$word_list[1] = trim($word_list[1], ", ");
		$word_list[1] = "The following words were already found in the ignore list: " . $word_list[1];
	}
	if ($show_error_dict) {
		$word_list[2] = trim($word_list[2], ", ");
		$word_list[2] = "The following words were already found in the dictionary: " . $word_list[2];
	}
	$word_list[0] = trim($word_list[0], ", ");
	if (strpos($word_list[0], ", ") !== false) {
		$word_list[0] = "The following words have been added to dictionary: " . $word_list[0];
	} else {
		$word_list[0] = "The following word has been added to dictionary: " . $word_list[0];
	}
	return $word_list;
}

/*
 *
 * When editing words, individual words get updated first then ones checked off to apply to entire site
 * If duplicates are detected in either list, the one which appears first in the results list takes priority
 * If duplicates are between each list, individual updates take priority over entire site changes
 *
*/
function update_word_admin($old_words, $new_words, $page_names, $page_types, $old_word_ids, $mass_edit) {
	global $wpdb;
	global $ent_included;
	$table_name = $wpdb->prefix . 'posts';
	$words_table = $wpdb->prefix . 'spellcheck_words';
	$terms_table = $wpdb->prefix . 'terms';
	$meta_table = $wpdb->prefix . 'postmeta';
	$taxonomy_table = $wpdb->prefix . 'term_taxonomy';
	$user_table = $wpdb->prefix . 'usermeta';
	$dict_table = $wpdb->prefix . "spellcheck_dictionary";
	$word_list = '';
	
	$mass_edit_list = array();
	$mass_edit_list_new = array();
	$mass_edit_words = array();
	$ignore_list = array();
	
	$my_dictionary = $wpdb->get_results("SELECT * FROM $dict_table;");
		
	foreach($my_dictionary as $dict_word) {
			array_push($ignore_list,$dict_word->word);
	}
	
	$my_ignore = $wpdb->get_results("SELECT * FROM $words_table WHERE ignore_word = 1;");
		
	foreach($my_ignore as $dict_word) {
			array_push($ignore_list,$dict_word->word);
	}

for ($x= 0; $x < sizeof((array)$old_words); $x++) {
	$old_words[$x] = str_replace('%28', '(', $old_words[$x]);
	$new_words[$x] = str_replace('%28', '(', $new_words[$x]);
	$old_words[$x] = str_replace('%27', "'", $old_words[$x]);
	$new_words[$x] = str_replace('%27', "'", $new_words[$x]);
	$old_words[$x] = stripslashes(stripslashes($old_words[$x]));
	$new_words[$x] = stripslashes(stripslashes($new_words[$x]));
	
	if (in_array($old_words[$x], $ignore_list)) continue;

	$edit_flag = false;
	if (is_array($mass_edit)) {
	foreach($mass_edit as $edit_id) {
		if ($edit_id == $old_word_ids[$x] && !in_array($old_words[$x], $mass_edit_words)) {
			array_push($mass_edit_list, array('old_word' => $old_words[$x], 'new_word' => $new_words[$x]));
			array_push($mass_edit_words, $old_words[$x]);
			$edit_flag = true;
		} 
	}
	}
	if ($edit_flag) continue;
	$word_id = $old_word_ids[$x];

	if ($page_types[$x] == 'Post Content' || $page_types[$x] == 'Page Content' || $page_types[$x] == 'Media Description' || $page_types[$x] == 'WooCommerce Product' || $page_types[$x] == 'WP eCommerce Product' ) {
		
		$page_result = $wpdb->get_results('SELECT post_content, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$updated_content = preg_replace('#\\b' . $old_words[$x] . '\\b#', $new_words[$x], $page_result[0]->post_content);

		$old_name = $page_result[0]->post_title;

		$wpdb->update($table_name, array('post_content' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Contact Form 7') {
		
		$page_result = $wpdb->get_results('SELECT post_content, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$meta_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id="' . $page_names[$x] . '"');

		
		$updated_content = preg_replace('#\\b' . $old_words[$x] . '\\b#', $new_words[$x], $page_result[0]->post_content);
		$updated_meta = str_replace($old_words[$x], $new_words[$x], $meta_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($table_name, array('post_content' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->update($meta_table, array('meta_value' => $updated_meta), array('post_id' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'WooCommerce Product Excerpt' || $page_types[$x] == 'WP eCommerce Product Excerpt') {
		
		$page_result = $wpdb->get_results('SELECT post_content, post_title, post_excerpt FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');

		
		$updated_content = preg_replace('#\\b' . $old_words[$x] . '\\b#', $new_words[$x], $page_result[0]->post_excerpt);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($table_name, array('post_excerpt' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Menu Item' || $page_types[$x] == 'Post Title' || $page_types[$x] == 'Page Title' || $page_types[$x] == 'Slider Title' || $page_types[$x] == 'Media Title' || $page_types[$x] == 'WP eCommerce Product Name' || $page_types[$x] == 'WooCommerce Product Name') {
		
		$menu_result = $wpdb->get_results('SELECT post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$updated_content = str_replace($old_words[$x], $new_words[$x], $menu_result[0]->post_title);

		$old_name = $menu_result[0]->post_title;
		$wpdb->update($table_name, array('post_title' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->update($words_table, array('page_name' => $updated_content), array('page_name' => $old_name)); //Update the title of the page/post/menu in the spellcheck database
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Author Nickname') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='nickname'");
		$updated_content = str_replace($old_words[$x], $new_words[$x], $author_result[0]->meta_value);
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_result[0]->post_author, 'meta_key' => 'nickname'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Author First Name') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='first_name'");
		$updated_content = str_replace($old_words[$x], $new_words[$x], $author_result[0]->meta_value);
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'first_name'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Author Last Name') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='last_name'");
		$updated_content = str_replace($old_words[$x], $new_words[$x], $author_result[0]->meta_value);
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_result[0]->post_author, 'meta_key' => 'last_name'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Author Biographical Info') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='description'");
		
		$updated_content = preg_replace('#\\b' . $old_words[$x] . '\\b#', $new_words[$x], $author_result[0]->meta_value);
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'description'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Author SEO Title') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='wpseo_title'");
		$updated_content = str_replace($old_words[$x], $new_words[$x], $author_result[0]->meta_value);
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'wpseo_title'));
		$wpdb->delete($words_table, array('word' => $old_words[$x], 'id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author SEO Description') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='wpseo_metadesc'");
		
		$updated_content = preg_replace('#\\b' . $old_words[$x] . '\\b#', $new_words[$x], $author_result[0]->meta_value);
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'wpseo_metadesc'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Site Name') {
		$opt_table = $wpdb->prefix . "options";
	
		$site_result = $wpdb->get_results("SELECT * FROM $opt_table WHERE option_name='blogname'");
		$updated_content = str_replace($old_words[$x], $new_words[$x], $site_result[0]->option_value);
	
		$wpdb->update($opt_table, array('option_value' => $updated_content), array('option_name' => 'blogname'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Site Tagline') {
		$opt_table = $wpdb->prefix . "options";
	
		$site_result = $wpdb->get_results("SELECT * FROM $opt_table WHERE option_name='blogdescription'");
		$updated_content = str_replace($old_words[$x], $new_words[$x], $site_result[0]->option_value);
	
		$wpdb->update($opt_table, array('option_value' => $updated_content), array('option_name' => 'blogdescription'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Slider Caption') {
		
		$menu_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$caption = get_post_meta($menu_result[0]->ID, 'my_slider_caption', true);
		$updated_content = str_replace($old_words[$x], $new_words[$x], $caption);

		update_post_meta($menu_result[0]->ID, 'my_slider_caption', $updated_content);
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Huge IT Slider Caption') {
		
		$it_table = $wpdb->prefix . 'huge_itslider_images';
		$menu_result = $wpdb->get_results('SELECT name, description FROM ' . $it_table . ' WHERE id="' . $page_names[$x] . '"');
		
		$updated_content = str_replace($old_words[$x], $new_words[$x], $menu_result[0]->description);
		
		$wpdb->update($it_table, array('description' => $updated_content), array('id' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Huge IT Slider Title') {
		
		$it_table = $wpdb->prefix . 'huge_itslider_images';
		$menu_result = $wpdb->get_results('SELECT name FROM ' . $it_table . ' WHERE id="' . $page_names[$x] . '"');
		
		$updated_content = str_replace($old_words[$x], $new_words[$x], $menu_result[0]->name);	

		$wpdb->update($it_table, array('name' => $updated_content), array('id' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Smart Slider Caption') {
		
		$slider_table = $wpdb->prefix . 'wp_nextend_smartslider_slides';
		$menu_result = $wpdb->get_results('SELECT description FROM ' . $slider_table . ' WHERE id="' . $page_names[$x] . '"');
		$updated_content = str_replace($old_words[$x], $new_words[$x], $menu_result[0]->description);

		$wpdb->update($slider_table, array('description' => $updated_content), array('id' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Smart Slider Title') {
		
		$slider_table = $wpdb->prefix . 'wp_nextend_smartslider_slides';
		$menu_result = $wpdb->get_results('SELECT title FROM ' . $slider_table . ' WHERE id="' . $page_names[$x] . '"');
		$updated_content = str_replace($old_words[$x], $new_words[$x], $menu_result[0]->title);

		$wpdb->update($slider_table, array('title' => $updated_content), array('id' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Media Alternate Text') {
		
		$menu_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$caption = get_post_meta($menu_result[0]->ID, '_wp_attachment_image_alt', true);
		$updated_content = str_replace($old_words[$x], $new_words[$x], $caption);

		update_post_meta($menu_result[0]->ID, '_wp_attachment_image_alt', $updated_content);
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Media Caption') {
		
		$page_result = $wpdb->get_results('SELECT post_excerpt, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $page_result[0]->post_excerpt);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($table_name, array('post_excerpt' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Tag Title' || $page_types[$x] == 'Category Title') {
		
		$tag_result = $wpdb->get_results('SELECT name FROM ' . $terms_table . ' WHERE term_id=' . $page_names[$x]);

		$updated_content = str_replace($old_words[$x], $new_words[$x], $tag_result[0]->name);

		$wpdb->update($terms_table, array('name' => $updated_content), array('name' => $tag_result[0]->name));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Tag Description') {
		
		$tag_result = $wpdb->get_results('SELECT description FROM ' . $taxonomy_table . ' WHERE term_id=' . $page_names[$x]);

		$updated_content = str_replace($old_words[$x], $new_words[$x], $tag_result[0]->description);

		$wpdb->update($taxonomy_table, array('description' => $updated_content), array('description' => $tag_result[0]->description));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Category Description') {
		
		$tag_result = $wpdb->get_results('SELECT description FROM ' . $taxonomy_table . ' WHERE term_id=' . $page_names[$x]);

		$updated_content = str_replace($old_words[$x], $new_words[$x], $tag_result[0]->description);

		$wpdb->update($taxonomy_table, array('description' => $updated_content), array('description' => $tag_result[0]->description));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Post Custom Field') {
		
		$page_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_value LIKE "%' . $old_words[$x] . '%"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Yoast SEO Description') {
		
		$page_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_key="_yoast_wpseo_metadesc"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID, 'meta_key' => '_yoast_wpseo_metadesc'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'All in One SEO Description') {
		
		$page_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_key="_aioseop_description"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID, 'meta_key' => '_aioseop_description'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Ultimate SEO Description') {
		
		$page_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_key="_su_description"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID, 'meta_key' => '_su_description'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Yoast SEO Title') {
		
		$page_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_key="_yoast_wpseo_title"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID, 'meta_key' => '_yoast_wpseo_title'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'All in One SEO Title') {
		$page_result = $wpdb->get_results('SELECT ID FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_key="_aioseop_title"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID, 'meta_key' => '_aioseop_title'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Ultimate SEO Title') {
		$page_result = $wpdb->get_results('SELECT ID FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$desc_result = $wpdb->get_results('SELECT meta_value FROM ' . $meta_table . ' WHERE post_id=' . $page_result[0]->ID . ' AND meta_key="_su_title"');

		$updated_content = str_replace($old_words[$x], $new_words[$x], $desc_result[0]->meta_value);

		$old_name = $page_result[0]->post_title;
		$wpdb->update($meta_table, array('meta_value' => $updated_content), array('post_id' => $page_result[0]->ID, 'meta_key' => '_su_title'));
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	} elseif ($page_types[$x] == 'Widget Content') {
		$widget_instances = get_option('widget_text');
		
		foreach (array_keys($widget_instances) as $index) {
			if ($widget_instances[$index]['title'] == $page_names[$x]) {
				$widget_instances[$index]['text'] = str_replace($old_words[$x], $new_words[$x], $widget_instances[$index]['text']);
			}
		}
		
		update_option('widget_text',$widget_instances);
		$wpdb->query("DELETE FROM $words_table WHERE id=$word_id");
	}
	

	
	$page_url = get_permalink( $page_names[$x] );
	$page_title = get_the_title( $page_names[$x] );
	$word_list .= $old_words[$x] . " to " . $new_words[$x] . ", ";
	
	$url = wpsc_construct_url($page_types[$x], $page_names[$x]);
	if ($ent_included) wpsc_print_changelog($old_words[$x], $new_words[$x], $page_types[$x], $url);
	
	}
	
	$return_message = "";
	if ($ent_included) {
		$url = plugins_url()."/wp-spell-check-pro/admin/changes.php";
		$view_link = "<a target='_blank' href='$url'>Click here</a> to view the changelog";
	} else {

		$view_link = "<span style='color: grey;'>Click here to view the changelog</a><span class='wpsc-mouseover-button-change' style='border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;'>?</span><span class='wpsc-mouseover-text-change'>To view the changelog, you must <a href='https://www.wpspellcheck.com/features/' target='_blank'>Upgrade to Pro</a></span>";
	}
	if (sizeof((array)$mass_edit_list) > 0) {
		$return_message = wpsc_mass_edit($mass_edit_list);
		$return_message .= "<br />";
	}
	
	$word_list =trim($word_list, ", ");
	//echo "Word List: |" . $word_list . "|";
	if (strpos($word_list, ", ") !== false) {
		return $return_message . "The following words have been updated: " . $word_list . "<br>" . $view_link;
	} else {
		if ($word_list != '') {
			return $return_message . "The following word has been updated: " . $word_list . "<br>" . $view_link;
		} else {
			return $return_message . $view_link;
		}
	}
}

function wpsc_mass_edit($to_update) {
	global $wpdb;
	global $ent_included;
	$posts_table = $wpdb->prefix . 'posts';
	$terms_table = $wpdb->prefix . 'terms';
	$meta_table = $wpdb->prefix . 'postmeta';
	$taxonomy_table = $wpdb->prefix . 'term_taxonomy';
	$user_table = $wpdb->prefix . 'usermeta';
	$words_table = $wpdb->prefix . 'spellcheck_words';
	$options_table = $wpdb->prefix . 'options';
	$it_table = $wpdb->prefix . 'hugeit_slider_slide';
	$slider_table = $wpdb->prefix . 'wp_nextend_smartslider_slides';
	
	wpsc_set_global_vars();
	global $wpsc_settings;
	
	$results = $wpdb->get_results("SELECT * FROM $words_table WHERE ignore_word != 1");
	
	foreach($to_update as $update) {
		foreach ($results as $result) {
			if ($update['old_word'] === stripslashes($result->word)) {
				$to_update = str_replace("'", "('|`|’)", $update['old_word']);
				if ($result->page_type == "Post Content" || $result->page_type ==  "Page Content" || $result->page_type == "Media Description" || $result->page_type == "WooCommerce Product" || $result->page_type == "WP eCommerce Product") {
					$content = $wpdb->get_results("SELECT post_content FROM $posts_table WHERE id=" . $result->page_id)[0]->post_content;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($posts_table, array("post_content" => $content), array("ID" => $result->page_id));
				} elseif ($result->page_type == "Contact Form 7" ) {
					$content = $wpdb->get_results("SELECT post_content FROM $posts_table WHERE id=" . $result->page_id)[0]->post_content;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($posts_table, array("post_content" => $content), array("ID" => $result->page_id));
					
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id)[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("ID" => $result->page_id));
				} elseif ($result->page_type == "WooCommerce Excerpt") {
					$content = $wpdb->get_results("SELECT post_excerpt FROM $posts_table WHERE id=" . $result->page_id)[0]->post_excerpt;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($posts_table, array("post_excerpt" => $content), array("ID" => $result->page_id));
				} elseif ($page_types[$x] == 'Menu Item' || $result->page_type == 'Post Title' || $result->page_type == 'Page Title' || $result->page_type == 'Slider Title' || $result->page_type == 'Media Title') {
					$content = $wpdb->get_results("SELECT post_title FROM $posts_table WHERE id=" . $result->page_id)[0]->post_title;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($posts_table, array("post_title" => $content), array("ID" => $result->page_id));
				} elseif ($result->page_type == "Author Nickname") {
					$content = $wpdb->get_results("SELECT meta_value FROM $user_table WHERE user_id=" . $result->page_id . " AND meta_key='nickname'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($user_table, array("meta_value" => $content), array("user_id" => $result->page_id, "meta_key" => "nickname"));
				} elseif ($result->page_type == "Author First Name") {
					$content = $wpdb->get_results("SELECT meta_value FROM $user_table WHERE user_id=" . $result->page_id . " AND meta_key='first_name'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($user_table, array("meta_value" => $content), array("user_id" => $result->page_id, "meta_key" => "first_name"));
				} elseif ($result->page_type == "Author Last Name") {
					$content = $wpdb->get_results("SELECT meta_value FROM $user_table WHERE user_id=" . $result->page_id . " AND meta_key='last_name'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($user_table, array("meta_value" => $content), array("user_id" => $result->page_id, "meta_key" => "last_name"));
				} elseif ($result->page_type == "Author Biographical Info") {
					$content = $wpdb->get_results("SELECT meta_value FROM $user_table WHERE user_id=" . $result->page_id . " AND meta_key='description'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($user_table, array("meta_value" => $content), array("user_id" => $result->page_id, "meta_key" => "description"));
				} elseif ($result->page_type == "Site Name") {
					$content = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='blogname'")[0]->option_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($user_table, array("option_value" => $content), array("option_name" => 'blogname'));
				} elseif ($result->page_type == "Site Tagline") {
					$content = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='blogdescription'")[0]->option_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($user_table, array("option_value" => $content), array("option_name" => 'blogdescription'));
				} elseif ($result->page_type == "Slider Caption") {
					$content = get_post_meta($result->page_id, "my_slider_caption", true);
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					update_post_meta($result->page_id, 'my_slider_caption', $content);
				} elseif ($result->page_type == "IT Slider Caption") {
					$content = $wpdb->get_results("SELECT description FROM $it_table WHERE slider_id=" . $result->page_id)[0]->description;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($it_table, array("description" => $content), array("slider_id" => $result->page_id));
				} elseif ($result->page_type == "IT Slider Title") {
					$content = $wpdb->get_results("SELECT title FROM $it_table WHERE slider_id=" . $result->page_id)[0]->title;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($it_table, array("title" => $content), array("slider_id" => $result->page_id));
				} elseif ($result->page_type == "Smart Slider Caption") {
					$content = $wpdb->get_results("SELECT description FROM $slider_table WHERE id=" . $result->page_id)[0]->description;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($slider_table, array("description" => $content), array("id" => $result->page_id));
				} elseif ($result->page_type == "Smart Slider Title") {
					$content = $wpdb->get_results("SELECT name FROM $it_table WHERE id=" . $result->page_id)[0]->name;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($slider_table, array("name" => $content), array("id" => $result->page_id));
				} elseif ($result->page_type == "Media Alternate Text") {
					$content = get_post_meta($result->page_id, "_wp_attachment_image_alt", true);
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					update_post_meta($result->page_id, '_wp_attachment_image_alt', $content);
				} elseif ($result->page_type == "Media Caption") {
					$content = $wpdb->get_results("SELECT post_excerpt FROM $posts_table WHERE id=" . $result->page_id)[0]->post_excerpt;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($posts_table, array("post_excerpt" => $content), array("id" => $result->page_id));
				} elseif ($result->page_type == "Tag Title" || $result->page_type == "Category Title") {
					$content = $wpdb->get_results("SELECT name FROM $terms_table WHERE term_id=" . $result->page_id)[0]->name;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($terms_table, array("name" => $content), array("term_id" => $result->page_id));
				} elseif ($result->page_type == "Tag Description" || $result->page_type == "Category Description") {
					$content = $wpdb->get_results("SELECT description FROM $taxonomy_table WHERE term_id=" . $result->page_id)[0]->description;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($taxonomy_table, array("description" => $content), array("term_id" => $result->page_id));
				} elseif ($result->page_type == "Tag Title" || $result->page_type == "Category Title") {
					$content = $wpdb->get_results("SELECT name FROM $terms_table WHERE term_id=" . $result->page_id)[0]->name;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($terms_table, array("name" => $content), array("id" => $result->page_id));
				} elseif ($result->page_type == "Yoast SEO Description") {
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id . " AND meta_key = '_yoast_wpseo_metadesc'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("post_id" => $result->page_id, 'meta_key' => '_yoast_wpseo_metadesc'));
				} elseif ($result->page_type == "All in One SEO Description") {
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id . " AND meta_key = '_aioseop_description'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("post_id" => $result->page_id, 'meta_key' => '_aioseop_description'));
				} elseif ($result->page_type == "Ultimate SEO Description") {
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id . " AND meta_key = '_su_description'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("post_id" => $result->page_id, 'meta_key' => '_su_description'));
				} elseif ($result->page_type == "Yoast SEO Title") {
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id . " AND meta_key = '_yoast_wpseo_title'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("post_id" => $result->page_id, 'meta_key' => '_yoast_wpseo_title'));
				} elseif ($result->page_type == "All in One SEO Title") {
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id . " AND meta_key = '_aioseop_title'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("post_id" => $result->page_id, 'meta_key' => '_aioseop_title'));
				} elseif ($result->page_type == "Ultimate SEO Title") {
					$content = $wpdb->get_results("SELECT meta_value FROM $meta_table WHERE post_id=" . $result->page_id . " AND meta_key = '_su_title'")[0]->meta_value;
					$content = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$content);
					$wpdb->update($meta_table, array("meta_value" => $content), array("post_id" => $result->page_id, 'meta_key' => '_su_title'));
				}  elseif ($result->page_type == 'Widget Content') {
					$widget_instances = get_option('widget_text');
					
					foreach (array_keys($widget_instances) as $index) {
						if ($widget_instances[$index]['title'] == $result->page_name) {
							$widget_instances[$index]['text'] = preg_replace("/(?<![A-Za-z0-9])" . $to_update . "(?![A-Za-z0-9])/",$update['new_word'],$widget_instances[$index]['text']);
						}
					}
					
					update_option('widget_text',$widget_instances);
				}
				$wpdb->delete($words_table, array("id" => $result->id));
				
				$url = wpsc_construct_url($result->page_type, $result->page_id);
				if ($ent_included) wpsc_print_changelog($update['old_word'], $update['new_word'], $result->page_type, $url);
			}
		}
		$word_list .= $update['old_word'] . " to " . $update['new_word'] . ", ";
	}
	
	$word_list =trim($word_list, ", ");
	if (strpos($word_list, ", ") !== false) {
		return "The following words have been updated on the entire site: " . $word_list;
	} else {
		return "The following word has been updated on the entire site: " . $word_list;
	}
}

function update_empty_admin($new_words, $page_names, $page_types, $old_word_ids) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'posts';
	$words_table = $wpdb->prefix . 'spellcheck_empty';
	$terms_table = $wpdb->prefix . 'terms';
	$meta_table = $wpdb->prefix . 'postmeta';
	$taxonomy_table = $wpdb->prefix . 'term_taxonomy';
	$user_table = $wpdb->prefix . 'usermeta';
	$word_list = '';
	$seo_error = false;

for ($x= 0; $x < sizeof((array)$new_words); $x++) {
	$new_words[$x] = str_replace('%28', '(', $new_words[$x]);
	$new_words[$x] = str_replace('%27', "'", $new_words[$x]);
	$new_words[$x] = stripslashes($new_words[$x]);
	
	if ($page_types[$x] == 'Media Description') {
		
		$page_result = $wpdb->get_results('SELECT post_content FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');

		$updated_content = $new_words[$x];

		$wpdb->update($table_name, array('post_content' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'WooCommerce Product Excerpt' || $page_types[$x] == 'WP eCommerce Product Excerpt') {
		
		$page_result = $wpdb->get_results('SELECT post_content, post_title, post_excerpt FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');

		$updated_content = $new_words[$x];

		$old_name = $page_result[0]->post_title;
		$wpdb->update($table_name, array('post_excerpt' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Menu Item' || $page_types[$x] == 'Post Title' || $page_types[$x] == 'Page Title' || $page_types[$x] == 'Slider Title' || $page_types[$x] == 'WP eCommerce Product Name' || $page_types[$x] == 'WooCommerce Product Name') {
		
		$menu_result = $wpdb->get_results('SELECT post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$updated_content = $new_words[$x];

		$wpdb->update($table_name, array('post_title' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->update($words_table, array('page_name' => $updated_content), array('id' => $old_word_ids[$x])); //Update the title of the page/post/menu in the spellcheck database
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author Nickname') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='nickname'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'nickname'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author First Name') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='first_name'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'first_name'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author Last Name') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='last_name'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'last_name'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author Biographical Information') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='description'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'description'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author twitter') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='twitter'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'twitter'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author googleplus') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='googleplus'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'googleplus'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author facebook') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='facebook'");
		$updated_content = $new_words[$x];
	
		$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'facebook'));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	}  elseif ($page_types[$x] == 'Author SEO Title') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='wpseo_title'");
		$updated_content = $new_words[$x];
		
		if (sizeof((array)$author_result) <= 0) {
			$wpdb->insert($user_table, array('meta_value' => $updated_content, 'meta_key' => 'wpseo_title', 'user_id' => $page_names[$x]));
		} else {
			$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_result[0]->post_author, 'meta_key' => 'wpseo_title'));
		}
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Author SEO Description') {
		$author_result = $wpdb->get_results("SELECT * FROM $user_table WHERE user_id=" . $page_names[$x] . " AND meta_key='wpseo_metadesc'");
		$updated_content = $new_words[$x];
	
		if (sizeof((array)$author_result) <= 0) {
			$wpdb->insert($user_table, array('meta_value' => $updated_content, 'meta_key' => 'wpseo_metadesc', 'user_id' => $page_result[0]->post_author));
		} else {
			$wpdb->update($user_table, array('meta_value' => $updated_content), array('user_id' => $page_names[$x], 'meta_key' => 'wpseo_metadesc'));
		}
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Media Alternate Text') {
		
		$menu_result = $wpdb->get_results('SELECT ID, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');
		$caption = get_post_meta($menu_result[0]->ID, '_wp_attachment_image_alt', true);
		$updated_content = $new_words[$x];

		update_post_meta($menu_result[0]->ID, '_wp_attachment_image_alt', $updated_content);
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Media Caption') {
		
		$page_result = $wpdb->get_results('SELECT post_excerpt, post_title FROM ' . $table_name . ' WHERE ID="' . $page_names[$x] . '"');

		$updated_content = $new_words[$x];

		$wpdb->update($table_name, array('post_excerpt' => $updated_content), array('ID' => $page_names[$x]));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Tag Description') {
		
		$tag_result = $wpdb->get_results('SELECT description FROM ' . $taxonomy_table . ' WHERE term_id=' . $page_names[$x]);

		$updated_content = $new_words[$x];

		$wpdb->update($taxonomy_table, array('description' => $updated_content), array('term_id' => $page_names[$x]));
		$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
	} elseif ($page_types[$x] == 'Category Description') {
		
		$tag_result = $wpdb->get_results('SELECT description FROM ' . $taxonomy_table . ' WHERE term_id=' . $page_names[$x]);

		$updated_content = $new_words[$x];

		$wpdb->update($taxonomy_table, array('description' => $updated_content), array('term_id' => $page_names[$x]));
		$wpdb->delete($words_table, array('word' => $old_words[$x])); 
	} elseif ($page_types[$x] == 'SEO Page Title' || $page_types[$x] == 'SEO Post Title' || $page_types[$x] == 'SEO Media Title') {
		if (is_plugin_active('wordpress-seo/wp-seo.php')) {
			
			$wpdb->insert($meta_table, array('post_id' => $page_names[$x], 'meta_key' => "_yoast_wpseo_title", 
			'meta_value' => $new_words[$x]));
			
			$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
		} elseif (is_plugin_active('seo-ultimate/seo-ultimate.php')) {
			$wpdb->insert($meta_table, array('post_id' => $page_names[$x], 'meta_key' => "_su_title", 
			'meta_value' => $new_words[$x]));
			
			$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
		} elseif (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')) {
			$wpdb->insert($meta_table, array('post_id' => $page_names[$x], 'meta_key' => "_aioseop_title", 
			'meta_value' => $new_words[$x]));
			
			$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
		} else {
			$seo_error = true;
		}
	} elseif ($page_types[$x] == 'SEO Page Description' || $page_types[$x] == 'SEO Post Description' || $page_types[$x] == 'SEO Media Description') {
		if (is_plugin_active('wordpress-seo/wp-seo.php')) {
			
			$wpdb->insert($meta_table, array('post_id' => $page_names[$x], 'meta_key' => "_yoast_wpseo_metadesc", 
			'meta_value' => $new_words[$x]));
			
			$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
		} elseif (is_plugin_active('seo-ultimate/seo-ultimate.php')) {
			$wpdb->insert($meta_table, array('post_id' => $page_names[$x], 'meta_key' => "_su_description", 
			'meta_value' => $new_words[$x]));
			
			$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
		} elseif (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')) {
			$wpdb->insert($meta_table, array('post_id' => $page_names[$x], 'meta_key' => "_aioseop_description", 
			'meta_value' => $new_words[$x]));
			
			$wpdb->delete($words_table, array('id' => $old_word_ids[$x])); 
		} else {
			$seo_error = true;
		}
	}
	

	
	$page_url = get_permalink( $page_names[$x] );
	$page_title = get_the_title( $page_names[$x] );
	$current_time = date( 'l F d, g:i a' );
	//$loc = dirname(__FILE__) . "/spellcheck.debug";
	//$debug_file = fopen($loc, 'a');
	//$debug_var = fwrite( $debug_file, " Empty Field | New Word: " . $new_words[$x] . " | Type: " . $page_types[$x] . " | Page Name: " . $page_title . " | Page URL: " . $page_url . " | Timestamp: " . $current_time . "\r\n\r\n" );
	//fclose($debug_file);
	}
	
	$message = "";
	if ($seo_error) $message = "<div style='color: #FF0000'>SEO fields could not be updated because no active SEO plugin could be detected</div>";
	return "Empty Fields have been updated" . $message;
}

function wpsc_admin_render() {

	$log_debug = true; //Enables debugging log

	$start = round(microtime(true),5);
	ini_set('memory_limit','8192M'); 
	set_time_limit(600); 
	global $wpdb;
	global $ent_included;
	global $pro_included;
	global $base_page_max;
	$table_name = $wpdb->prefix . "spellcheck_words";
	$empty_table = $wpdb->prefix . "spellcheck_empty";
	$options_table = $wpdb->prefix . "spellcheck_options";
	$post_table = $wpdb->prefix . "posts";
	$estimated_time = 6;
	
	$sql_count = 0;
	$total_smartslider = 0;
	$total_huge_it = 0;
	
	$settings = $wpdb->get_results('SELECT option_name, option_value FROM ' . $options_table); $sql_count++;

	$max_pages = intval($settings[138]->option_value);
	
	if (!$ent_included) $max_pages = $base_page_max;
	
	$message = '';
	
	if (isset($_GET['submit'])) {
	if ($_GET['submit'] == "Stop Scans") {
		$message = "All current spell check scans have been stopped.";
		wpsc_clear_scan();
	}
	}
	if (isset($_GET['submit-empty'])) {
	if ($_GET['submit-empty'] == "Stop Scans") {
		$message = "All current empty field scans have been stopped.";
		wpsc_clear_empty_scan();
	}
	}

	if ($settings[4]->option_value || $settings[12]->option_value || $settings[18]->option_value) {
		$check_pages = 'true';
	} else {
		$check_pages = 'false';
	}
	if ($settings[5]->option_value || $settings[13]->option_value || $settings[19]->option_value) {
		$check_posts = "true";
	} else {
		$check_posts = "false";
	}
	$check_menus = $settings[7]->option_value;
	$page_titles = $settings[12]->option_value;
	$post_titles = $settings[13]->option_value;
	$tags = $settings[14]->option_value;
	$categories = $settings[15]->option_value;
	$seo_desc = $settings[16]->option_value;
	$seo_titles = $settings[17]->option_value;
	$page_slugs = $settings[18]->option_value;
	$post_slugs = $settings[19]->option_value;
	$check_sliders = $settings[30]->option_value;
	$check_media = $settings[31]->option_value;
	$check_ecommerce = $settings[36]->option_value;
	$check_cf7 = $settings[37]->option_value;
	$check_tag_desc = $settings[38]->option_value;
	$check_tag_slug = $settings[39]->option_value;
	$check_cat_desc = $settings[40]->option_value;
	$check_cat_slug = $settings[41]->option_value;
	$check_custom = $settings[42]->option_value;
	$check_authors = $settings[44]->option_value;
	$check_authors_empty = $settings[46]->option_value;
	$check_authors_empty = $settings[47]->option_value;
	$check_menu_empty = $settings[48]->option_value;
	$check_page_titles_empty = $settings[49]->option_value;
	$check_post_titles_empty = $settings[50]->option_value;
	$check_tag_desc_empty = $settings[51]->option_value;
	$check_cat_desc_empty = $settings[52]->option_value;
	$check_page_seo_empty = $settings[53]->option_value;
	$check_post_seo_empty = $settings[54]->option_value;
	$check_media_seo_empty = $settings[55]->option_value;
	$check_media_empty = $settings[56]->option_value;
	$check_ecommerce_empty = $settings[57]->option_value;
	$check_widgets = $settings[148]->option_value;
	
	$postmeta_table = $wpdb->prefix . "postmeta";
	$post_table = $wpdb->prefix . "posts";
	$it_table = $wpdb->prefix . "huge_itslider_images";
	$smartslider_table = $wpdb->prefix . "nextend_smartslider_slides";
	
	
	
	$total_pages = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type = 'page'"); $sql_count++;
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type = 'post'"); $sql_count++;
	$total_media = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type = 'attachment'"); $sql_count++;
	
	$post_count = $total_pages;
	$page_count = $total_posts;
	$media_count = $total_media;
	
	$end = round(microtime(true),5);
	//echo "Set up Variables: " . ($end - $start) . "<br>";
	$start = round(microtime(true),5);
	
	if (isset($_GET['action'])) {
		if ($_GET['action'] == 'check') {
			
			
			
			$total_products = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='product' AND (post_status='draft' OR post_status='publish')");
			$total_cf7 = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='wpcf7_contact_form' AND (post_status='draft' OR post_status='publish')");
			$total_menu = $wpdb->get_var("SELECT COUNT(*) FROM $post_table WHERE post_type='nav_menu_item' AND (post_status='draft' OR post_status='publish')");
			$total_authors = sizeof((array)$wpdb->get_results("SELECT * FROM $post_table GROUP BY post_author")); $sql_count++;
			$total_tags = sizeof(get_tags()); $sql_count++;
			$total_tag_desc = $total_tags;
			$total_tag_slug = $total_tags;
			$total_cat = sizeof(get_categories()); $sql_count++;
			$total_cat_desc = $total_cat;
			$total_cat_slug = $total_cat;
			$total_seo_title = sizeof((array)$wpdb->get_results("SELECT * FROM $postmeta_table WHERE meta_key='_yoast_wpseo_title' OR meta_key='_aioseop_title' OR meta_key='_su_title'")); $sql_count++;
			$total_seo_desc = sizeof((array)$wpdb->get_results("SELECT * FROM $postmeta_table WHERE meta_key='_yoast_wpseo_metadesc' OR meta_key='_aioseop_description' OR meta_key='_su_description'")); $sql_count++;
			
			
			
			
			
			
			
			$total_generic_slider = get_pages(array('number' => PHP_INT_MAX, 'hierarchical' => 0, 'post_type' => 'slider', 'post_status' => array('publish', 'draft'))); $sql_count++;
			$total_sliders = $total_huge_it + $total_smartslider + sizeof((array)$total_generic_slider);
			
			$total_other = $total_menu + $total_authors + $total_tags + $total_tag_desc + $total_tag_slug + $total_cat + $total_cat_desc + $total_cat_slug + $total_seo_title + $total_seo_desc;
			
			$total_page_slugs = $total_pages; 
			$total_post_slugs = $total_posts; 
			$total_page_title = $total_pages; 
			$total_post_title = $total_posts; 
			
			$estimated_time = intval((($total_pages + $total_posts) / 3.5) + 3);
	}
	}
	$scan_message = '';
	
	$check_scan = wpsc_check_scan_progress();
	
	if ($check_scan) {
	if ($check_scan && $_GET['wpsc-script'] != 'noscript') {
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		sleep(1);
	}
	}
	
	
	
	
	$estimated_time = time_elapsed($estimated_time);

	if (isset($_GET['action'])) {
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Pages') {
		$estimated_time = 5 + intval($total_pages / 3.5);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Page Content</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		clear_results();
		$rng_seed = rand(0,999999999);
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'page_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Page Content'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckpages_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckpages', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Posts') {
		$estimated_time = 5 + intval($total_posts / 3.5);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Post Content</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'post_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Post Content'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckposts_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckposts', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Authors') {
		$estimated_time = 5 + intval($total_authors / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Authors</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'author_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Authors'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		wp_schedule_single_event(time(), 'admincheckauthors', array ($rng_seed));
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Menus') {
		$estimated_time = 5 + intval($total_menu / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Menus</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'menu_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Menus'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckmenus_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckmenus', array ($rng_seed ));
		}
	}
	/*if ($_GET['action'] == 'check' && $_GET['submit'] == 'Page Titles') {
		$estimated_time = 5 + intval($total_page_title / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Page Titles</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'page_title_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Page Titles'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckpagetitles_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckpagetitles', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Post Titles') {
		$estimated_time = 5 + intval($total_post_title / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Post Titles</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'post_title_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Post Titles'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckposttitles_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckposttitles', array ($rng_seed ));
		}
	}*/
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Tags') {
		$estimated_time = 5 + intval($total_tags / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Tags</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'tag_title_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Tag Titles'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckposttags_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckposttags', array ($rng_seed ));
		}
	}
	/*if ($_GET['action'] == 'check' && $_GET['submit'] == 'Tag Descriptions') {
		$estimated_time = 5 + intval($total_tag_desc / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Tag Descriptions</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'tag_desc_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Tag Descriptions'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckposttagsdesc_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckposttagsdesc', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Tag Slugs') {
		$estimated_time = 5 + intval($total_tag_slug / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Tag Slugs</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'tag_slug_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Tag Slugs'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckposttagsslugs_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckposttagsslugs', array ($rng_seed ));
		}
	}*/
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Categories') {
		$estimated_time = 5 + intval($total_cat / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Categories</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'cat_title_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Category Titles'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckcategories_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckcategories', array ($rng_seed ));
		}
	}
	/*if ($_GET['action'] == 'check' && $_GET['submit'] == 'Category Descriptions') {
		$estimated_time = 5 + intval($total_cat_desc / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Category Descriptions</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'cat_desc_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Category Descriptions'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckcategoriesdesc_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckcategoriesdesc', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Category Slugs') {
		$estimated_time = 5 + intval($total_cat_slug / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Category Slugs</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'cat_slug_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Category Slugs'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckcategoriesslugs_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckcategoriesslugs', array ($rng_seed ));
		}
	}*/
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'SEO Descriptions') {
		$estimated_time = 5 + intval($total_seo_desc / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">SEO Descriptions</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'seo_desc_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'SEO Descriptions'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckseodesc_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckseodesc', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'SEO Titles') {
		$estimated_time = 5 + intval($total_seo_title / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">SEO Titles</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'seo_title_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'SEO Titles'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckseotitles_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckseotitles', array ($rng_seed ));
		}
	}
	/*if ($_GET['action'] == 'check' && $_GET['submit'] == 'Page Slugs') {
		$estimated_time = 5 + intval($total_page_slugs / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Page Slugs</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'page_slug_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Page Slugs'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckpageslugs_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckpageslugs', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Post Slugs') {
		$estimated_time = 5 + intval($total_post_slugs / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Post Slugs</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'post_slug_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Post Slugs'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckpostslugs_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckpostslugs', array ($rng_seed ));
		}
	}*/
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Sliders') {
		$estimated_time = 5 + intval($total_sliders / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Sliders</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'slider_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Sliders'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'adminchecksliders_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'adminchecksliders_pro', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Media Files') {
		$estimated_time = 5 + intval($total_media / 3.5);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Media Files</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'media_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Media Files'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckmedia_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckmedia_pro', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'WooCommerce and WP-eCommerce Products') {
		$estimated_time = 5 + intval($total_products / 3.5);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">eCommerce Products</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'ecommerce_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'eCommerce Products'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'admincheckecommerce_ent', array ($rng_seed ));
		} else {
		wp_schedule_single_event(time(), 'admincheckecommerce', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Widgets') {
		$estimated_time = 5 + intval($total_pages / 3.5);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Widgets</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'ecommerce_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Widgets'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		if ($ent_included) { 
		wp_schedule_single_event(time(), 'wpsccheckwidgets', array ($rng_seed ));
		}
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Contact Form 7') {
		$estimated_time = 5 + intval($total_cf7 / 100);
		$estimated_time = time_elapsed($estimated_time);
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for <span style="color: rgb(0, 150, 255); font-weight: bold;">Contact Form 7</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results();
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'scan_in_progress')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'true'), array('option_name' => 'cf7_sip')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Contact Form 7'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		wp_schedule_single_event(time(), 'admincheckcf7', array ($rng_seed, false));
	}
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Entire Site') {
		$estimated_time = intval((($total_pages + $total_posts + $total_media) / 3.5) + (intval(($total_seo_title + $total_seo_desc + $total_cat + $total_tags) / 100)) + 3);
		$estimated_time = time_elapsed($estimated_time);
		
		$scan_message = '';
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> Scan has been started for the <span style="color: rgb(0, 150, 255); font-weight: bold;">Entire Site</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
		clear_results("full");
		$rng_seed = rand(0,999999999);
		wp_enqueue_script( 'results-ajax', plugin_dir_url( __FILE__ ) . '/ajax.js', array('jquery') );
		wp_localize_script( 'results-ajax', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		$wpdb->update($options_table, array('option_value' => time()), array('option_name' => 'last_scan_date')); $sql_count++;
		$wpdb->update($options_table, array('option_value' => 'Entire Site'), array('option_name' => 'last_scan_type')); $sql_count++;
		sleep(1);
		
		set_scan_in_progress($rng_seed);
		wp_schedule_single_event(time(), 'adminscansite', array($rng_seed, $log_debug));
	}
	
	
	
	if ($_GET['action'] == 'check' && $_GET['submit'] == 'Clear Results') {
		$message = 'All spell check results have been cleared';
		clear_results("full");
	}
	
	$end = round(microtime(true),5);
	//echo "Check button presses: " . ($end - $start) . "<br>";
	$start = round(microtime(true),5);
	
	}
	if (isset($_GET['ignore_word'])) {
	if ($_GET['ignore_word'] != '' && $_GET['wpsc-scan-tab'] != 'empty') {
		$ignore_message = ignore_word($_GET['ignore_word']); 
	} elseif ($_GET['ignore_word'] != '' && $_GET['wpsc-scan-tab'] == 'empty') {
		$ignore_message = ignore_word_empty($_GET['ignore_word']); 
	}
	}
	
	if (isset($_GET['add_word'])) {
	if ($_GET['add_word'] != '')
		$dict_message = add_to_dictionary($_GET['add_word']); 
	}
	
	if (isset($_GET['old_words'])) {
	if ($_GET['old_words'] != '' && $_GET['new_words'] != '' && $_GET['page_types'] != '' && $_GET['old_word_ids'] != '')  {
		$message = update_word_admin($_GET['old_words'], $_GET['new_words'], $_GET['page_names'], $_GET['page_types'], $_GET['old_word_ids'], $_GET['mass_edit']);
	} elseif ($_GET['new_words'] != '' && $_GET['page_types'] != '' && $_GET['old_word_ids'] != '') {
		$message = update_empty_admin($_GET['new_words'], $_GET['page_names'], $_GET['page_types'], $_GET['old_word_ids']);
	}
	}
	
		
	$word_count = $wpdb->get_var ( "SELECT COUNT(*) FROM $table_name WHERE ignore_word='false'" ); $sql_count++;
	
	$end = round(microtime(true),5);
	//echo "Check for Ignore/Dictionary/Edit/Suggested Changes: " . ($end - $start) . "<br>";
	$start = round(microtime(true),5);
	
	$pro_words = 0;
	$empty_words = 0;
	if (!$pro_included && !$ent_included) {
		$pro_words = $settings[21]->option_value;
	}
	$total_word_count = $settings[22]->option_value;
	$literacy_factor = $settings[64]->option_value;
	
	
	if ($check_scan && $scan_message == '') {
		$last_type = $settings[45]->option_value;
		$scan_message = '<img src="'. plugin_dir_url( __FILE__ ) . 'images/loading.gif" alt="Scan in Progress" /> A scan is currently in progress for <span class="sc-message" style="color: rgb(0, 150, 255); font-weight: bold;">' . $last_type[0]->option_value . '</span>. Estimated time for completion is '.$estimated_time.' . <a href="/wp-admin/admin.php?page=wp-spellcheck.php">Click here</a> to see scan results. <span class="wpsc-mouseover-button-refresh" style="border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;">?</span><span class="wpsc-mouseover-text-refresh">The page will automatically refresh when the scan is finished. You do not need to remain on this page for the scan to run.<br /><br />Time estimate may vary based on server strength.</span>';
	} elseif ($scan_message == '') {
		$scan_message = "No scan currently running";
	}

	
	$time_of_scan = $wpdb->get_results("SELECT option_value FROM $options_table WHERE option_name='last_scan_finished';"); $sql_count++;
	if ($time_of_scan[0]->option_value == "0") {
		$time_of_scan = "0 Minutes";
	} else {
		$time_of_scan = $time_of_scan[0]->option_value;
		if ($time_of_scan == '') $time_of_scan = "0 Seconds";
	}
	
	$scan_type = $settings[45]->option_value;
	
	
	
	$post_status = array("publish", "draft");

	
	
	
	$page_scan = $settings[28]->option_value;
	$post_scan = $settings[29]->option_value;
	$media_scan = $settings[32]->option_value;
	
	$post_scan_count = $post_scan;
	if ($post_scan_count > $post_count) $post_scan_count = $post_count;
	
	$total_words = $settings[22]->option_value;
	
	wp_enqueue_script('results-nav', plugin_dir_url( __FILE__ ) . 'results-nav.js');
	
	$list_table = new sc_table();
	$list_table->prepare_items();	
	
	//$empty_factor = ();
	?>
		<?php show_feature_window(); ?>
		<?php check_install_notice(); ?>
		
	<style>.search-box input[type=submit] { color: white; background-color: #00A0D2; border-color: #0073AA; } #cb-select-all-1,#cb-select-all-2 { display: none; } td.word { font-size: 15px; } p.submit { display: inline-block; margin-left: 8px; } h3.sc-message { width: 49%; display: inline-block; font-weight: normal; padding-left: 8px; } .wpsc-mouseover-text-page,.wpsc-mouseover-text-post,.wpsc-mouseover-text-refresh, .wpsc-mouseover-text-change { color: black; font-size: 12px; width: 225px; display: inline-block; position: absolute; margin: -13px 0 0 -270px; padding: 3px; border: 1px solid black; border-radius: 10px; opacity: 0; background: white; z-index: -100; } .wpsc-row .row-actions, .wpsc-row .row-actions *{ visibility: visible!important; left: 0!important; } #current-page-selector { width: 12%; } .hidden { display: none; } .wpsc-scan-nav-bar { border-bottom: 1px solid #BBB; margin-botton: 15px; } .wpsc-scan-nav-bar a { text-decoration: none; margin: 5px 5px -1px 5px; padding: 8px; border: 1px solid #BBB; display: inline-block; font-weight: bold; color: black; font-size: 14px; } .wpsc-scan-nav-bar a.selected { border-bottom: 1px solid white; background: white; } #wpsc-empty-fields-tab .button-primary { background: #73019a; border-color: #51006E; text-shadow: 1px 1px #51006d; box-shadow: 0 1px 0 #51006d; } #wpsc-empty-fields-tab .button-primary:hover { background: #9100c3 } #wpsc-empty-fields-tab .button-primary:active { background: #51006d; }.wpsc-scan-buttons input#submit:active { margin-top: -7px; } #wpsc-empty-fields-tab span.wpsc-bulk { display: none; } span.wpsc-bulk { color: black; } th#count { width: 80px; }
	
	</style>
	<script>
		jQuery(document).ready(function() {
			var should_submit = false;
			var shown_box = false;
			var allow_next = false;
			var pending = false;
			
			jQuery(".wpsc-edit-update-button").click( function(event) {
				if (!should_submit) event.preventDefault();
				jQuery('.wpsc-mass-edit-chk').each(function() {
					if (jQuery(this).is(":checked") && shown_box == false) {
						shown_box = true;
						jQuery( "#wpsc-mass-edit-confirm" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							"Yes": function() {
							  jQuery( this ).dialog( "close" );
							  should_submit = true;
							  jQuery("#wpsc-edit-update-button-hidden").click();
							},
							Cancel: function() {
							  jQuery( this ).dialog( "close" );
							}
						  }
						});
				}
				});
				if (shown_box == false) {
					should_submit = true;
					jQuery("#wpsc-edit-update-button-hidden").click();
				}
			  } );
			  
			  jQuery(".next-page, .prev-page, .last-page, .first-page").click(function (event) {
				if (!allow_next) event.preventDefault();
					pending = false;
					button = jQuery(this).attr('href');
					
					jQuery('.wpsc-ignore-checkbox, .wpsc-add-checkbox').each(function() {
						if (jQuery(this).is(":checked")) pending = true;
					});
					
					jQuery('.wpsc-mass-edit-chk').each(function() {
						if (jQuery(this).attr('value') != '') pending = true;
					});
					
					
					if (pending) {
						jQuery( "#wpsc-mass-edit-block" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							Cancel: function() {
							  jQuery( this ).dialog( "close" );
							},
							"Move Forward Anyway": function() {
							  jQuery( this ).dialog( "close" );
							  allow_next = true;
							  window.location.replace(button);
							}
						  }
						});
					} else {
						allow_next = true;
						window.location.replace(button);
					}
			  });
			  
			  jQuery(".wpsc-scan-buttons input").click(function (event) {
				if (!allow_next) event.preventDefault();
					pending = false;
					value = jQuery(this).attr('value');
					button = '/wp-admin/admin.php?page=wp-spellcheck.php&action=check&submit=' + value;
					
					jQuery('.wpsc-ignore-checkbox, .wpsc-add-checkbox').each(function() {
						if (jQuery(this).is(":checked")) pending = true;
					});
					
					jQuery('.wpsc-mass-edit-chk').each(function() {
						if (jQuery(this).attr('value') != '') pending = true;
					});
					
					
					if (pending) {
						jQuery( "#wpsc-mass-edit-block" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							cancel: function() {
							  jQuery( this ).dialog( "close" );
							},
							"Move Forward Anyway": function() {
							  jQuery( this ).dialog( "close" );
							  allow_next = true;
							  window.location.replace(button);
							}
						  }
						});
					} else {
						allow_next = true;
						window.location.replace(button);
					}
			  });
		});
	</script>
	<?php
	$end = round(microtime(true),5);
	//echo "Set up CSS, JavaScript, and any display messages: " . ($end - $start) . "<br>";
	$start = round(microtime(true),5);
	?>
<div id="wpsc-mass-edit-block" title="Are you sure?" style="display: none;">
  <p>You have changes pending on the current page. Please go back and click save all changes.</p>
</div>
<div id="wpsc-mass-edit-confirm" title="Are you sure?" style="display: none;">
  <p>Have you backed up your database? This will update all areas of your website that you have selected WP Spell Check to scan. Are you sure you wish to proceed with the changes?</p>
</div>
		<div class="wrap wpsc-table">
			<h2><a href="admin.php?page=wp-spellcheck.php"><img src="<?php echo plugin_dir_url( __FILE__ ) . '../images/logo.png'; ?>" alt="WP Spell Check" /></a> <span style="position: relative; top: -15px;">Scan Results</span></h2>
			<div class="wpsc-scan-nav-bar">
				<a href="#scan-results" id="wpsc-scan-results" class="selected" name="wpsc-scan-results">Spelling Errors</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-grammar.php" id="wpsc-grammar" name="wpsc-grammar">Grammar</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-seo.php" id="wpsc-empty-fields" name="wpsc-empty-fields">SEO Empty Fields</a>
				<a href="/wp-admin/admin.php?page=wp-spellcheck-html.php" id="wpsc-grammar" name="wpsc-grammar">Broken Code</a>
			</div>
			<div id="wpsc-scan-results-tab" <?php if ($_GET['wpsc-scan-tab'] == 'empty') echo 'class="hidden"';?>>
			<form action="<?php echo admin_url('admin.php'); ?>" method='GET'>
				<div class="wpsc-scan-buttons" style="background: white; padding-left: 8px;">
				<h3 style="display: inline-block;">Scan:</h3>
				<p class="submit"><input style="background-color: #ffb01f; border-color: #ffb01f; box-shadow: 0px 1px 0px #ffb01f; text-shadow: 1px 1px 1px #ffb01f; font-weight: bold;" type="submit" name="submit" id="submit" class="button button-primary" value="Entire Site" <?php if ($checked_pages == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Pages" <?php if ($check_pages == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Posts" <?php if ($check_posts == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<?php if ($pro_included || $ent_included) { ?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="SEO Titles" <?php if ($seo_titles == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="SEO Descriptions" <?php if ($seo_desc == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Media Files" <?php if ($check_media == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<?php } ?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Authors" <?php if ($check_authors == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Contact Form 7" <?php if ($check_cf7 == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>	
				<?php if ($pro_included || $ent_included) { ?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Menus" <?php if ($check_menus == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<!--<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Page Titles" <?php if ($page_titles == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Post Titles" <?php if ($post_titles == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>-->
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Tags" <?php if ($tags == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Categories" <?php if ($categories == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>	
				<!--<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Page Slugs" <?php if ($page_slugs == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Post Slugs" <?php if ($post_slugs == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>-->
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Sliders" <?php if ($check_sliders == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<?php if (is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) { ?><p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="WooCommerce and WP-eCommerce Products" <?php if ($check_ecommerce == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p><?php } ?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Widgets" <?php if ($check_widgets == 'false') echo "style='background: darkgrey!important; color: white!important; border-color: grey!important;' disabled" ?>></p>
				<?php } ?>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Clear Results"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="See Scan Results"></p>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="Stop Scans"></p>
				<?php if (($scan_type[0]->option_value == "Entire Site" || $scan_type[0]->option_value == "Page Content" || $scan_type[0]->option_value == "Post Content") && $scan_message == 'No scan currently running' && $ent_included) { ?>
				<?php } ?>
				<!--<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" style="background-color: red;" value="Create Pages"></p>-->
				</div>
				<div style="background: white; padding: 5px; font-size: 12px;">
				<input type="hidden" name="page" value="wp-spellcheck.php">
				<input type="hidden" name="action" value="check">
				<?php echo "<h3 class='sc-message'style='color: rgb(0, 150, 255); font-size: 1.4em;'>Website Literacy Factor: " . $literacy_factor . "%"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Last scan took $time_of_scan</h3>"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>$scan_message</h3><br />"; ?>
				<?php if (!$ent_included) {
					if ($word_count > 0 && $pro_words > 0) {
						echo "<h3 class='sc-message' style='color: rgb(225, 0, 0);'>" . $pro_words . " Errors were found on other parts of your website. <a href='https://www.wpspellcheck.com/features/?utm_source=baseplugin&utm_campaign=upgradespellch&utm_medium=spellcheck_scan&utm_content=7.0.2' target='_blank'>Click here</a> to upgrade to find and fix all errors.</h3>";
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
				<a href="https://www.wpspellcheck.com/tutorials?utm_source=baseplugin&utm_campaign=toturial_rightside&utm_medium=spell_check&utm_content=7.0.2" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/wp-spellcheck-tutorials.jpg'; ?>" style="max-width: 99%;" alt="Watch WP Spell Check Tutorials" /></a>
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
			<?php if(($message != '' || $ignore_message[0] != '' || $dict_message[0] != '' || $mass_edit_message != '') && $_GET['wpsc-scan-tab'] != 'empty') { ?>
				<div style="text-align: center; background-color: white; padding: 5px; margin: 15px 0; width: 74%;">
					<?php if($message != '') echo "<div class='wpsc-message' style='font-size: 1.3em; color: rgb(0, 115, 0); font-weight: bold;'>" . $message . "</div>"; ?>
					<?php if($mass_edit_message != '') echo "<div class='wpsc-message' style='font-size: 1.3em; color: rgb(0, 115, 0); font-weight: bold;'>" . $mass_edit_message . "</div>"; ?>
					<?php if($ignore_message[0] != '') echo "<div class='wpsc-message' style='font-size: 1.3em; color: rgb(0, 115, 0); font-weight: bold;'>" . $ignore_message[0] . "</div>"; ?>
					<?php if($dict_message[0] != '') echo "<div class='wpsc-message' style='font-size: 1.3em; color: rgb(0, 115, 0); font-weight: bold;'>" . $dict_message[0] . "</div>"; ?>
				</div>
				<?php } ?>
			<form id="words-list" method="get" style="width: 75%; float: left; margin-top: 10px;">
				<input name="wpsc-edit-update-button-hidden" id="wpsc-edit-update-button-hidden" type="submit" value="Save all Changes" class="button button-primary" style="display:none;"/>
				<p class="search-box" style="position: relative; margin-top: 8px;">
					<label class="screen-reader-text" for="search_id-search-input">search:</label>
					<input type="search" id="search_id-search-input-top" name="s-top" value="" placeholder="Search for Misspelled Words">
					<input type="submit" id="search-submit-top" class="button" value="search">
				</p>
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<input name="wpsc-edit-update-button" class="wpsc-edit-update-button" type="submit" value="Save all Changes" class="button button-primary" style="width: 15%; margin-left: 32.5%; display: block; background: #008200; border-color: #005200; color: white; font-weight: bold; position: absolute; margin-top: 7px;"/>
				<?php 
	
	
	 ?>
				<?php 
				$list_table->display(); 
				?>
				
				<?php 
	
				$end_display = time();
	
	 ?>
				<p class="search-box" style="margin-top: 0.7em;">
					<label class="screen-reader-text" for="search_id-search-input">search:</label>
					<input type="search" id="search_id-search-input" name="s" value="" placeholder="Search for Misspelled Words">
					<input type="submit" id="search-submit" class="button" value="search">
				</p>
				<input name="wpsc-edit-update-buttom" class="wpsc-edit-update-button" type="submit" value="Save all Changes" class="button button-primary" style="width: 15%; margin-left: 31.5%; display: block;  background: #008200; border-color: #005200; color: white; font-weight: bold; position: absolute; margin-top: -31px;"/>
			</form>
			
			<div style="padding: 15px; background: white; clear: both; width: 72%; font-family: helvetica;">
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Errors found on <span style='color: rgb(0, 150, 255); font-weight: bold;'>".$settings[45]->option_value."</span>: {$word_count}</h3>"; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Posts scanned: " . $settings[29]->option_value . "/" . $page_count; ?>
				<?php echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Pages scanned: " . $settings[28]->option_value . "/" . $post_count; ?>
				<?php if ($pro_included || $ent_included) { echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'>Media files scanned: " . $settings[32]->option_value . "/" . $media_count . "</h3>"; } ?>
				<?php 
					if ($ent_included) {
						$url = plugins_url()."/wp-spell-check-pro/admin/changes.php"; 
						echo "<h3 class='sc-message' style='color: rgb(0, 115, 0);'><a target='_blank' href='$url'>Click here</a> to view the changelog</h3>"; 
					} else {
						echo "<h3 class='sc-message' style='color: rgb(70, 70, 70);'>Click here to view the changelog<span class='wpsc-mouseover-button-change' style='border-radius: 29px; border: 1px solid green; display: inline-block; margin-left: 10px; padding: 4px 10px; cursor: help;'>?</span><span class='wpsc-mouseover-text-change'>To view the changelog, you must Upgrade to Pro</span></h3>";
					}
					
				?>
			</div>
		</div>
		<!-- Empty Fields  Tab -->
		
		</div>
		<!-- Quick Edit Clone Field -->
		<table style="display: none;">
			<tbody>
				<tr id="wpsc-editor-row" class="wpsc-editor">
					<td colspan="4">
						<div class="wpsc-edit-content">
							<h4 style="display: inline-block;">Edit %Word%</h4>
							<input type="text" size="60" name="word_update[]" style="margin-left: 3em;" value class="wpsc-edit-field edit-field">
							<br><span class="wpsc-bulk"><input name="wpsc-mass-edit[]" class="wpsc-mass-edit-chk" type="checkbox" value="" />Apply this change to the entire website</span>
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
							</select><br>
							<input name="wpsc-mass-edit[]" class="wpsc-mass-edit-chk" type="checkbox" value="" />Apply this change to the entire website
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
	<?php 
	$end = round(microtime(true),5);
	//echo "HTML Rendered(End of function): " . ($end - $start) . "<br>";
	$start = round(microtime(true),5);
	}
	
	
	
	
?>