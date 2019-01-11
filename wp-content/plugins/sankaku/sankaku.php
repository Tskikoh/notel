<?php
/*
Plugin Name: 三角
Plugin URI: 
Description: 三角形を計算するためのプラグインです。
Author: 髙野　力也
Version: 0.0.1
Author URI: https://notel.xyz/
Text Domain: sankaku
Domain Path: /languages
*/
$scnum = 1;

if ( ! class_exists( 'Sankaku' ) ) :
	class Sankaku {
		var $admin_options_name = 'Sankaku_options';
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
			load_plugin_textdomain( 'sankaku', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

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
			add_submenu_page( 'edit.php', __( '三角', 'sankaku' ), __( '三角', 'sankaku' ),
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
					return __( 'そのような投稿はありません！', 'sankaku' );
				}
				if ( 'publish' === get_post_status( $p ) ) {
					return __( '投稿は公開されています！', 'sankakut' );
				}
				if ( ! current_user_can( 'edit_post', $p->ID ) ) {
					return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'sankaku' );
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
						return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'sankaku' );
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
						return __( '申し訳ありませんが、編集できない投稿を共有することはできません。', 'sankaku' );
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
				'label' => __( '自分の下書き:', 'sankaku' ),
				'posts' => $my_unpublished,
			),
			array(
				'label' => __( '他の投稿:', 'sankaku' ),
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
				$names[] = sprintf( _n( '%d分', '%d分', $res[0], 'sankaku' ), $res[0] );
			}
			if ( isset( $res[1] ) ) {
				$names[] = sprintf( _n( '%d時間', '%d時間', $res[1], 'sankaku' ), $res[1] );
			}
			if ( isset( $res[2] ) ) {
				$names[] = sprintf( _n( '%d日', '%d日', $res[2], 'sankaku' ), $res[2] );
			}
			return implode( '', array_reverse( $names ) );
		}

    public function quicktags_add_button() {
        wp_enqueue_script('quicktags_' . $this->plugin_slug, plugins_url('script.js', __FILE__), array('quicktags'), $this->plugin_version);
    }

		function output_existing_menu_sub_admin_page() {
			$msg = '';
			if ( isset( $_POST['sankaku_submit'] ) ) {
				check_admin_referer( 'sankaku-new-share' );
				$msg = $this->process_new_share( $_POST );
			} elseif ( isset( $_POST['action'] ) && $_POST['action'] === 'extend' ) {
				check_admin_referer( 'sankaku-extend' );
				$msg = $this->process_extend( $_POST );
			} elseif ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' ) {
				check_admin_referer( 'sankaku-delete' );
				$msg = $this->process_delete( $_GET );
			}
			$draft_groups = $this->get_drafts();
	?>
	<div class="wrap">
		<h2><?php _e( '三角', 'sankaku' ); ?></h2>
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
		<script type="text/javascript">
            document.getElementById("select").value = "101";
            document.getElementById("yomikomi").style = "display: none;";
		</script>
    <style type="text/css">
		    	.myModal_popUp,
		input[name="myModal_switch"],
		#myModal_open + label ~ label {
		  display: none;
		}
		#myModal_open + label,
		#myModal_close-button + label {
		  cursor: pointer;
		}

		/*.myModal_popUp {
		  animation: fadeIn 1s ease 0s 1 normal;
		  -webkit-animation: fadeIn 1s ease 0s 1 normal;
		}*/
		#myModal_close-button + label{
		  animation: fadeIn 2s ease 0s 1 normal;
		  -webkit-animation: fadeIn 2s ease 0s 1 normal;
		}
		@keyframes fadeIn {
		  0% {opacity: 0;}
		  100% {opacity: 1;}
		}
		@-webkit-keyframes fadeIn {
		  0% {opacity: 0;}
		  100% {opacity: 1;}
		}

		.myModal_popUp {
		  background: #fff;
		  display: block;
		  width: 10%;
		  height: 10%;
		  position: fixed;
		  top: 50%;
		  left: 50%;
		  transform: translate(-50%,-50%);
		  -webkit-transform: translate(-50%,-50%);
		  -ms-transform: translate(-50%,-50%);
		  z-index: 998;
		}

		.myModal_popUp > .myModal_popUp-content {
		  width: calc(100% - 40px);
		  height: calc(100% - 20px - 44px );
		  padding: 10px 20px;
		  overflow-y: auto;
		  -webkit-overflow-scrolling:touch;
		}

		#myModal_close-overlay + label {
		  background: rgba(0, 0, 0, 0.70);
		  /*display: block;*/
		  width: 100%;
		  height: 100%;
		  position: fixed;
		  top: 0;
		  left: 0;
		  overflow: hidden;
		  white-space: nowrap;
		  text-indent: 100%;
		  z-index: 997;
		}

		#myModal_close-button + label {
		  display: block;
		  background: #fff;
		  text-align: center;
		  font-size: 25px;
		  line-height: 44px;
		  width: 90%;
		  height: 44px;
		  position: fixed;
		  bottom: 10%;
		  left: 5%;
		  z-index: 999;
		}
		#myModal_close-button + label::before {
		  content: '×';
		}
		#myModal_close-button + label::after {
		  content: 'CLOSE';
		  margin-left: 5px;
		  font-size: 80%;
		}

		@media (min-width: 768px) {
		.myModal_popUp {
		    width: 100px;
		    height: 100px;
		  }
		 .myModal_popUp > .myModal_popUp-content {
		    height: calc(100% - 20px);
		  }
		#myModal_close-button + label {
		    width: 44px;
		    height: 44px;
		    left: 50%;
		    top: 50%;
		    margin-left: 240px;
		    margin-top: -285px;
		    overflow: hidden;
		  }
		#myModal_close-button + label::after {
		    display: none;
		  }
		}
    </style>
		<?php if (empty($_POST["id"])) : ?>
	<section class="myModal">
	  <input id="myModal_open" type="radio" name="myModal_switch" />
	  <label for="myModal_open"></label>
	  <input id="myModal_close-overlay" type="radio" name="myModal_switch" />
	  <label for="myModal_close-overlay">オーバーレイで閉じる</label>
	  <input id="myModal_close-button" type="radio" name="myModal_switch" />
	  <label for="myModal_close-button"></label>
	  <div class="myModal_popUp" id="yomikomi">
	  <div class="myModal_popUp-content">
	  	<h1 id="h1" style="color: red;">読み込み中…</h1>
	  </div>
	 </div>
	</section>

    <form>
        <p>下のメニューから入力したい値を選択してください。</p>
        <select id="select" onchange="clickBtn()">
            <option value="101" selected>3辺a,b,cの長さを入力</option>
            <option value="102">2辺a,bの長さと高さhを入力（角度Cが鈍角）</option>
            <option value="103">2辺a,bの長さと高さhを入力（角度BかCが鈍角）</option>
            /*<option value="104">2辺b,cの長さと高さhを入力（角度BかCが鋭角）</option>
            <option value="105">2辺b,cの長さと高さhを入力（角度BかCが鈍角）</option>*/
            <option value="106">2辺a,bと角度Cの入力</option>
            <option value="107">辺aと高さhと角度Cの入力</option>
        </select>
    </form>

    <form id="101" src="edit.php?page=sankaku%2Fsankaku.php" method="POST">
    	<input type="hidden" name="id" value="101" />
        <p>1つ目のメニュー</p>
        <p>辺aの入力</p>
        <input type="text" name="1" placeholder="a" />
        <p>辺bの入力</p>
        <input type="text" name="2" placeholder="b" />
        <p>辺cの入力</p>
        <input type="text" name="3" placeholder="c" />
		<input type="submit" name="submit" value="送信" />
    </form>
        
    <form id="102" src="edit.php?page=sankaku%2Fsankaku.php" style="display: none;" method="POST">
    	<input type="hidden" name="id" value="102" />
        <p>2つ目のメニュー</p>
        <p>辺aの入力</p>
        <input type="text" name="1" placeholder="a" />
        <p>辺bの入力</p>
        <input type="text" name="2" placeholder="b" />
        <p>高さhの入力</p>
        <input type="text" name="3" placeholder="h" />
		<input type="submit" name="submit" value="送信" />
    </form>

    <form id="103" src="edit.php?page=sankaku%2Fsankaku.php" style="display: none;" method="POST">
    	<input type="hidden" name="id" value="103" />
        <p>3つ目のメニュー</p>
        <p>辺aの入力</p>
        <input type="text" name="1" placeholder="a" />
        <p>辺bの入力</p>
        <input type="text" name="2" placeholder="b" />
        <p>高さhの入力</p>
        <input type="text" name="3" placeholder="h" />
		<input type="submit" name="submit" value="送信" />
    </form>

    <form id="104" src="edit.php?page=sankaku%2Fsankaku.php" style="display: none;" method="POST">
    	<input type="hidden" name="id" value="104" />
        <p>4つ目のメニュー</p>
        <p>辺bの入力</p>
        <input type="text" name="1" placeholder="b" />
        <p>辺cの入力</p>
        <input type="text" name="2" placeholder="c" />
        <p>高さhの入力</p>
        <input type="text" name="3" placeholder="h" />
		<input type="submit" name="submit" value="送信" />
    </form>

    <form id="105" src="edit.php?page=sankaku%2Fsankaku.php" style="display: none;" method="POST">
    	<input type="hidden" name="id" value="105" />
        <p>5つ目のメニュー</p>
        <p>辺bの入力</p>
        <input type="text" name="1" placeholder="b" />
        <p>辺cの入力</p>
        <input type="text" name="2" placeholder="c" />
        <p>高さhの入力</p>
        <input type="text" name="3" placeholder="h" />
		<input type="submit" name="submit" value="送信" />
    </form>

    <form id="106" src="edit.php?page=sankaku%2Fsankaku.php" style="display: none;" method="POST">
    	<input type="hidden" name="id" value="106" />
        <p>6つ目のメニュー</p>
        <p>辺aの入力</p>
        <input type="text" name="1" placeholder="a" />
        <p>辺bの入力</p>
        <input type="text" name="2" placeholder="b" />
        <p>角度Cの入力</p>
        <input type="text" name="3" placeholder="C" />
		<input type="submit" name="submit" value="送信" />
    </form>

    <form id="107" src="edit.php?page=sankaku%2Fsankaku.php" style="display: none;" method="POST">
    	<input type="hidden" name="id" value="107" />
        <p>7つ目のメニュー</p>
        <p>辺aの入力</p>
        <input type="text" name="1" placeholder="a" />
        <p>高さhの入力</p>
        <input type="text" name="2" placeholder="h" />
        <p>角度Cの入力</p>
        <input type="text" name="3" placeholder="C" />
		<input type="submit" name="submit" value="送信" />
    </form>
    
    <?php //if分の分岐と終了
    elseif ($_POST["id"] == "101") : ?>
        <span>id:101の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
        
<?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$a = $_POST["1"];
	$b = $_POST["2"];
	$c = $_POST["3"];
	
	
	print "<br>";
	print "<br>";
	
	
	
	
	print "<br>";
	
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	//高さhの計算
	$h = $b * sin( deg2rad ($cosC));
	
	/*$h = round($h ,2);
	$A = round($A ,0);
	$B = round($B ,0);
	$C = round($C ,0);
	*/
	
	
	/*結果の出力
	print "$cosA<br>";
	
	print "$cosB<br>";
	
	print "$cosC<br>";
	
	print "$h<br>";
	
	
	*/
	$ax2 = ($c**2) - ($h**2);
	print "<br>";
	$ax = sqrt($ax2);
	
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a =  ($bx ,$by) ～ ($cx,$cy)<br>";

	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>

    <?php elseif ($_POST["id"] == "102") : ?>
        <span>id:102の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
        <?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$a = $_POST["1"];
	$b = $_POST["2"];
	$h = $_POST["3"];
	
	
	print "<br>";
	print "<br>";
	print "<br>";
	
	
	print "<br>";
	//辺Cの計算過程　１
	$a1 = ($b * $b) - ($h * $h);
	print "<br>";
	//辺Cの計算過程　２
	$c = sqrt($a1);
	print "<br>";
	//辺cの計算過程　３
	$a2 = $a - $a1;
	//辺cの計算過程　４
	$c = ($a2**2) + ($h**2);
	$c = sqrt($c);
	print "<br>";
	
	
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	//高さhの計算
	$h = $b * sin( deg2rad ($cosC));
	
	/*$h = round($h ,2);
	$A = round($A ,0);
	$B = round($B ,0);
	$C = round($C ,0);
	*/
	
	
	/*結果の出力
	print "$cosA<br>";
	
	print "$cosB<br>";
	
	print "$cosC<br>";
	
	print "$h<br>";
	
	*/
	$ax2 = ($c**2) - ($h**2);
	print "<br>";
	$ax = sqrt($ax2);
	
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a = ($bx ,$by) ～ ($cx ,$cy)<br>";
	
	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>

    <?php elseif ($_POST["id"] == "103") : ?>
        <span>id:103の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
		<?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$a = $_POST["1"];
	$b = $_POST["2"];
	$h = $_POST["3"];
	
	
	print "$a<br>送られてきた\$aの値<br>";
	print "$b<br>送られてきた\$bの値<br>";
	print "$h<br>送られてきた\$hの値<br>";
	
	
	print "<br>";
	//辺Cの計算過程　１
	$a1 = ($b * $b) - ($h * $h);
	print "<br>";
	//辺Cの計算過程　２
	$c = sqrt($a1);
	print "<br>";
	//辺cの計算過程　３
	$a2 = $a + $a1;
	//辺cの計算過程　４
	$c = ($a2**2) + ($h**2);
	$c = sqrt($c);
	
	
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	//高さhの計算
	$h = $b * sin( deg2rad ($cosC));
	
	/*$h = round($h ,2);
	$A = round($A ,0);
	$B = round($B ,0);
	$C = round($C ,0);
	*/
	
	
	//結果の出力
	print "$cosA<br>";
	
	print "$cosB<br>";
	
	print "$cosC<br>";
	
	print "$h<br>";
	
	$ax2 = ($c**2) - ($h**2);
	print "<br>";
	$ax = sqrt($ax2);
	
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a =  ($bx ,$by) ～ ($cx,$cy)<br>";
	
	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>
        
        
        
        <?php elseif ($_POST["id"] == "104") : ?>
        <span>id:104の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
		<?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$b = $_POST["1"];
	$c = $_POST["2"];
	$h = $_POST["3"];
	
	
	print "<br>";
	print "<br>";
	//print "<br>";
	
	
	print "<br>";
	//辺Aの計算過程　１
	$b1 = ($c * $c) - ($h * $h);
	print "<br>";
	//辺Aの計算過程　２
	$b2 = ($b * $b) - ($b1 * $b1);
	print "<br>";
	//辺Aの計算過程　３
	$a = $b2 + ($h * $h);
	//辺Aの計算過程　４
	$a = sqrt($a);
	print "<br>";
	
	
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	//高さhの計算
	//$h = $b * sin( deg2rad ($cosC));
	
	/*$h = round($h ,2);
	$A = round($A ,0);
	$B = round($B ,0);
	$C = round($C ,0);
	*/
	
	
	//結果の出力
	//print "$cosA<br>";
	
	//print "$cosB<br>";
	
	//print "$cosC<br>";
	
	//print "$h<br>";
	
	$ax2 = ($c**2) - ($h**2);
	//print "<br>";
	$ax = sqrt($ax2);
	
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a =  ($bx ,$by) ～ ($cx,$cy)<br>";
	
		
		
	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>

	<?php elseif ($_POST["id"] == "105") : ?>
        <span>id:105の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
		<?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$b = $_POST["1"];
	$c = $_POST["2"];
	$h = $_POST["3"];
		
	
	print "<br>";
	print "<br>";
	print "<br>";
	
	
	print "<br>";
	//辺Aの計算過程　１
	$b1 = ($c * $c) - ($h * $h);
	print "<br>";
	//辺Aの計算過程　２
	$b2 = ($b1 * $b1) - ($b * $b);
	print "<br>";
	//辺Aの計算過程　３
	$a = $b2 + ($h * $h);
	//辺Aの計算過程　４
	$a = sqrt($a);
	print "<br>";
	
	
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	//高さhの計算
	//$h = $b * sin( deg2rad ($cosC));
	
	/*$h = round($h ,2);
	$A = round($A ,0);
	$B = round($B ,0);
	$C = round($C ,0);
	*/
	
	
	//結果の出力
	//print "$cosA<br>";
	
	//print "$cosB<br>";
	
	//print "$cosC<br>";
	
	//print "$h<br>";
	
	$ax2 = ($c**2) - ($h**2);
	print "<br>";
	$ax = sqrt($ax2);
	
	//print "$z5";
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a =  ($bx ,$by) ～ ($cx,$cy)<br>";
		
		
		
	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>

<?php elseif ($_POST["id"] == "106") : ?>
        <span>id:106の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
		<?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$a = $_POST["1"];
	$b = $_POST["2"];
	$C = $_POST["3"];
	
	
	print "$a<br>";
	
	
	print "$b<br>";
	print "$C<br>";
	
	//cosCの計算
	$cosC = $a / $b;
	
	
	//高さhの計算
	$h = $b * sin( deg2rad ($cosC));
	
	//条件分岐
	if($C < 90){
		
		$a1 = ($b**2) - ($h**2);
		//$a1 = sqrt($c);
		$a2 = $a - $a1;
		$c = ($h**2) + ($a2**2);
		$c = sqrt($c);
		
	}else{
		
		$a1 = ($b**2) - ($h**2);
		//$a1 = sqrt($c);
		$a2 = $a + $a1;
		$c = ($h**2) + ($a2**2);
		$c = sqrt($c);
	}
	
	
	print "<br>";
	
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	//$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	
	//高さhの計算
	$h = $b * sin( deg2rad ($cosC));
	
	/*$h = round($h ,2);
	$A = round($A ,0);
	$B = round($B ,0);
	$C = round($C ,0);
	*/
	
	
	//結果の出力
	//print "$cosA<br>";
	
	//print "$cosB<br>";
	
	//print "$cosC<br>";
	
	//print "$h<br>";
	
	
	$ax2 = ($c**2) - ($h**2);
	//print "$ax<br>";
	$ax = sqrt($ax2);
	
	//print "$z5";
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a = ($bx ,$by) ～ ($cx,$cy)<br>";
		
		
		
	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>

<?php elseif ($_POST["id"] == "107") : ?>
        <span>id:107の中身は「<?php echo $_POST["1"]; ?>」と「<?php echo $_POST["2"]; ?>」と「<?php echo $_POST["3"]; ?>」です。</span><br />
		<?php
	if (empty($_POST["1"]) || empty($_POST["2"]) || empty($_POST["3"])) { ?>
		<h1>値が入っていないか、「0」の箇所があります。</h1>
	<?php } else {
	//送られてきた値を代入
	$a = $_POST["1"];
	$h = $_POST["2"];
	$C = $_POST["3"];
		
	/*<?php
	//送られてきた値を代入
	$a = $_POST["a"];
	$h = $_POST["h"];
	$C = $_POST["C"];*/
	
	
	print "$a<br>";
	print "$h<br>";
	print "$C<br>";
	
	
	print "<br>";
	
	$b = $a * cos( deg2rad ($C));
	
	
	$a1 = ($b**2) - ($h**2);
	
	//条件分岐
	if($C <=90){
		$a2 = $a - $a1;
		$c = ($a2**2) + ($h**2);
		$c = sqrt($c);
	}else{
		$a2 = $a + $a1;
		$c = ($a2**2) + ($h**2);
		$c = sqrt($c);
	}
	//cosAの計算
	$cosA = (($b * $b) * ($c * $c) - ($a * $a)) / (2 * $b * $c);
	
	
	
	//cosBの計算
	$cosB = (($a * $a) * ($c * $c) - ($b * $b)) / (2 * $a * $c);
	
	
	
	//cosCの計算
	$cosC = (($a * $a) * ($b * $b) - ($c * $c)) / (2 * $a * $b);
	
	
	//高さhの計算
	//$h = $b * sin( deg2rad ($cosC));
	
	/*
	  $h = round($h ,2);
	  $A = round($A ,0);
	  $B = round($B ,0);
	  $C = round($C ,0);
	*/
	
	
	//結果の出力
	//print "$cosA<br>";
	
	//print "$cosB<br>";
	
	//print "$cosC<br>";
	
	//print "$h<br>";
	
	
	$ax2 = ($c**2) - ($h**2);
	//print "$ax<br>";
	$ax = sqrt($ax2);
	
	//print "$z5";
	
	//座標用の変数z1~z6
	$bx = 0;
	$by = 0;
	$cx = $a;
	$cy = 0;
	$ay = $h;
	
	
	print "辺c = ($bx ,$by) ～ ($ax ,$ay)<br>";
	print "辺b = ($ax ,$ay) ～ ($cx ,$cy)<br>";
	print "辺a = ($bx ,$by) ～ ($cx,$cy)<br>";	
		
		
	} //空の時のif文終了
?>
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>

    <?php else: ?>
    	<h1>存在しない送信先だよ＾〜</h1>
		
        <a href="edit.php?page=sankaku%2Fsankaku.php">戻る</a>


    <?php endif; ?>

		<!--
		<span id="textSpan"></span>
		<table class="widefat">
			<thead>
			<tr>
				<th><?php _e( 'ID', 'sankaku' ); ?></th>
				<th><?php _e( 'タイトル', 'sankaku' ); ?></th>
				<th><?php _e( 'リンク', 'sankaku' ); ?></th>
				<th><?php _e( '残り時間', 'sankaku' ); ?></th>
				<th colspan="2" class="actions"><?php _e( '操作', 'sankaku' ); ?></th>
			</tr>
			</thead>
			<tbody>
			-->
<?php
		$s = $this->get_shared();
foreach ( $s as $share ) :
	$p = get_post( $share['id'] );
	$url = get_bloginfo( 'url' ) . '/?p=' . $p->ID . '&sankaku=' . $share['key'];
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
	<a class="sankaku-extend edit" id="sankaku-extend-link-<?php echo $share['key']; ?>"
		href="javascript:sankaku.toggle_extend( '<?php echo $share['key']; ?>' );">
			<?php _e( '延長', 'sankaku' ); ?>
	</a>
	<form class="sankaku-extend" id="sankaku-extend-form-<?php echo $share['key']; ?>"
		action="" method="post">
		<input type="hidden" name="action" value="extend" />
		<input type="hidden" name="key" value="<?php echo $share['key']; ?>" />
<?php _e( '延長期間', 'sankaku' );?>
<?php echo $this->tmpl_measure_select(); ?>
		<input type="submit" class="button" name="sankaku_extend_submit"
			value="<?php echo esc_attr__( '追加', 'sankaku' ); ?>"/>
		<a class="sankaku-extend-cancel"
			href="javascript:sankaku.cancel_extend( '<?php echo $share['key']; ?>' );">
			<?php _e( 'キャンセル', 'sankaku' ); ?>
		</a>
		<?php wp_nonce_field( 'sankaku-extend' ); ?>
	</form>
</td>
<td class="actions">
-->
<?php
	//$delete_url = 'edit.php?page=' . plugin_basename( __FILE__ ) . '&action=delete&key=' . $share['key'];
	//$nonced_delete_url = wp_nonce_url( $delete_url, 'sankaku-delete' );
?><!--
	<a class="delete" href="<?php echo esc_url( $nonced_delete_url ); ?>"><?php _e( '削除', 'sankaku' ); ?></a>
</td>
</tr>-
<?php 
		endforeach;
if ( empty( $s ) ) : 
?>
<tr>
<td colspan="5"><?php //_e( '共有しているノートはありません', 'sankaku' ); ?></td>
</tr>
<?php
		endif;
?>
			</tbody>
		</table>
<!--
		<h3><?php //_e( '三角を作成', 'sankaku' ); ?></h3>
		<form id="sankaku-share" action="" method="post">
		<p>
			<select id="sankaku-postid" name="post_id">
			<option value=""><?php //_e( 'ノートを選択', 'sankaku' ); ?></option>
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
			<?php _e( '期間', 'sankaku' ); ?>
			<?php echo $this->tmpl_measure_select(); ?>
			<input type="submit" class="button" name="sankaku_submit"
				value="<?php echo esc_attr__( '共有する', 'sankaku' ); ?>" />
		</p>
		<?php wp_nonce_field( 'sankaku-new-share' ); ?>
		</form>
		</div>-->
<?php
		}

		function can_view( $post_id ) {
			if ( ! isset( $_GET['sankaku'] ) || ! is_array( $this->admin_options ) ) {
				return false;
			}
			foreach ( $this->admin_options as $option ) {
				if ( ! is_array( $option ) || ! isset( $option['shared'] ) ) {
					continue;
				}
				$shares = $option['shared'];
				foreach ( $shares as $share ) {
					if ( $share['id'] === $post_id && $share['key'] === $_GET['sankaku'] ) {
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
			$mins = __( '分', 'sankaku' );
			$hours = __( '時間', 'sankaku' );
			$days = __( '日', 'sankaku' );
			$weeks = __( '週間', 'sankaku' );
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
		a.sankaku-extend, a.sankaku-extend-cancel { display: none; }
		form.sankaku-extend { white-space: nowrap; }
		form.sankaku-extend, form.sankaku-extend input, form.sankaku-extend select { font-size: 11px; }
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
			$( 'form.sankaku-extend' ).hide();
			$( 'a.sankaku-extend' ).show();
			$( 'a.sankaku-extend-cancel' ).show();
			$( 'a.sankaku-extend-cancel' ).css( 'display', 'inline' );
		} );
		window.sankaku = {
			toggle_extend: function( key ) {
				$( '#sankaku-extend-form-'+key ).show();
				$( '#sankaku-extend-link-'+key ).hide();
				$( '#sankaku-extend-form-'+key+' input[name="expires"]' ).focus();
			},
			cancel_extend: function( key ) {
				$( '#sankaku-extend-form-'+key ).hide();
				$( '#sankaku-extend-link-'+key ).show();
			}
		};
	} )( jQuery );
	//]]>
	</script>
	<?php
		}
	}
endif;

if ( class_exists( 'Sankaku' ) ) {
	$__word_cloud = new Sankaku();
}
