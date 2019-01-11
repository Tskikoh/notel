<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sydney
 */

if ( ! is_user_logged_in() ) {
	$login_message = "ログイン";
	$login_url = wp_login_url('/');
	$register_message = "新規登録";
	$register_url = $login_url."&action=register";
} else {
	$login_message = "投稿一覧";
	$login_url = "/wp-admin/edit.php";
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
	<?php do_action('sydney_before_content'); ?>

	<div id="primary" class="content-area col-md-9">
		<main id="main" class="post-wrap" role="main">

		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php do_action('sydney_after_content'); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
