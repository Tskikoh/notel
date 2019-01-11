<?php
/**


* The admin-specific functionality of the plugin.


*


* @link       http://www.datainterlock.com


* @since      1.0.0


*


* @package    Note_Press


* @subpackage Note_Press/admin


*/
/**


* The admin-specific functionality of the plugin.


*


* Defines the plugin name, version, and two examples hooks for how to


* enqueue the admin-specific stylesheet and JavaScript.


*


* @package    Note_Press


* @subpackage Note_Press/admin


* @author     Rod Kinnison <postmaster@datainterlock.com>


*/
class Note_Press_Admin
	{
	/**
	
	
	* The ID of this plugin.
	
	
	*
	
	
	* @since    1.0.0
	
	
	* @access   private
	
	
	* @var      string    $plugin_name    The ID of this plugin.
	
	
	*/
	private $plugin_name;
	/**
	
	
	* The version of this plugin.
	
	
	*
	
	
	* @since    1.0.0
	
	
	* @access   private
	
	
	* @var      string    $version    The current version of this plugin.
	
	
	*/
	private $version;
	/**
	
	
	* Initialize the class and set its properties.
	
	
	*
	
	
	* @since    1.0.0
	
	
	* @param      string    $plugin_name       The name of this plugin.
	
	
	* @param      string    $version    The version of this plugin.
	
	
	*/
	public function __construct($plugin_name, $version)
		{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		}
	/**
	
	
	* Register the stylesheets for the admin area.
	
	
	*
	
	
	* @since    1.0.0
	
	
	*/
	public function enqueue_styles()
		{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Note_Press_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Note_Press_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('wp-color-picker');
		//		wp_enqueue_style($this->plugin_name, 'wp-color-picker', array(), $this->version, 'all' );
		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/Note_Press-admin.css', array(), $this->version, 'all' );
		}
	/**
	
	
	* Register the JavaScript for the admin area.
	
	
	*
	
	
	* @since    1.0.0
	
	
	*/
	public function enqueue_scripts()
		{
		/**
		
		
		* This function is provided for demonstration purposes only.
		
		
		*
		
		
		* An instance of this class should be passed to the run() function
		
		
		* defined in Note_Press_Loader as all of the hooks are defined
		
		
		* in that particular class.
		
		
		*
		
		
		* The Note_Press_Loader will then create the relationship
		
		
		* between the defined hooks and the functions defined in this
		
		
		* class.
		
		
		*/
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/js/admin.js', array(
			'wp-color-picker'
		), $this->version, false);
		}
	public function Note_Press_dashboard($post, $callback_args)
		{
		global $wpdb;
		$userID     = get_current_user_id();
		$table_name = $wpdb->prefix . "Note_Press";
		$num        = $wpdb->get_var("SELECT COUNT(*) FROM $table_name where userTo = $userID");
		if ($num == 0)
			{
			echo __('No notes to you.', 'Note_Press');
			}
		else
			{
			echo '<table width="100%">';
			echo '<tr><td></td><td>' . __("Title", "Note_Press") . '</td><td>' . __("Priority", "Note_Press") . "</td><td>" . __("Deadline", "Note_Press") . "</td><td>" . __("Read", "Note_Press") . "</tr>";
			$noteList = $wpdb->get_results("Select * from $table_name where userTo = $userID");
			foreach ($noteList as $note)
				{
				if ($note->Deadline == NULL)
					{
					$thisdate = '';
					}
				else
					{
					$date     = new DateTime($note->Deadline);
					$thisdate = date_format($date, 'Y-m-d');
					}
				echo '<tr><td>';
				echo ("<img src=" . get_option("Note_Press_icons_url", 'null') . $note->Icon . "  alt='Icon not found' >");
				echo '</td><td>';
				echo "<a href='?page=Note_Press-Main-Menu&action=view&id=$note->ID'>" . $note->Title . '</a>';
				echo '</td><td>';
				switch ($note->Priority)
				{
					case 0:
						echo "<img src=" . plugins_url('admin/images/P0.png', dirname(__FILE__)) . " alt='Icon not found'>";
						break;
					case 1:
						echo "<img src=" . plugins_url('admin/images/P1.png', dirname(__FILE__)) . " alt='Icon not found'>";
						break;
					case 2:
						echo "<img src=" . plugins_url('admin/images/P2.png', dirname(__FILE__)) . " alt='Icon not found'>";
						break;
				}
				echo '</td><td>';
				echo $thisdate;
				echo '</td><td>';
				switch ($note->userRead)
				{
					case 0:
						echo "<img src=" . plugins_url('admin/images/X.png', dirname(__FILE__)) . " alt='Icon not found'>";
						break;
					case 1:
						echo "<img src=" . plugins_url('admin/images/CH.png', dirname(__FILE__)) . " alt='Icon not found'>";
						break;
				}
				echo '</td></tr>';
				}
			echo '</table>';
			}
		}
	function Note_Press_sticky_dashboard($post, $callback_args)
		{
		global $wpdb;
		$dashid     = substr($callback_args['id'], strpos($callback_args['id'], '-') + 1);
		$dashid     = substr($dashid, 0, strpos($dashid, '-'));
		$table_name = $wpdb->prefix . "Note_Press";
		$SQL        = "Select * from $table_name where ID = $dashid";
		$thisSticky = $wpdb->get_row($SQL);
		$wpdb->update($table_name, array(
			'userRead' => '1'
		), array(
			'ID' => $dashid
		), array(
			'%s'
		), array(
			'%d'
		));
		echo '<style>


		#' . $callback_args['id'] . '{


    	background-color: ' . $thisSticky->StickyColor . ';


		}


		</style>';
		if ($thisSticky->Deadline == NULL)
			{
			$thisdate = '';
			}
		else
			{
			$date     = new DateTime($thisSticky->Deadline);
			$thisdate = date_format($date, 'Y-m-d');
			}
		switch ($thisSticky->Priority)
		{
			case 0:
				$icon = "<img src=" . plugins_url('admin/images/P0.png', dirname(__FILE__)) . " alt='Icon not found'>";
				break;
			case 1:
				$icon = "<img src=" . plugins_url('admin/images/P1.png', dirname(__FILE__)) . " alt='Icon not found'>";
				break;
			case 2:
				$icon = "<img src=" . plugins_url('admin/images/P2.png', dirname(__FILE__)) . " alt='Icon not found'>";
				break;
		}
		$users = get_users(array(
			'fields' => array(
				'display_name',
				'ID'
			)
		));
		foreach ($users as $user)
			{
			if ($user->ID == $thisSticky->AddedBy)
				{
				$username = $user->display_name;
				}
			}
		echo "<table width='100%'>";
		echo "<tr><td><img src=" . get_option("Note_Press_icons_url", 'null') . $thisSticky->Icon . "  alt='Icon not found' ></td>";
		echo "<td><strong>" . __("From:", "Note_Press") . "</strong> $username</td><td><strong>" . __("Priority:", "Note_Press") . "</strong> $icon</td><td><strong>" . __("Deadline:", "Note_Press") . "</strong> $thisdate</td></tr></table>";
		echo '<hr>';
		echo $thisSticky->Content;
		echo '<hr>';
		echo '<form id="DeleteSticky" name="Delete Sticky" method="post" action="">


		  		<input type="submit" name="Delete" id="Delete" value="Delete" class="button button-primary" />


				<input type="hidden" value=' . $dashid . ' name="StickyID">


				</form>';
		}
	function Note_Press_add_dashboard_widgets()
		{
		global $wpdb;
		$table_name = $wpdb->prefix . "Note_Press";
		if (isset($_POST['StickyID']))
			{
			$wpdb->delete($table_name, array(
				'ID' => $_POST['StickyID']
			));
			}
		$user       = get_current_user_id();
		$SQL        = "Select * from $table_name where Sticky = 1 and userTo = $user";
		$stickylist = $wpdb->get_results($SQL);
		foreach ($stickylist as $sticky)
			{
			if ($sticky->userTo = $user)
				{
				add_meta_box('Note_Press-' . $sticky->ID . '-dashboard_widget', $sticky->Title, array(
					$this,
					'Note_Press_sticky_dashboard'
				), 'dashboard', 'normal', 'low');
				}
			}
		$menulevel = get_option("Note_Press_Menu_Level", 3);
		switch ($menulevel)
		{
			case 0:
				$setting = 'edit_posts';
				break;
			case 1:
				$setting = 'publish_posts';
				break;
			case 2:
				$setting = 'moderate_comments';
				break;
			case 3:
				$setting = 'manage_options';
				break;
		}
		if (current_user_can($setting))
			{
			add_meta_box('Note_Press_dashboard_widget', __('Note Press - Notes to you', 'Note_Press'), array(
				$this,
				'Note_Press_dashboard'
			), 'dashboard', 'side', 'high');
			}
		}
	function Note_Press_add_plugin_admin_menu()
		{
		$menulevel = get_option("Note_Press_Menu_Level", 3);
		switch ($menulevel)
		{
			case 0:
				$setting = 'edit_posts';
				break;
			case 1:
				$setting = 'publish_posts';
				break;
			case 2:
				$setting = 'moderate_comments';
				break;
			case 3:
				$setting = 'manage_options';
				break;
		}
		add_menu_page('Note Press', 'Note Press', $setting, 'Note_Press-Main-Menu', array(
			$this,
			'Note_Press_load_menu'
		), plugins_url('admin/images/Note_Pressicon.png', dirname(__FILE__)));
		add_submenu_page('Note_Press-Main-Menu', __('Note Press Settings', 'Note Press'), __('Note Press Settings', 'Note Press'), 'manage_options', 'Note-Press-Settings', array(
			$this,
			'Note_Press_load_settings'
		));
		}
	function Note_Press_load_menu()
		{
		include_once('Note_Press-admin-menu.php');
		}
	function Note_Press_load_settings()
		{
		include_once('Note_Press-settings.php');
		}
	function Note_Press_load_help()
		{
		include_once('Note_Press-help.php');
		}
	}


