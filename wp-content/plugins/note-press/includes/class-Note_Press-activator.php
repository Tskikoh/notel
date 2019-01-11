<?php
/**
 * Fired during plugin activation
 *
 * @link       http://www.datainterlock.com
 * @since      1.0.0
 *
 * @package    Note_Press
 * @subpackage Note_Press/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Note_Press
 * @subpackage Note_Press/includes
 * @author     Rod Kinnison <postmaster@datainterlock.com>
 */
class Note_Press_Activator
	{
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
		{
		global $wpdb;
		function table_column_exists($table_name, $column_name)
			{
			global $wpdb;
			$column = $wpdb->get_results($wpdb->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ", DB_NAME, $table_name, $column_name));
			if (!empty($column))
				{
				return true;
				}
			return false;
			}
		
		function db_install($table_name)
		{
			global $wpdb;
			if($wpdb->get_var("SHOW TABLES LIKE 'datainterlock_wp_Note_Press'") == "datainterlock_wp_Note_Press") 
			{
				$GLOBALS['wpdb']->query("RENAME TABLE `datainterlock_wp_Note_Press` to $table_name");
			}
			
			$sql        = "
				CREATE TABLE IF NOT EXISTS `$table_name` (
				  `ID` int(11) NOT NULL AUTO_INCREMENT,
				  `Icon` varchar(50) NOT NULL,
				  `Title` varchar(255) NOT NULL,
				  `Content` mediumtext NOT NULL,
				  `Date` datetime NOT NULL,
				  `AddedBy` varchar(255) NOT NULL,
				  `userTo` varchar(255) NOT NULL,
				  `userRead` tinyint(1) NOT NULL DEFAULT '0',
				  `Priority` int(11) NOT NULL,
				  `Deadline` datetime DEFAULT NULL,
				  `Sticky` tinyint(1) NOT NULL DEFAULT '0',
				  `StickyColor` varchar(20) NOT NULL DEFAULT '#eeee22',
				  PRIMARY KEY (`ID`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;
			";
			$wpdb->query($sql);	
			if (!table_column_exists($table_name,'Priority'))
				{
					$sql = "
					ALTER TABLE $table_name
					DROP COLUMN ViewLevel,
					DROP COLUMN Category,
					ADD COLUMN `userTo` varchar(255) NOT NULL,
					ADD COLUMN `userRead` tinyint(1) NOT NULL DEFAULT '0',
					ADD COLUMN `Priority` int(11) NOT NULL,
					ADD COLUMN `Deadline` datetime DEFAULT NULL
				 ";
					$wpdb->query($sql);
					$noteList   = $wpdb->get_results("SELECT * FROM $table_name");
					foreach ($noteList as $note)
					{
						$users = get_users( array( 'fields' => array( 'display_name','ID' ) ) );
						foreach ($users as $user)
						{
							if ($user->display_name == $note->AddedBy)
							{
								$userid = $user->ID;
							}
						}
						$wpdb->update($table_name, array(
						'userTo' => $userid,
						'AddedBy' => $userid,
						'userRead' => 0,
						'Priority' => 0,
						'Deadline' => NULL
						), array(
						'ID' => $note->ID
						));
					}
				}
			if (!table_column_exists($table_name,'Sticky'))
				{		
					$sql = 
					"ALTER TABLE $table_name
					ADD COLUMN `Sticky` tinyint(1) NOT NULL DEFAULT '0',
					ADD COLUMN `StickyColor` varchar(20) NOT NULL DEFAULT '#eeee22'
					";
					$wpdb->query($sql);				
				}				
		}
		if (file_exists(plugin_dir_path( __DIR__ ).'messages.pot'))
		{
			unlink(plugin_dir_path( __DIR__ ).'messages.pot');
		}
		if (file_exists(plugin_dir_path( __DIR__ ).'messages.temp'))
		{
			unlink(plugin_dir_path( __DIR__ ).'messages.temp');
		}
		if (is_multisite()) 
		{
			$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
			if ($blogs) 
			{
				foreach($blogs as $blog) 
				{
					switch_to_blog($blog['blog_id']);
					$table_name = $wpdb->prefix . "Note_Press";
					db_install($table_name);
					restore_current_blog();
				}
			}
		}
		else
		{
			$table_name = $wpdb->prefix . "Note_Press";
			db_install($table_name);
		}
		update_option("Note_Press_db_version", '2.1');
		}
	}
