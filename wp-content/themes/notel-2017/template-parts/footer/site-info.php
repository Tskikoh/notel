<?php
/**
 * Displays footer site info
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

$then = 2018;
$now = date('Y');
if ($then < $now) {
	$year = $then.'–'.$now;
} else {
	$year = $then;
}

?>
<div class="site-info">
	<?php
	if ( function_exists( 'the_privacy_policy_link' ) ) {
		the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
	}
	?>
	<?php echo "(c)$year ノートPC作りました ・"; ?>
	<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentyseventeen' ) ); ?>" class="imprint">
		<?php printf( __( '%s（ドヤ顔）', 'twentyseventeen' ), 'WordPress' ); ?>
	</a>
</div><!-- .site-info -->
