<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Note_Press
 * @author    datainterlock <postmaster@datainterlock.com>
 * @license   GPL-3.0+
 * @link      http://www.datainterlock.com
 * @Copyright (C) 2015 Rod Kinnison postmaster@datainterlock.com
 *
 * @wordpress-plugin
 
 * Plugin Name:       Note Press
 * Plugin URI:        http://www.datainterlock.com
 * Description:       Add, edit and delete multiple notes and display them with icons on the Admin page or dashboard.
 * Version:           0.1.10
 * Author:            datainterlock
 * Author URI:        http://www.datainterlock.com
 * Text Domain:       Note_Press
 * License:           -3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate: v2.6.1
 
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 
 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 The basic structure of this plugin was cloned from the [WordPress-Plugin-Boilerplate]
 (https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) project. Thanks Tom!
 
 Many of the features of the Boilerplate, such as the admin settings, css and js are included 
 in this plugin yet are not used in this version. I've went ahead and included them as I do have
 plans to use them in the future.
 
 */
// If this file is called directly, abort.
if (!defined('WPINC'))
	{
	die;
	}
/**

* The code that runs during plugin activation.

* This action is documented in includes/class-Note_Press-activator.php

*/
function activate_Note_Press()
	{
	require_once plugin_dir_path(__FILE__) . 'includes/class-Note_Press-activator.php';
	Note_Press_Activator::activate();
	}
/**

* The code that runs during plugin deactivation.

* This action is documented in includes/class-Note_Press-deactivator.php

*/
function deactivate_Note_Press()
	{
	require_once plugin_dir_path(__FILE__) . 'includes/class-Note_Press-deactivator.php';
	Note_Press_Deactivator::deactivate();
	}
register_activation_hook(__FILE__, 'activate_Note_Press');
register_deactivation_hook(__FILE__, 'deactivate_Note_Press');
/**

* The core plugin class that is used to define internationalization,

* admin-specific hooks, and public-facing site hooks.

*/
require plugin_dir_path(__FILE__) . 'includes/class-Note_Press.php';
/**

* Begins execution of the plugin.

*

* Since everything within the plugin is registered via hooks,

* then kicking off the plugin from this point in the file does

* not affect the page life cycle.

*

* @since    1.0.0

*/
function run_Note_Press()
	{
	$plugin = new Note_Press();
	$plugin->run();
	}
run_Note_Press();

