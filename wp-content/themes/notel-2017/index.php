<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */


if ( ! is_user_logged_in() ) {
	$message1 = "ログイン";
	$url1 = wp_login_url('/');
	$message2 = "新規登録";
	$url2 = $url1."&action=register";
} else {
	$message1 = "投稿一覧";
	$user = wp_get_current_user() -> user_login;
	$url1 = "/author/".$user."/";
	$message2 = "新規投稿";
	$url2 = "/wp-admin/post-new.php";
	$message3 = "プロフィール";
	$url3 = "/wp-admin/profile.php";
	$message4 = "ログアウト";
	$url4 = wp_logout_url('/');
}

get_header(); ?>

<header class="page-header" style="width: 100%;">
		<h2 class="page-title">
			<div class="login-menu">
				<table>
					<tr>
						<td>
							<a href="<?= $url1; ?>"><?= $message1; ?></a>
						</td>
						<?php if ( wp_is_mobile() ) : ?>
					</tr>
					<tr>
						<?php endif; ?>
						<td>
							<a href="<?= $url2; ?>"><?= $message2; ?></a>
						</td>
					</tr>
					<?php if ( is_user_logged_in() ) : ?>
					<tr>
						<td>
							<a href="<?= $url3; ?>"><?= $message3; ?></a>
						</td>
						<?php if ( wp_is_mobile() ) : ?>
					</tr>
					<tr>
						<?php endif; ?>
						<td>
							<a href="<?= $url4; ?>"><?= $message4; ?></a>
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</div>
		</h2>
	</header>
<div style="margin: 0 auto; width: 100%; max-width: 500px;">
	<h2 style="text-align:center;">〜ノートるとは〜</h2>
	<li>多機能なクラウドノートアプリ</li>
	<li>PCでノートをきれいにとりたい！</li>
	<li>頻出語句を抽出したい！</li>
	<!-- <li>漢文のノートをとるときレ点や上下点をつけたい場合は、
		つけたい文字の下にスペースを入力し、上付き文字や下付き文字をつけること。
	
	これをしない場合、文字が小さくなってしまいますのでご注意ください。</li> -->
	<br />
</div>
<div style="margin: 0 auto; text-align: center;">
	<img src="https://www.infocircus.jp/letsencrypt/seal/notel.xyz" /><br />
	このサイトは、Let's Encryptの証明書を利用し、暗号化しています。
</div>

<style type="text/css" >
				.login-menu {
					width: 100%;
					margin: 0 auto;
					max-width: 700px;
					margin-top: 30px;
					#vertical-align: middle;
				@media screen and (min-width: 320px) {
					margin-left: 100px;
					}
				<?php if ( wp_is_mobile() ) : ?>
					/* スマホ専用のCSS */
					width : 98% ;
					width : -webkit-calc(100% - 20px) ;
					width : calc(100% - 20px) ;
					max-width: 400px;
					margin: 10px　auto;
					border:2px solid;
					border-color:#aaa #444 #444 #aaa;
				<?php endif; ?>
				
			}
			.login-menu a{
				font-size:3em;
				display:block;
				width:300px;
				height:100px;
				line-height: 75px;
				padding-top:10px;
				padding-bottom:10px;
				margin: auto;
				text-align:center;
				border:2px solid;
				border-color:#aaa #444 #444 #aaa;
			}
	<?php if ( wp_is_mobile() ) : ?>
	/* スマホ専用のCSS */
	.login-menu a{
		width:100%;
		max-width: 400px;
		height:70px;
		border:0px;
		text-align: left;
		margin-left: 10px;
	}
	.login-menu a:before {
		content:"> ";
		color: #888;
	}
	/* スマホ専用のCSS - ここまで */
	<?php endif; ?>
	@media screen and (min-width: 500px) {
		.tate {
			display: none;
		}
	}
	</style>

<?php if( $_SERVER['REQUEST_URI'] != "/" ) { //ここから下は、トップページでは表示されません ?>

<div class="wrap">
	<?php if ( is_home() && ! is_front_page() ) : ?>
		<header class="page-header">
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		</header>
	<?php else : ?>
	<header class="page-header">
		<h2 class="page-title"><?php _e( 'Posts', 'twentyseventeen' ); ?></h2>
	</header>
	<?php endif; ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			if ( have_posts() ) :

				/* Start the Loop */
				while ( have_posts() ) : the_post();

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/post/content', get_post_format() );

				endwhile;

				the_posts_pagination( array(
					'prev_text' => twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '<span class="screen-reader-text">' . __( 'Previous page', 'twentyseventeen' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next page', 'twentyseventeen' ) . '</span>' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ),
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyseventeen' ) . ' </span>',
				) );

			else :

				get_template_part( 'template-parts/post/content', 'none' );

			endif;
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php } ?>

<?php get_footer();
