<?php
/**
 *
 * This template is used to show crawler options
 *
 * @package Actions\Crawler
 */

// phpcs:disable VariableAnalysis
// There are "undefined" variables here because they're defined in the code that includes this file as a template.

?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div>
<h2><?php echo __( 'Internal Links', 'wp-media-web-crawler' ); ?></h2>
	<?php if ( ! empty( $links ) ) : ?>
		<table class="form-table" role="presentation" style="border-collapse: collapse; width: 100%;">
			<tbody class="card">
				<?php foreach ( $links as $wpmedia_key => $wpmedia_page_link ) : ?>
					<tr style="border: 1px solid #ccc;">
						<th scope="row" style="border: 1px solid #ccc; padding: 10px;"><?php echo $wpmedia_key + 1; ?><?php echo esc_html__( '.', 'wp-media-web-crawler' ); ?></th>
						<td style="border: 1px solid #ccc; padding: 10px;"><a href="<?php echo esc_url( $wpmedia_page_link ); ?>" target="_blank"><?php echo esc_html( $wpmedia_page_link ); ?></a></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
