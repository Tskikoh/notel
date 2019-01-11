<?php
/*
Plugin Name: h2vR for WordPress
Plugin URI: http://jisakupc-technical.info/web-survice/wordpress/4448/
Description: 記事を縦書き表示する支援プラグインです。h2vR.jsとh2vR.cssをheadタグに実装します。ショートコードh2vrにより縦書き表示する箇所を指定可能。
Author: http://jisakupc-technical.info/
Version: 1.0.1
Author URI: http://jisakupc-technical.info/
*/

/*●h2vR.jsとh2vR.cssをheadタグに追加*/
function add_h2vr_to_wp_head (){
  $url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__), "", plugin_basename(__FILE__));

  echo '<link rel="stylesheet" href="'.$url.'h2vR.css" />'."\n".
       '<script src="'.$url.'h2vR.js"></script>'."\n".
       '<script src="'.$url.'h2vR_extensions.js"></script>'."\n";
}
add_action ('wp_head', 'add_h2vr_to_wp_head', 10);

/*●h2vR用ショートコード*/
function shortcode_h2vr($atts, $content = null) {
  extract(shortcode_atts(array(
    'class'  => '',   // クラス
    'char'   => 0,   // 行あたりの文字数 (数値)
    'size'   => 0,   // 文字サイズ (数値)px
    'line'   => 0,   // 行間 (数値)em
    'width'  => 0,   // 領域の横幅 (数値)px
    'height' => 0,   // 領域の高さ (数値)px
    'option' => '',  // カスタムCSS
    'single' => 'no'
  ), $atts));
  
  $clazz = 'h2vr_0';
  $style = '';
  
  // 文字サイズが指定されている場合
  if ($char != 0) {
    /* 可変長 */
    $clazz = 'h2vr_'.$char;
  } else {
    /* 固定長 */
    // 横幅
    if ($width != 0) { $style .= 'width:'.$width.'px;'; }
    // 高さ
    if ($height != 0) { $style .= 'height:'.$height.'px;'; }
  }
  // 文字サイズ
  if ($size != 0) { $style .= 'font-size:'.$size.'px;'; }
  // 行間
  if ($line != 0) { $style .= 'line-height:'.$line.'em;'; }
  
  // 文字数固定時にスクロール対応
  if ($single == 'yes' && $char != 0) {
    $clazz.= ' h2vr_single';
    if ($width != 0) { $style .= 'width:'.$width.'px;'; }
    else { $style .= 'width:200px;';  }
  }
  
  // カスタムCSS
  $style .= $option;
  
  return do_shortcode('<div class="'.$clazz.' '.$class.'" style="'.$style.'">'.$content.'</div>');
}
add_shortcode('h2vr', 'shortcode_h2vr');


/*●h2vR用ショートコード(文字間スペース)*/
function shortcode_h2vr_space($atts, $content = null) {
  return do_shortcode('<div class="space">'.$content.'</div>');
}
add_shortcode('h2vr_space', 'shortcode_h2vr_space');

/*●h2vR用ショートコード(ルビ本体タグ)*/
function shortcode_h2vr_ruby($atts, $content = null) {
  return do_shortcode('<ruby>'.$content.'</ruby>');
}
add_shortcode('h2vr_ruby', 'shortcode_h2vr_ruby');

/*●h2vR用ショートコード(ルビタグ)*/
function shortcode_h2vr_rb($atts, $content = null) {
  return do_shortcode('<rb>'.$content.'</rb>');
}
add_shortcode('h2vr_rb', 'shortcode_h2vr_rb');

/*●h2vR用ショートコード(ルビふりがなタグ)*/
function shortcode_h2vr_rt($atts, $content = null) {
  return do_shortcode('<rt>'.$content.'</rt>');
}
add_shortcode('h2vr_rt', 'shortcode_h2vr_rt');

/*●h2vR用ショートコード(傍線タグ)*/
function shortcode_h2vr_bousen($atts, $content = null) {
  extract(shortcode_atts(array(
    'style'   =>'solid',   // 傍線のスタイル(solid, double, dashed, dotted)
  ), $atts));
  return do_shortcode('<span class="bousen_'.$style.'_h2vr">'.$content.'</span>');
}
add_shortcode('h2vr_bousen', 'shortcode_h2vr_bousen');

?>