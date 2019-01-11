<?php





/**


 * Define the internationalization functionality


 *


 * Loads and defines the internationalization files for this plugin


 * so that it is ready for translation.


 *


 * @link       http://www.datainterlock.com


 * @since      1.0.0


 *


 * @package    Note_Press


 * @subpackage Note_Press/includes


 */





/**


 * Define the internationalization functionality.


 *


 * Loads and defines the internationalization files for this plugin


 * so that it is ready for translation.


 *


 * @since      1.0.0


 * @package    Note_Press


 * @subpackage Note_Press/includes


 * @author     Rod Kinnison <postmaster@datainterlock.com>


 */


class Note_Press_i18n {








	/**


	 * Load the plugin text domain for translation.


	 *


	 * @since    1.0.0


	 */


	public function load_plugin_textdomain() {





		load_plugin_textdomain(


			'Note_Press',


			false,


			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'


		);





	}











}


