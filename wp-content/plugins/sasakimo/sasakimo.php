<?php
/*
Plugin Name: 英単語帳作成
Plugin URI: 
Description: <span style="color: red;">絶対に更新するな</span>
Author: 	佐々木希歩
Version: 100.0
Author URI: https://app.tki.jp/
Text Domain: sasakimo
Domain Path: /languages

https://wordpress.org/plugins/shareadraft/
Share a Draft
By Nikolay Bachiyski, Automattic
*/
$scnum = 1;

if ( ! class_exists( 'Sasa_Kimo' ) ) :
	class Sasa_Kimo {
		var $admin_options_name = 'sasakimo_options';
		var $shared_post = null;

		function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		function init() {
			global $current_user;
			add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
			add_filter( 'the_posts', array( $this, 'the_posts_intercept' ) );
			add_filter( 'posts_results', array( $this, 'posts_results_intercept' ) );

			$this->admin_options = $this->get_admin_options();
			$this->admin_options = $this->clear_expired( $this->admin_options );
			$this->user_options = array();
			if ( $current_user->ID > 0 && isset( $this->admin_options[ $current_user->ID ] ) ) {
				$this->user_options = $this->admin_options[ $current_user->ID ];
			}
			$this->save_admin_options();
			load_plugin_textdomain( 'sasakimo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			if ( isset( $_GET['page'] ) && $_GET['page'] === plugin_basename( __FILE__ ) ) {
				$this->admin_page_init();
			}
		}

		function admin_page_init() {
			wp_enqueue_script( 'jquery' );
			add_action( 'admin_head', array( $this, 'print_admin_css' ) );
			add_action( 'admin_head', array( $this, 'print_admin_js' ) );
		}

		function get_admin_options() {
			$saved_options = get_option( $this->admin_options_name );
			return is_array( $saved_options )? $saved_options : array();
		}

		function save_admin_options() {
			global $current_user;
			if ( $current_user->ID > 0 ) {
				$this->admin_options[ $current_user->ID ] = $this->user_options;
			}
			update_option( $this->admin_options_name, $this->admin_options );
		}

		function clear_expired( $all_options ) {
			$all = array();
			foreach ( $all_options as $user_id => $options ) {
				$shared = array();
				if ( ! isset( $options['shared'] ) || ! is_array( $options['shared'] ) ) {
					continue;
				}
				foreach ( $options['shared'] as $share ) {
					if ( $share['expires'] < time() ) {
						continue;
					}
					$shared[] = $share;
				}
				$options['shared'] = $shared;
				$all[ $user_id ] = $options;
			}
			return $all;
		}

		function add_admin_pages() {
			add_submenu_page( 'edit.php', __( '英単語帳作成', 'sasakimo' ), __( '英単語帳作成', 'sasakimo' ),
			'edit_posts', __FILE__, array( $this, 'output_existing_menu_sub_admin_page' ) );
		}

		function calculate_seconds( $params ) {
			$exp = 60;
			$multiply = 60;
			if ( isset( $params['expires'] ) && ( $e = intval( $params['expires'] ) ) ) {
				$exp = $e;
			}
			$mults = array(
				'm' => MINUTE_IN_SECONDS,
				'h' => HOUR_IN_SECONDS,
				'd' => DAY_IN_SECONDS,
				'w' => WEEK_IN_SECONDS,
			);
			if ( isset( $params['measure'] ) && isset( $mults[ $params['measure'] ] ) ) {
				$multiply = $mults[ $params['measure'] ];
			}
			return $exp * $multiply;
		}

		function process_new_share( $params ) {
			global $current_user;
			if ( isset( $params['post_id'] ) ) {
				//print_r ($params);
				//echo '<br>';
				$p = get_post( $params['post_id'] );
				$content = get_the_content();
				//print get_post_field('post_id', 'post_content');
				$content = strip_tags($content, '<td>');
				$pattern = '/<td>(.+?)<\/td>/';
				preg_match_all($pattern, $content, $matches);
				//print_r($matches);
				return __('txtファイルを作成しました！', 'sasakimo');
				$this->user_options['shared'][] = array(
					'id' => $p->ID,
					'expires' => time() + $this->calculate_seconds( $params ),
					'key' => uniqid( 'baba' . $p->ID . '_' ),
				);
				$this->save_admin_options();
			}
		}

		function process_delete( $params ) {
			if ( ! isset( $params['key'] ) ||
			! isset( $this->user_options['shared'] ) ||
			! is_array( $this->user_options['shared'] ) ) {
				return '';
			}
			$shared = array();
			foreach ( $this->user_options['shared'] as $share ) {
				if ( $share['key'] === $params['key'] ) {
					if ( ! current_user_can( 'edit_post', $share['id'] ) ) {
						return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'sasakimo' );
					}
					continue;
				}
				$shared[] = $share;
			}
			$this->user_options['shared'] = $shared;
			$this->save_admin_options();
		}

		function process_extend( $params ) {
			if ( ! isset( $params['key'] ) ||
			! isset( $this->user_options['shared'] ) ||
			! is_array( $this->user_options['shared'] ) ) {
				return '';
			}
			$shared = array();
			foreach ( $this->user_options['shared'] as $share ) {
				if ( $share['key'] === $params['key'] ) {
					if ( ! current_user_can( 'edit_post', $share['id'] ) ) {
						return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'sasakimo' );
					}
					$share['expires'] += $this->calculate_seconds( $params );
				}
				$shared[] = $share;
			}
			$this->user_options['shared'] = $shared;
			$this->save_admin_options();
		}

		function get_drafts() {
			global $current_user;
			$unpublished_statuses = array( 'pending', 'future', 'private');
			$my_unpublished = get_posts( array(
				//'post_status' => $unpublished_statuses,
				'author' => $current_user->ID,
				// some environments, like WordPress.com hook on those filters
				// for an extra caching layer
			) );
			$draft_groups = array(
			array(
				'label' => __( '自分の下書き:', 'sasakimo' ),
				'posts' => $my_unpublished,
			),
			);
			return $draft_groups;
		}

		function get_shared() {
			if ( ! isset( $this->user_options['shared'] ) || ! is_array( $this->user_options['shared'] ) ) {
				return array();
			}
			return $this->user_options['shared'];
		}

		function output_existing_menu_sub_admin_page() {
			$msg = '';
			if ( isset( $_POST['sasakimo_submit'] ) ) {
				check_admin_referer( 'sasakimo-new-share' );
				//print_r($_POST);	
				//echo "<br>";	
				$msg = $this->process_new_share( $_POST );
				//echo $msg;	
				//echo "<br>";	
			} elseif ( isset( $_POST['action'] ) && $_POST['action'] === 'extend' ) {
				check_admin_referer( 'sasakimo-extend' );
				$msg = $this->process_extend( $_POST );
			} elseif ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' ) {
				check_admin_referer( 'sasakimo-delete' );
				$msg = $this->process_delete( $_GET );
			}
			$draft_groups = $this->get_drafts();
			//print_r($draft_groups);		
	?>

		<h3><?php _e( '英単語帳を作成', 'sasakimo' ); ?></h3>
		<form id="sasakimo-share" action="" method="post">
		<p>
			<select id="sasakimo-postid" name="post_id">
			<option value=""><?php _e( 'ノートを選択', 'sasakimo' ); ?></option>
<?php
foreach ( $draft_groups as $draft_group ) :
	if ( $draft_group['posts'] ) :
?>
	<option value="" disabled="disabled"></option>
	<option value="" disabled="disabled"><?php echo $draft_group['label']; ?></option>
<?php
foreach ( $draft_group['posts'] as $draft ) :
	if ( empty( $draft->post_title ) ) {
		continue;
	}
?>
<option value="<?php echo $draft->ID?>"><?php echo esc_html( $draft->post_title ); ?></option>
<?php
		endforeach;
endif;
		endforeach;
?>
			</select>
		</p>
		<p>
			<input type="submit" class="button" name="sasakimo_submit"
				value="<?php echo esc_attr__( '単語帳作成！', 'sasakimo' ); ?>" />
		</p>
		<?php wp_nonce_field( 'sasakimo-new-share' ); ?>
		</form>
		</div>
<?php
		}

		function can_view( $post_id ) {
			if ( ! isset( $_GET['sasakimo'] ) || ! is_array( $this->admin_options ) ) {
				return false;
			}
			foreach ( $this->admin_options as $option ) {
				if ( ! is_array( $option ) || ! isset( $option['shared'] ) ) {
					continue;
				}
				$shares = $option['shared'];
				foreach ( $shares as $share ) {
					if ( $share['id'] === $post_id && $share['key'] === $_GET['sasakimo'] ) {
						return true;
					}
				}
			}
			return false;
		}

		function posts_results_intercept( $posts ) {
			if ( 1 !== count( $posts ) ) {
				return $posts;
			}
			$post = $posts[0];
			$status = get_post_status( $post );
			if ( 'publish' !== $status && $this->can_view( $post->ID ) ) {
				$this->shared_post = $post;
			}
			return $posts;
		}

		function the_posts_intercept( $posts ) {
			if ( empty( $posts ) && ! is_null( $this->shared_post ) ) {
				return array( $this->shared_post );
			} else {
				$this->shared_post = null;
				return $posts;
			}
		}

		function tmpl_measure_select() {
			$mins = __( '分', 'sasakimo' );
			$hours = __( '時間', 'sasakimo' );
			$days = __( '日', 'sasakimo' );
			$weeks = __( '週間', 'sasakimo' );
			return <<<SELECT
			<input name="expires" type="text" value="1" size="2"/>
			<select name="measure">
				<option value="m">$mins</option>
				<option value="h">$hours</option>
				<option value="d">$days</option>
				<option value="w" selected>$weeks</option>
			</select>
SELECT;
		}

		function print_admin_css() {
	?>
	<style type="text/css">
		a.sasakimo-extend, a.sasakimo-extend-cancel { display: none; }
		form.sasakimo-extend { white-space: nowrap; }
		form.sasakimo-extend, form.sasakimo-extend input, form.sasakimo-extend select { font-size: 11px; }
		th.actions, td.actions { text-align: center; }
	</style>
	<?php
		}

		function print_admin_js() {
	?>
	<script type="text/javascript">
	//<![CDATA[
	( function( $ ) {
		$( function() {
			$( 'form.sasakimo-extend' ).hide();
			$( 'a.sasakimo-extend' ).show();
			$( 'a.sasakimo-extend-cancel' ).show();
			$( 'a.sasakimo-extend-cancel' ).css( 'display', 'inline' );
		} );
		window.sasakimo = {
			toggle_extend: function( key ) {
				$( '#sasakimo-extend-form-'+key ).show();
				$( '#sasakimo-extend-link-'+key ).hide();
				$( '#sasakimo-extend-form-'+key+' input[name="expires"]' ).focus();
			},
			cancel_extend: function( key ) {
				$( '#sasakimo-extend-form-'+key ).hide();
				$( '#sasakimo-extend-link-'+key ).show();
			}
		};
	} )( jQuery );
	//]]>
	</script>
	<?php
		}
	}
endif;

if ( class_exists( 'Sasa_Kimo' ) ) {
	$__Sasa_Kimo = new Sasa_Kimo();
}

