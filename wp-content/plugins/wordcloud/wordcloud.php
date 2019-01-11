<?php
/*
Plugin Name: ワードクラウド
Plugin URI: 
Description: ワードクラウドでワードプレスの記事の単語頻出度を可視化するためのプラグインです。
Author: 伊井　勝利
Version: 0.2.0
Author URI: https://tsukikoh.com/
Text Domain: wordcloud
Domain Path: /languages

原作：
https://wordpress.org/plugins/shareadraft/
Share a Draft
By Nikolay Bachiyski, Automattic
*/
$scnum = 1;

if ( ! class_exists( 'Word_Cloud' ) ) :
	class Word_Cloud {
		var $admin_options_name = 'WordCloud_options';
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
			load_plugin_textdomain( 'wordcloud', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

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
			add_submenu_page( 'edit.php', __( 'ワードクラウド', 'wordcloud' ), __( 'ワードクラウド', 'wordcloud' ),
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
				$p = get_post( $params['post_id'] );
				if ( ! $p ) {
					return __( 'そのような投稿はありません！', 'wordcloud' );
				}
				if ( 'publish' === get_post_status( $p ) ) {
					return __( '投稿は公開されています！', 'wordcloudt' );
				}
				if ( ! current_user_can( 'edit_post', $p->ID ) ) {
					return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'wordcloud' );
				}
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
						return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'wordcloud' );
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
						return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'wordcloud' );
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
			$unpublished_statuses = array( 'pending', 'draft', 'future', 'private' );
			$my_unpublished = get_posts( array(
				'post_status' => $unpublished_statuses,
				'author' => $current_user->ID,
				// some environments, like WordPress.com hook on those filters
				// for an extra caching layer
				'suppress_filters' => false,
			) );
			$others_unpublished = get_posts( array(
				'post_status' => $unpublished_statuses,
				'author' => -$current_user->ID,
				'suppress_filters' => false,
				'perm' => 'editable',
			) );
			$draft_groups = array(
			array(
				'label' => __( '自分の下書き:', 'wordcloud' ),
				'posts' => $my_unpublished,
			),
			array(
				'label' => __( '他の投稿:', 'wordcloud' ),
				'posts' => $others_unpublished,
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

		function friendly_delta( $s ) {
			$m = (int) ( $s / MINUTE_IN_SECONDS );
			$h = (int) ( $s / HOUR_IN_SECONDS );
			$free_m = (int) ( ( $s - $h * HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
			$d = (int) ( $s / DAY_IN_SECONDS );
			$free_h = (int) ( ( $s - $d * DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
			if ( $m < 1 ) {
				$res = array();
			} elseif ( $h < 1 ) {
				$res = array( $m );
			} elseif ( $d < 1 ) {
				$res = array( $free_m, $h );
			} else {
				$res = array( $free_m, $free_h, $d );
			}
			$names = array();
			if ( isset( $res[0] ) ) {
				$names[] = sprintf( _n( '%d分', '%d分', $res[0], 'wordcloud' ), $res[0] );
			}
			if ( isset( $res[1] ) ) {
				$names[] = sprintf( _n( '%d時間', '%d時間', $res[1], 'wordcloud' ), $res[1] );
			}
			if ( isset( $res[2] ) ) {
				$names[] = sprintf( _n( '%d日', '%d日', $res[2], 'wordcloud' ), $res[2] );
			}
			return implode( '', array_reverse( $names ) );
		}

		function output_existing_menu_sub_admin_page() {
			$msg = '';
			if ( isset( $_POST['wordcloud_submit'] ) ) {
				check_admin_referer( 'wordcloud-new-share' );
				$msg = $this->process_new_share( $_POST );
			} elseif ( isset( $_POST['action'] ) && $_POST['action'] === 'extend' ) {
				check_admin_referer( 'wordcloud-extend' );
				$msg = $this->process_extend( $_POST );
			} elseif ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' ) {
				check_admin_referer( 'wordcloud-delete' );
				$msg = $this->process_delete( $_GET );
			}
			$draft_groups = $this->get_drafts();
	?>
	<div class="wrap">
		<h2><?php _e( 'ワードクラウド', 'wordcloud' ); ?></h2>
		<script>
			<?php if($_GET['load'] != "true") : ?>
			window.addEventListener('DOMContentLoaded', function() {
				disp();
			})
			<?php endif; ?>
			function disp(){
				//document.getElementById("reload").style = "display: none;";
				document.getElementById("loading1").innerHTML = "<img src='https://img.gifmagazine.net/gifmagazine/images/1258326/original.gif' width='100%'>";
				setTimeout("link();", 1500);
			}
			function link(){
				document.location='edit.php?page=wordcloud%2Fwordcloud.php&load=true';
			}
		</script>
		<input id="reload" type="button" value="再読み込み" onClick="disp();"> 
		<input id="reload" type="button" value="メディアライブラリに保存" onClick="pre();">
		<div id="loading1"></div>
<?php 	if ( $msg ) :?>
		<div id="message" class="updated fade" ><?php echo $msg; ?></div>
<?php 	endif;?>
		<?php
			$key = '8w1VJFg9uGfmZ6ZZv45HYkDSFOzECHPyB2ZsVGAY';
			$plain_text = wp_get_current_user() -> user_login;
			
			//openssl
			$c_t = openssl_encrypt($plain_text, 'AES-128-ECB', $key);
			//var_dump($plain_text, $c_t, $p_t);
		?>
		<!--<h1 style="color: red;">
			プラグインの調整中
		</h1>-->
		
		<?php
		if ($_GET['load'] == true) {
			$user = wp_get_current_user() -> user_login;
			$url = "https://101023.xyz/sql-test4.php?user_name=";
			$url .= $user;
			$link_url .= urlencode($url);
			
			//コンテキストを作成
			$context = stream_context_create(
				[
					"http"=>
					[
						"ignore_errors"=>true
					]
				]
			);
			
			$html = file_get_contents('https://wordcloud.notel.xyz/python/get3.py?data_url='.$link_url.'&key=DGNYOpRNVlAVlwCxAdsquw7wV5LVyeH7H2dG3rQy', false, $context);
			preg_match("/[0-9]{3}/", $http_response_header[0], $stcode);
			if((int)$stcode[0] == 522 ){
				echo "<h3>接続がタイムアウトしました。</h3>";
				echo "WordCloudの生成サーバーでエラーが発生しています。<br />";
				echo "<img src=\"http://anihonetwallpaper.com/image/2016/08/36321-LoveLive-NishikinoMaki-Android.jpg\" "
					. " style=\"height: 100vmin;\"/><br />";
			} else if((int)$stcode[0] != 200 ){
				echo "Status Code : ".$stcode[0]. "<br />";
				echo "不明なエラーが発生しています。<br />";
			} else {
				$domDocument = new DOMDocument();
				$domDocument->loadHTML($html);
				$xmlString = $domDocument->saveXML();
				$xmlObject = simplexml_load_string($xmlString);
				$array = json_decode(json_encode($xmlObject), true);
				$img = $array['head']['title'];
				echo "<div style=\"margin: 0 auto; text-align: center;\">";
				echo "<img src= \"$img\" style=\"height: 100vmin; width: 100vmin;\" />";
				echo "</div>";
				if ( current_user_can( 'administrator' ) ) {
					echo "<h3>デバッグモード</h3>";
					echo '<a href="https://wordcloud.notel.xyz/python/get3.py?data_url='.$link_url.'&key=DGNYOpRNVlAVlwCxAdsquw7wV5LVyeH7H2dG3rQy">画像</a><br />';
					echo "<a href=\"https://wc-vnc.notel.xyz/\">管理画面</a><br />";
					echo "IDは「notel-admin」、パスワードは「Qwerty1234!」です。";
				}
			}
		}
			?>
		<!--
		<span id="textSpan"></span>
		<table class="widefat">
			<thead>
			<tr>
				<th><?php _e( 'ID', 'wordcloud' ); ?></th>
				<th><?php _e( 'タイトル', 'wordcloud' ); ?></th>
				<th><?php _e( 'リンク', 'wordcloud' ); ?></th>
				<th><?php _e( '残り時間', 'wordcloud' ); ?></th>
				<th colspan="2" class="actions"><?php _e( '操作', 'wordcloud' ); ?></th>
			</tr>
			</thead>
			<tbody>
			-->
<?php
		$s = $this->get_shared();
foreach ( $s as $share ) :
	$p = get_post( $share['id'] );
	$url = get_bloginfo( 'url' ) . '/?p=' . $p->ID . '&wordcloud=' . $share['key'];
	//$url = str_replace('http://', 'https://', $url);
	//$esc_url = esc_url( $url )
	$friendly_delta = $this->friendly_delta( $share['expires'] - time() );
	$iso_expires = date_i18n( 'c', $share['expires'] );
?>
		<!--
<tr>
<td><?php echo $p->ID; ?></td>
<td><?php echo $p->post_title; ?></td>
<-- TODO: make the draft link selecatble >
<td><a href="<?php echo esc_url( $url ); ?>" target="_blank">開く</a>・
	<a href="#<?php if($scnum==""){$scnum=1;} echo $scnum; ?>" id="copy<?php echo $scnum; $scnum++; ?>">コピー</a>
	<script type="text/javascript" language="javascript">
		function execCopy(string){
  var temp = document.createElement('div');
  
  temp.appendChild(document.createElement('pre')).textContent = string;
  
  var s = temp.style;
  s.position = 'fixed';
  s.left = '-100%';
  
  document.body.appendChild(temp);
  document.getSelection().selectAllChildren(temp);
  
  var result = document.execCommand('copy');

  document.body.removeChild(temp);
  // true なら実行できている falseなら失敗か対応していないか
  return result;
}

var copy = document.getElementById('copy<?php echo $scnum - 1; ?>');

copy.onclick = function(){
  if(execCopy("<?php echo esc_url( $url ); ?>")){
	  var span = document.getElementById("textSpan");
    span.textContent = "ID<?php echo $share['id'] ?>の共有リンクをコピーしました。";
    var text = span.textConten
	//3000ミリ秒（3秒）後に関数「syori()」を呼び出す;
	setTimeout("syori()", 2000);
  }
  else {
    alert('このブラウザではコピーに対応していません');
  }
};
		function syori(){
	  var span = document.getElementById("textSpan");
    span.textContent = "";
    var text = span.textContent;
		}
	</script>
-->
	<?php /*
		$lineMessage = <<< EOM
ノートる（お遊び用で）、共有リンクを発行しましたので暇な時間に見てください。
%URL
EOM;

		$mailTitle = <<< EOM
ノートる（お遊び用で）、共有リンクを発行しました。
EOM;

		$mailMessage = <<< EOM
暇な時間に見てください。
URL: %URL
EOM;

		$lineMessage = str_replace('%URL', $url, $lineMessage);
		$mailTitle = str_replace('%URL', $url, $mailTitle);
		$mailMessage = str_replace('%URL', $url, $mailMessage);
		*/
	?><!--
	<div class="line-it-button" data-lang="ja" data-type="share-a" data-url="<?php echo urlencode( $lineMessage ); ?>" style="display: none;"></div>
 <script src="https://d.line-scdn.net/r/web/social-plugin/js/thirdparty/loader.min.js" async="async" defer="defer"></script>
	<a href="mailto:?subject=<?php echo urlencode( $mailTitle ); ?>&amp;body=<?php echo urlencode( $mailMessage ); ?>"><img src="https://icon-rainbow.com/i/icon_02440/icon_024400.svg" width="20px" /></a>
</td>
<td><time title="<?php echo $iso_expires; ?>" datetime="<?php echo $iso_expires; ?>"><?php echo $friendly_delta; ?></time></td>
<td class="actions">
	<a class="wordcloud-extend edit" id="wordcloud-extend-link-<?php echo $share['key']; ?>"
		href="javascript:wordcloud.toggle_extend( '<?php echo $share['key']; ?>' );">
			<?php _e( '延長', 'wordcloud' ); ?>
	</a>
	<form class="wordcloud-extend" id="wordcloud-extend-form-<?php echo $share['key']; ?>"
		action="" method="post">
		<input type="hidden" name="action" value="extend" />
		<input type="hidden" name="key" value="<?php echo $share['key']; ?>" />
<?php _e( '延長期間', 'wordcloud' );?>
<?php echo $this->tmpl_measure_select(); ?>
		<input type="submit" class="button" name="wordcloud_extend_submit"
			value="<?php echo esc_attr__( '追加', 'wordcloud' ); ?>"/>
		<a class="wordcloud-extend-cancel"
			href="javascript:wordcloud.cancel_extend( '<?php echo $share['key']; ?>' );">
			<?php _e( 'キャンセル', 'wordcloud' ); ?>
		</a>
		<?php wp_nonce_field( 'wordcloud-extend' ); ?>
	</form>
</td>
<td class="actions">
-->
<?php
	//$delete_url = 'edit.php?page=' . plugin_basename( __FILE__ ) . '&action=delete&key=' . $share['key'];
	//$nonced_delete_url = wp_nonce_url( $delete_url, 'wordcloud-delete' );
?><!--
	<a class="delete" href="<?php echo esc_url( $nonced_delete_url ); ?>"><?php _e( '削除', 'wordcloud' ); ?></a>
</td>
</tr>-
<?php 
		endforeach;
if ( empty( $s ) ) : 
?>
<tr>
<td colspan="5"><?php //_e( '共有しているノートはありません', 'wordcloud' ); ?></td>
</tr>
<?php
		endif;
?>
			</tbody>
		</table>
<!--
		<h3><?php //_e( 'ワードクラウドを作成', 'wordcloud' ); ?></h3>
		<form id="wordcloud-share" action="" method="post">
		<p>
			<select id="wordcloud-postid" name="post_id">
			<option value=""><?php //_e( 'ノートを選択', 'wordcloud' ); ?></option>
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
			<?php _e( '期間', 'wordcloud' ); ?>
			<?php echo $this->tmpl_measure_select(); ?>
			<input type="submit" class="button" name="wordcloud_submit"
				value="<?php echo esc_attr__( '共有する', 'wordcloud' ); ?>" />
		</p>
		<?php wp_nonce_field( 'wordcloud-new-share' ); ?>
		</form>
		</div>-->
<?php
		}

		function can_view( $post_id ) {
			if ( ! isset( $_GET['wordcloud'] ) || ! is_array( $this->admin_options ) ) {
				return false;
			}
			foreach ( $this->admin_options as $option ) {
				if ( ! is_array( $option ) || ! isset( $option['shared'] ) ) {
					continue;
				}
				$shares = $option['shared'];
				foreach ( $shares as $share ) {
					if ( $share['id'] === $post_id && $share['key'] === $_GET['wordcloud'] ) {
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
			$mins = __( '分', 'wordcloud' );
			$hours = __( '時間', 'wordcloud' );
			$days = __( '日', 'wordcloud' );
			$weeks = __( '週間', 'wordcloud' );
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
		a.wordcloud-extend, a.wordcloud-extend-cancel { display: none; }
		form.wordcloud-extend { white-space: nowrap; }
		form.wordcloud-extend, form.wordcloud-extend input, form.wordcloud-extend select { font-size: 11px; }
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
			$( 'form.wordcloud-extend' ).hide();
			$( 'a.wordcloud-extend' ).show();
			$( 'a.wordcloud-extend-cancel' ).show();
			$( 'a.wordcloud-extend-cancel' ).css( 'display', 'inline' );
		} );
		window.wordcloud = {
			toggle_extend: function( key ) {
				$( '#wordcloud-extend-form-'+key ).show();
				$( '#wordcloud-extend-link-'+key ).hide();
				$( '#wordcloud-extend-form-'+key+' input[name="expires"]' ).focus();
			},
			cancel_extend: function( key ) {
				$( '#wordcloud-extend-form-'+key ).hide();
				$( '#wordcloud-extend-link-'+key ).show();
			}
		};
	} )( jQuery );
	//]]>
	</script>
	<?php
		}
	}
endif;

if ( class_exists( 'Word_Cloud' ) ) {
	$__word_cloud = new Word_Cloud();
}
