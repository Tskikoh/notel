<?php
/*
Plugin Name: Allow Categories
Plugin URI: http://jameslow.com/2007/12/02/allow-categories/
Description: Per-category permissions for Wordpress. English + Allow version of L. Fargue http://stknights.free.fr/?p=69
Version: 0.6.6
Author: James Low, Pascaline Chotard (pchotard@digitalys.com)
Author URI: http://jameslow.com http://www.digitalys.com
*/

/*  
    Copyright 2007  James LOW

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


class Allow_Category{

	function allow_getAllCategories() {
		return get_categories(array('hide_empty' => false));
	}

	function allow_getCategory() {
		static $cats;
		if (!isset($cats)) {
			global $user_ID;
			$user = Allow_Category::allow_getUser($user_ID);
			$public = Allow_Category::allow_getPublic();
			$loggedin = Allow_Category::allow_getLoggedIn();
			if ($user && $public && $loggedin) {
				$cats = array_merge($user,$public,$loggedin);
			} elseif ($user && $public) {
				$cats = array_merge($user,$public);
			} elseif ($user && $loggedin) {
				$cats = array_merge($user,$loggedin);
			} elseif ($public && $loggedin) {
				$cats = array_merge($public,$loggedin);
			} elseif ($user) {
				$cats = $user;
			} elseif ($loggedin) {
				$cats = $loggedin;
			} else {
				$cats = $public;
			}
		}
		return $cats;
	}
	
	function allow_getCategoryString() {
		return implode(",",Allow_Category::allow_getCategory());
	}
	
	function allow_getCategoryIncludeString() {
		return "include=" . Allow_Category::allow_getCategoryString();
	}
	
	function allow_getCategoryDetails() {
		return get_categories(Allow_Category::allow_getCategoryIncludeString());
	}

	function allow_getUser($user) {
		$opts = get_option("allow_option");
		$keys = array_keys($opts);
		$sid = (string)$user;
		if (in_array($sid, $keys)) {
			return $opts[$sid];
		}
		return false;
	}

	function allow_getPublic() {
		return Allow_Category::allow_getUser("public");
	}

	function allow_getLoggedIn() {
		if (Allow_Category::allow_loggedIn()) {
			return Allow_Category::allow_getUser("loggedin");
		}
	}
	
	function allow_removeCategorySelection($page) {
		global $catUserPreg;
		$cats = Allow_Category::allow_getAllCategories();
		foreach ($cats as $cat){
			$keep = false;
			if ($catUserPreg) {
				//might need to add code here, to make sure old categories remain, even if user doesn't have permission
				if (in_array($cat->cat_ID,$catUserPreg)) {
					$keep = true;
				}
			}
			if (!$keep) {
				$page2=preg_replace('#id="in-category-'. $cat->cat_ID .'" checked="checked"#', 'id="in-category-'. $cat->cat_ID .'" checked="checked" disabled', $page);
				if($page == $page2) {
					if ( count(get_categories(array('child_of' => $cat->cat_ID))) ) {
						$page=preg_replace("#<li id=\"category-$cat->cat_ID\".*?</ul>#sim", "", $page);
					}else{
						$page=preg_replace("#<li id=\"category-$cat->cat_ID\".*?</li>#sim", "", $page);
					}
				} else {
					$page = $page2;	
				}
			}
		}
		return $page;
	}

	function allow_wpversion_cat() {
		global  $wp_version;
		$version = explode (".", $wp_version);
		return ($version[0] > 2 || ($version[0] == 2 && $version[1] >= 3));
	}
	
	function allow_wpversion_user() {
		global  $wp_version;
		$version = explode (".", $wp_version);
		return ($version[0] > 2 || ($version[0] == 2 && $version[1] >= 5));
	}

	function allow_where($where) {
		global $wpdb;
		global $userdata;
		$show_titles = get_option("allow_show_titles");
		$pages_public = get_option("allow_pages_public");
		$redirect_notperm = get_option("allow_redirect_notperm");
		get_currentuserinfo();

		//If we know its a page, and the user can see pages, no point to do the following
		if (Allow_Category::allow_notAdmin() && !($redirect_notperm && !Allow_Category::allow_loggedIn() && (is_single() || is_category())) && !($show_titles && is_feed()) && !(($pages_public || Allow_Category::allow_loggedIn()) && is_page())) {
			$catUser = Allow_Category::allow_getCategory();
			if (Allow_Category::allow_wpversion_cat()) {
				$ids = get_objects_in_term($catUser, 'category');
				if (is_wp_error($ids)) {
					return $ids;
				}
			} else {
				$query = 'SELECT p.ID FROM $wpdb->posts p, $wpdb->post2cat p2c, $wpdb->categories c WHERE p.ID = p2c.post_id AND p2c.category_id = c.cat_ID';
				$query .= ' AND category_id in (' . implode(', ', $catUser) . ')';
				$ids = $wpdb->get_col($query . ';');
			}
			if ( count($ids) <= 0 ) {
				//No Permissions, remove all posts
				$ids = array(-1);
			}
			$out_posts = implode(', ', $ids);
			if ($pages_public || Allow_Category::allow_loggedIn()) {
				$includepages = " OR $wpdb->posts.post_type = 'page'";	
			}
			if (Allow_Category::allow_loggedIn()) {
				$draftsbyauthor = " OR ($wpdb->posts.post_status = 'draft' AND $wpdb->posts.post_author = $userdata->ID)";
			}
			$where .= " AND ($wpdb->posts.ID IN ($out_posts)$draftsbyauthor$includepages)";
		}
		return $where;
	}

	function allow_notAllowed($page) {
		return preg_replace("#<form name=\"post\".*?</form>#sim", "<div class=\"error\"><p><strong>You are not authorised to edit posts in this category.</strong><br />
				<a href=\"".$_SERVER['HTTP_REFERER']."\">Return to previous page.</a></p></div>", $page);
	}

	function allow_adminHead($in) {
		global $catUserPreg;
		$catUserPreg = Allow_Category::allow_getCategory();
		get_currentuserinfo();
		if (Allow_Category::allow_notAdmin()) {
			if(preg_match("#/wp-admin/post-new.php#", $_SERVER['REQUEST_URI']) ) {
				ob_start(array('Allow_Category', 'allow_removeCategorySelection'));
			}
			if(preg_match("#/wp-admin/post.php#", $_SERVER['REQUEST_URI']) ) {
					$trouve=false;
					if ($catUserPreg) {
						$theCat=get_the_category();
						foreach($theCat as $cat_u){
							if (in_array($cat_u->cat_ID,$catUserPreg)){
								$trouve=true;
								break;
							}
						}
					}
					if ($trouve){
						ob_start(array('Allow_Category', 'allow_removeCategorySelection'));
					}else{
						ob_start(array('Allow_Category', 'allow_notAllowed'));
					}
			}
		}
		return $in;
	}
	
	function allow_delPost($in){
		global $wpdb;
		$article=get_post($in);
		$catUser = Allow_Category::allow_getCategory();
		if (Allow_Category::allow_notAdmin()) {
			$trouve=false;
			if ($catUser) {
				$cats = get_the_category($in);

				foreach($cats as $ct){
					if (in_array($ct->cat_ID,$catUser)) {
						$trouve=true;
						break;
					}
				}
			}
			if (!$trouve) {
				die("You are not autorised to delete posts in this category.");
			}
		}
	}
	
	function allow_notAdmin() {
		global $userdata;
		get_currentuserinfo();
		if (Allow_Category::allow_loggedIn()) {
			return ($userdata->user_level < 10);
		} else {
			return true;
		}
	}
	
	function allow_loggedIn() {
		global $userdata;
		get_currentuserinfo();
		return $userdata->user_login != '';
	}

	function allow_comments($result) {
		//Not the most elegant solution using a filter function, as this function is called every
		//time there is a comment,  but none of the comment action hooks seem to happen at the right
		//time. So we use the below global variable to only do it once.
		global $Allow_Comments_Done;
		if (!$Allow_Comments_Done) {
			$Allow_Comments_Done = true;
			$msg = "Please login to view content";
			global $userdata;
			get_currentuserinfo();
			$force_login = get_option("allow_force_login");
			$show_feed = get_option("allow_show_feed");
			//comments_rss2_url
			if (Allow_Category::allow_notAdmin()) {
				$catUser = Allow_Category::allow_getCategory();
				$trouve = false;
				if (is_array($catUser)) {
					global $posts;
					$postlocal = $posts[0];
					$cats = get_the_category($postlocal->ID);
					foreach($cats as $ct){
						if (in_array($ct->cat_ID,$catUser)) {
							$trouve=true;
							break;
						}
					}
				}
				if (!$trouve) {
					global $wp_query;
					$comments = $wp_query->comments;
					foreach($comments as $comment) {
						$comment->comment_content = $msg;
					}
				}
			}
		}
	}

	function allow_catRedirect() {
		global $userdata;
		get_currentuserinfo();
		$force_login = get_option("allow_force_login");
		$show_feed = get_option("allow_show_feed");
		$redirect_notperm = get_option("allow_redirect_notperm");
		//comments_rss2_url
		if ($show_feed && is_feed()) {
			if (Allow_Category::allow_notAdmin()) {
				$show_titles = get_option("allow_show_titles");
				//If show titles we need to remove content for private posts
				if ($show_titles) {
					$catUser = Allow_Category::allow_getCategory();
					global $posts;
					$i=0;
					$msg = "Please login to view content";
					while ($i < count($posts)) {
						$keep = false;
						$postlocal = $posts[$i];
						if ($catUser) {
							$cats = get_the_category($postlocal->ID);
							foreach ($cats as $cat) {
								if (in_array($cat->cat_ID,$catUser)) {
									$keep = true;
									break;
								}
							}
						}
						if (!$keep) {
							$postlocal->post_content = $msg;
							$postlocal->post_excerpt = $msg;
						}
						$i++;
					}
				}
			}
		} elseif (!Allow_Category::allow_loggedIn() && $force_login) {
			Allow_Category::allow_redirect();
		} elseif (!Allow_Category::allow_loggedIn() && ($redirect_notperm && (is_single() || is_category()))) {
			if (Allow_Category::allow_notAdmin()) {
				$catUser = Allow_Category::allow_getCategory();
				$redirect = true;
				if ($catUser) {
					if (is_category()) {
						foreach($catUser as $cat) {
							if(is_category($cat)) {
								$redirect = false;
								break;
							}
						}
					} else {
						global $post;
						$cats = get_the_category($post->ID);
						foreach ($cats as $cat) {
							if (in_array($cat->cat_ID,$catUser)) {
								$redirect = false;
								break;
							}
						}
					}
				}
				if ($redirect) {
					Allow_Category::allow_redirect();
				}
			}
		}
	}
	
	function allow_redirect() {
		//if ( (!empty($_COOKIE[USER_COOKIE]) && !wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true)) || (empty($_COOKIE[USER_COOKIE])) ) {
		nocache_headers();
		header("HTTP/1.1 302 Moved Temporarily");
		header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        header("Status: 302 Moved Temporarily");
		exit();
		//}
	}
	
	function allow_form() {
		global $wpdb;
		if (isset($_POST['info_update'])) {
			$updated = Allow_Category::allow_saveForm($_POST);
			if ($updated) {
				echo "<div class=\"updated\"><p><strong>" . __('Settings saved.', 'allow_option') ."</strong></p></div>";
			} else {
				echo "<div class=\"error\"><p><strong>" . __('There was an error while saving.', 'allow_option') ."</strong></p></div>";			
			}
		}
		echo "<div class=\"wrap\"><form method=\"post\" action=\"\">";
		echo "<h2>Allow access to category parents.</h2>";
		echo "<p>Check the category to ALLOW permission.</p>";

		if (function_exists('get_authors')) {
			$wpusers = get_authors();
			$userIds = array();
			foreach ($wpusers as $wpuser) {
				//TODO: When wordpress implement get_autors, check this works
				$userIds[] = $wpuser->ID;
			}
		} else {
			$userIds = $wpdb->get_col("SELECT ID FROM $wpdb->users;");
		}
		$users = array();
		$cats = Allow_Category::allow_getAllCategories();
		
		$opts = get_option("allow_option");
	
		echo "<table class=\"widefat\">
		<thead>
		<tr>
		<th scope=\"col\"><div style=\"text-align: center\">User</div></th>";
		foreach ($cats as $ct){
			echo "<th scope=\"col\"><div style=\"text-align: center\">$ct->cat_name</div></th>";
		}
		echo "</tr>
		</thead>";

		$class1="alternate author-other status-publish";
		$class2="author-other status-publish";

		$i=0;
		$userId = "public";
		echo "<tr class=\"$class1\"><td>(Public)";
		echo '<input type="hidden" name="user[]" value="'. $userId.'" />';
		echo '</td>';
		foreach ($cats as $ct){
			echo "<td><div align=\"center\"><input name=\"cat[$i][]\" type=\"checkbox\" value=\"$ct->cat_ID\"";
			if ($opts["$userId"] && in_array($ct->cat_ID,$opts["$userId"])) echo "checked";
			echo "/></div></td>";
		}
		echo '</tr>';

		$i=1;
		$userId = "loggedin";
		echo "<tr class=\"$class2\"><td>(Logged In)";
		echo '<input type="hidden" name="user[]" value="'. $userId.'" />';
		echo '</td>';
		foreach ($cats as $ct){
			echo "<td><div align=\"center\"><input name=\"cat[$i][]\" type=\"checkbox\" value=\"$ct->cat_ID\"";
			if ($opts["$userId"] && in_array($ct->cat_ID,$opts["$userId"])) echo "checked";
			echo "/></div></td>";
		}
		echo '</tr>';

		$i=2;
		foreach ($userIds as $userId) {
			$tmp_user = new WP_User($userId);
			if (Allow_Category::allow_wpversion_user()) {
				$level = $tmp_user->user_level;
			} else {
				$level = $tmp_user->wp_user_level;
			}
			if ($level > 7) continue;
			$users[$userId] = $tmp_user;
			echo "<tr class=\"$class1\"><td>$tmp_user->user_nicename<input type=\"hidden\" name=\"user[]\" value=\"$tmp_user->ID\" /></td>";
			foreach($cats as $ct){
				echo "<td><div align=\"center\"><input name=\"cat[$i][]\" type=\"checkbox\" value=\"$ct->cat_ID\"";
				if ($opts["$userId"] && in_array($ct->cat_ID,$opts["$userId"])) echo "checked";
				echo "/></div></td>";
			}
			echo "</tr>\n";
			$classTemp=$class1;
			$class1=$class2;
			$class2=$classTemp;
			$i++;
		}
	
		echo "</table><br />";
	
		/*
		$force_login = get_option("allow_force_login");
		$show_feed = get_option("allow_show_feed");
		$show_titles = get_option("allow_show_titles");
		$pages_public = get_option("allow_pages_public");
		$redirect_notperm = get_option("allow_redirect_notperm");
		echo '<input name="force_login" type="checkbox" value="1"' .  ($force_login ? 'checked' : '') . '/> Force user login ';
		echo '<input name="show_feed" type="checkbox" value="1"' .  ($show_feed ? 'checked' : '') . '/> Do not force login for feed ';
		echo '<input name="show_titles" type="checkbox" value="1"' .  ($show_titles ? 'checked' : '') . '/> Show private post titles in rss feed ';
		echo '<input name="pages_public" type="checkbox" value="1"' .  ($pages_public ? 'checked' : '') . '/> Pages are public (or logged in if force login) ';
		echo '<input name="redirect_notperm" type="checkbox" value="1"' .  ($redirect_notperm ? 'checked' : '') . '/> Redirect to login if not permissioned ';
		*/
		Allow_Category::allow_checkbox("force_login","Force user login");
		Allow_Category::allow_checkbox("show_feed","Do not force login for feed");
		Allow_Category::allow_checkbox("show_titles","Show private post titles in rss feed");
		Allow_Category::allow_checkbox("pages_public","Pages are public (or logged in if force login)");
		Allow_Category::allow_checkbox("redirect_notperm","Redirect to login if not permissioned");
		
		echo "<div class=\"submit\"><input type=\"submit\" name=\"info_update\" value=\"" . __('Save', 'allow_option') . "\" /></div></form>";
	
		echo "</div>";
	
	}
	
	function allow_checkbox($name, $text) {
		$checked = get_option("allow_$name");
		echo '<input name="'.$name.'" type="checkbox" value="1"' . ($checked ? 'checked' : '') . "/> $text ";
	}
	
	function allow_saveForm() {
		$len = count($_POST["user"]);
		if ($len){
			$opts = array();
			for ($i = 0; $i < $len; $i++) {
				if ($_POST["user"][$i] ){
					if (count($_POST["cat"][$i])>0){
						foreach($_POST["cat"][$i] as $ucat){
							$opts[$_POST["user"][$i]][] = (int)$ucat;
						}
					}
				}
			}
			update_option("allow_option", $opts);
		}

		Allow_Category::allow_update_bool("force_login");
		Allow_Category::allow_update_bool("show_feed");
		Allow_Category::allow_update_bool("show_titles");
		Allow_Category::allow_update_bool("pages_public");
		Allow_Category::allow_update_bool("redirect_notperm");
		return true;
	}

	function allow_update_bool($name) {
		$value = $_POST[$name];
		if (isset($value)) {
			update_option("allow_$name", true);
		} else {
			update_option("allow_$name", false);
		}
	}

	function allow_menu() {
		add_management_page(__('Allow Categories'),
						 __('Allow Categories'),
						 10, basename(__FILE__), array('Allow_Category', 'allow_form'));
		
	}

	function allow_list_cats($catname) {
		//Currently can't filter categories here or in wp_list_categories
		/*
		$catdetails = Allow_Category::allow_getCategoryDetails();
		foreach ($catdetails as $cat) {
			if ($cat->cat_name == $catname) {
				return $catname;
			}
		}
		*/
		return $catname;
	}

	function allow_menu_nav($items)	{
		$cat_ids_allowed = Allow_Category::allow_getCategory();	// get ID categories allowed in the user context
		if (!is_array($cat_ids_allowed)) {
			return $items;
		}
		foreach ( $items as $key => $item ) {	// foreach categories
			$object_id = get_post_meta( $item->ID, '_menu_item_object_id', true );	// ID of the category
			$type      = get_post_meta( $item->ID, '_menu_item_type',      true );	// type of the category (custom = links, post_type = pages, taxonomy = categories)
			if ( 'taxonomy' == $type)	{	// only from categories
				$is_allowed = false;
				foreach ($cat_ids_allowed as $idcat)	{
					if ( $idcat == $object_id )	{	// category is alowed
						$is_allowed = true;
						break;
					}
				}
				if (! $is_allowed ) unset($items[$key]);	// remove the item from the navigation items if not in the allowed categories
			}
		}
		return $items;
	}
}

function allow_list_categories($args = '') {
	if (Allow_Category::allow_notAdmin()) {
		wp_list_categories(($args == '' ? '' : $args . '&') . Allow_Category::allow_getCategoryIncludeString());
	} else {
		wp_list_categories($args);
	}
}

add_option('allow_option', 		array(), '', false);
add_action('admin_menu', 		array('Allow_Category','allow_menu'));
add_action('admin_head', 		array('Allow_Category','allow_adminHead'));
if('wp-login.php' != $pagenow && 'wp-register.php' != $pagenow) add_action('template_redirect',		array('Allow_Category','allow_catRedirect'));
add_action('delete_post',		array('Allow_Category','allow_delPost'));
add_action('posts_where',		array('Allow_Category','allow_where'));
add_filter('comment_text_rss',		array('Allow_Category','allow_comments'));
add_filter('getarchives_where', array('Allow_Category','allow_where'));
add_filter('wp_get_nav_menu_items',  array('Allow_Category','allow_menu_nav'));
//add_filter('list_cats',		array('Allow_Category','allow_list_cats'));
//add_action('commentrss2_item',	array('Allow_Category','allow_comments'));
//add_action('parse_request',		array('Allow_Category','allow_parse'));
//add_action('do_feed_atom',		array('Allow_Category','allow_comments'));
//add_action('do_feed_rss',		array('Allow_Category','allow_comments'));
//add_action('rss2_head',		array('Allow_Category','allow_comments'));
//add_action('do_feed_rdf',		array('Allow_Category','allow_comments'));