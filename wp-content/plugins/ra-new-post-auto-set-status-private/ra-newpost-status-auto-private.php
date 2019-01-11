<?php 
/*
Plugin Name: RA - New Post Auto Set Status "Private"
Description: The status of the post exhibited directly from new post is compulsorily changed into "private".
Author: Rasin (skuraomoto)
Author URI: http://www.rains.jp/
Version: 1.0.3
License: GPLv2
Text Domain: rains-npassp
Domain Path: /languages/
*/
/*
  Copyright 2013 Rains
  
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$ra_npassp_lang = dirname(plugin_basename(__FILE__)) . "/languages/";
load_plugin_textdomain("rains-npassp", false, $ra_npassp_lang);

function ra_post_status_auto_private() {

	global $post, $wpdb;
	
    $wpdb->update($wpdb->posts, array('post_status' => 'private'), array('ID' => $post->ID));
	clean_post_cache($post->ID);
	
}
add_action('new_to_publish', 'ra_post_status_auto_private');
add_action('draft_to_publish', 'ra_post_status_auto_private');
add_action('auto-draft_to_publish', 'ra_post_status_auto_private');