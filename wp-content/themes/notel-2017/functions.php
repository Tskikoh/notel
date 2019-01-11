<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style')
);
}

/* カスタムCSS */

function remove_page_title_prefix( $title = '' ) {
 if ( empty( $title )) return $title;
 $search[0] = '/^' . str_replace('%s', '(.*)', preg_quote(__('Protected: %s'), '/' )) . '$/';
 $search[1] = '/^' . str_replace('%s', '(.*)', preg_quote(__('Private: %s'), '/' )) . '$/';
	$answer = preg_replace( $search, '$1<span style="font-size: 70%; color: #666;">（<img src="//101023.xyz/key.svg" style="width: 15px;"/>非公開）</span>', $title );
	if( $title == "Protected:" || $title == "Private:" || $title == "非公開:" ){
		$answer = "[タイトル無し]<span style=\"font-size: 70%; color: #666;\">（<img src=\"//101023.xyz/key.svg\"  style=\"width: 15px;\"/>非公開）</span>";
	}
	if(!preg_match("/（非公開）/", $answer)) {
		//$answer .= "<span style=\"font-size: 70%; color: #f00;\">（公開されています）</span>";
	}
	return $answer;
}
add_filter( 'the_title', 'remove_page_title_prefix' );

function appthemes_add_quicktags() {
    ?>
    <script type="text/javascript">
    QTags.addButton('縦書き', '縦書き', '<div class="tategaki">', '</div>');
    QTags.addButton('右線（縦書き用）', '右線（縦書き用）', '<span class="right">', '</span>');
    QTags.addButton('左線（縦書き用）', '左線（縦書き用）', '<span class="left">', '</span>');
    QTags.addButton('ルビ範囲1', 'ルビ範囲', '<ruby>', '</ruby>');
    QTags.addButton('ふりがな2', '左付きにするルビ範囲（縦書き用）', '<ruby class="rbleft" id="rbleft">', '</ruby>');
    QTags.addButton('ふりがな1', 'ふりがな', '<rt>', '</rt>');
		
	var tmp1234 = document.getElementsByClassName("meta-box-sortables");
    var val1234="sikakuwaku";
    tmp1234[0].setAttribute("id",val1234);
		
	document.getElementById("sikakuwaku").style.display ="";
		
	var tmp12 = document.getElementsByClassName("update-nag");
    var val12="apudehuka";
    tmp12[0].setAttribute("id",val12);
		
	document.getElementById("apudehuka").style.display ="none";
    </script>
    <?php
}
add_action('admin_print_footer_scripts', 'appthemes_add_quicktags');

// オリジナルウィジェットを追加
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
function my_custom_dashboard_widgets() {
 global $wp_meta_boxes;
 wp_add_dashboard_widget('custom_help_widget', 'お知らせ', 'dashboard_text');
}
function dashboard_text() {
 $html = 'ノートるアプリ制作中';
 echo $html;
}

//ここ新規by中島
// オートフォーマット関連の無効化
add_action('init', function() {
	remove_filter('the_title', 'wptexturize');
	remove_filter('the_content', 'wptexturize');
	remove_filter('the_excerpt', 'wptexturize');
	remove_filter('the_title', 'wpautop');
	remove_filter('the_content', 'wpautop');
	remove_filter('the_excerpt', 'wpautop');
	remove_filter('the_editor_content', 'wp_richedit_pre');
	remove_filter('the_content', 'convert_chars');	//	convert_charsによる文字列変換をしない
	remove_filter('the_title'  , 'convert_chars');		//	タイトル
	remove_filter('the_excerpt', 'convert_chars');		//	抜粋
	remove_filter('comment_text', 'convert_chars');		//	コメント
});

// オートフォーマット関連の無効化 TinyMCE
add_filter('tiny_mce_before_init', function($init) {
	$init['wpautop'] = false;
	$init['apply_source_formatting'] = ture;
	return $init;
});