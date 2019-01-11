<?php
/**
 * The front page template file
 *
 * If the user has selected a static page for their homepage, this is what will
 * appear.
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
//ここじゃない
if ( ! is_user_logged_in() ) {
	$login_message = "ログイン";
	$login_url = wp_login_url('/');
	$register_message = "新規登録";
	$register_url = $login_url."&action=register";
} else {
	$login_message = "投稿一覧あいうあいう";
	$user = wp_get_current_user() -> user_login;
	$login_url = "/author/".$user."/";
	$register_message = "ログアウト";
	$register_url = wp_logout_url('/');
}

get_header(); ?>

			<div class="login-menu">
				<table>
					<tr>
						<td>
							<a href="<?= $login_url; ?>"><?= $login_message; ?></a>
						</td>
						<?php if ( wp_is_mobile() ) : ?>
					</tr>
					<tr>
						<?php endif; ?>
						<td>
							<a href="<?= $register_url; ?>"><?= $register_message; ?></a>
						</td>
					</tr>
				</table>
			</div>

<div style="margin: 0 auto; max-width: 500px;">
	<h2 style="text-align:center;">〜よぬこへのお願い〜</h2>
	<li>ケンカはやめようね。</li>
	<li>荒野行動もやめようね。</li>
	<li>淫夢厨は帰って、どうぞ</li>
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
				border-color:#aaaaaa #444444 #444444 #aaaaaa;
			}
	@media screen and (min-width: 500px) {
		.tate {
			display: none;
		}
	}
	</style>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php // Show the selected frontpage content. */
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/page/content', 'front-page' );
			endwhile;
		else :
			get_template_part( 'template-parts/post/content', 'none' );
		endif; ?>

		<?php
		// Get each of our panels and show the post data.
		if ( 0 !== twentyseventeen_panel_count() || is_customize_preview() ) : // If we have pages to show.

			/**
			 * Filter number of front page sections in Twenty Seventeen.
			 *
			 * @since Twenty Seventeen 1.0
			 *
			 * @param int $num_sections Number of front page sections.
			 */
			$num_sections = apply_filters( 'twentyseventeen_front_page_sections', 4 );
			global $twentyseventeencounter;

			// Create a setting and control for each of the sections available in the theme.
			for ( $i = 1; $i < ( 1 + $num_sections ); $i++ ) {
				$twentyseventeencounter = $i;
				twentyseventeen_front_page_section( null, $i );
			}

	endif; // The if ( 0 !== twentyseventeen_panel_count() ) ends here. 
?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php 
get_footer();
