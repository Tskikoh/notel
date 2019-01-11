<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

function is_IE() {
    $ua = mb_strtolower($_SERVER['HTTP_USER_AGENT']);  //すべて小文字にしてユーザーエージェントを取得
    if (strpos($ua,'msie') !== false || strpos($ua,'trident') !== false) {
        return true;
    }
    return false;
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<?php get_template_part( 'template-parts/header/header', 'image' ); ?>

		<?php if ( has_nav_menu( 'top' ) ) : ?>
			<div class="navigation-top">
				<div class="wrap">
					<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
				</div><!-- .wrap -->
			</div><!-- .navigation-top -->
		<?php endif; ?>

	</header><!-- #masthead -->

	<?php

	/*
	 * If a regular post or page, and not the front page, show the featured image.
	 * Using get_queried_object_id() here since the $post global may not be set before a call to the_post().
	 */
	if ( ( is_single() || ( is_page() && ! twentyseventeen_is_frontpage() ) ) && has_post_thumbnail( get_queried_object_id() ) ) :
		echo '<div class="single-featured-image-header">';
		echo get_the_post_thumbnail( get_queried_object_id(), 'twentyseventeen-featured-image' );
		echo '</div><!-- .single-featured-image-header -->';
	endif;
	?>


<?php 
	if (is_IE()) {
		echo '<div class="ie_alert" id="ie_alert">';
		echo '<span class="title" >Internet Explorer、ダメ絶対！</span>';
		echo '<p>あなたが使用している <b>Internet Explorer</b> というブラウザはとても古いものです。</p>';
		echo '<p>言ってしまえば、百害あって一利なしの老害です。</p>';
		echo '<p>世界中で、多くのWEB制作者があなたの <b>Internet Explorer</b> のせいで苦悩しています。</p>';
		echo '<p class="br">　</p>';
		echo '<p>どうか、<b>最新のモダンブラウザ（EdgeやChrome）</b>をお使いください。</p>';
		echo '<a href="microsoft-edge:https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">';
		echo '<img src="https://upload.wikimedia.org/wikipedia/commons/d/d6/Microsoft_Edge_logo.svg" />';
		echo 'Edgeで開く</a><br />';
		echo '<a href="https://www.google.co.jp/chrome/index.html" target="_blank">';
		echo '<img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Google_Chrome_icon_%28September_2014%29.svg" />';
		echo 'Google Chromeをダウンロード</a><br /><br />';
		echo '<p><input type="button" name="B1" value="閉じる" onclick="msgdsp()" /></p>';
		echo '</div>';
	}
	?>
     <script>
	var result = document.cookie.indexOf('ie_alert');

	if(result !== -1) {
		document.getElementById("ie_alert").style.display = "none";
	}

	function msgdsp() {
		if( confirm("お使いのInternet Explorerは、当サイトでは動作保証対象外です。\n予期せぬ誤作動を引き起こす可能性があります。\n\n本当にこのまま続行しますか？") ) {
			document.getElementById("ie_alert").style.display = "none";
			document.cookie = 'ie_alert=none; max-age=600';
		}
	}
     </script>

	<div class="site-content-contain">
		<div id="content" class="site-content">
