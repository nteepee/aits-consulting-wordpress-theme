<?php
/**
 * Stats Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$stats = isset( $attributes['stats'] ) ? $attributes['stats'] : [];
$columns = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
$gap = isset( $attributes['gap'] ) ? sanitize_text_field( $attributes['gap'] ) : '24px';

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';
?>

<div class="wp-block-stitch-stats <?php echo $block_classes; ?>" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr); gap: <?php echo esc_attr( $gap ); ?>; padding: 0;">
	<?php foreach ( $stats as $stat ) : ?>
		<?php
		$stat_value = isset( $stat['value'] ) ? sanitize_text_field( $stat['value'] ) : '';
		$stat_label = isset( $stat['label'] ) ? sanitize_text_field( $stat['label'] ) : '';
		$stat_icon = isset( $stat['icon'] ) ? sanitize_text_field( $stat['icon'] ) : '';
		?>
		<div class="wp-block-stitch-stats__stat" style="text-align: center; padding: 24px;">
			<?php if ( $stat_icon ) : ?>
				<div class="wp-block-stitch-stats__icon" style="font-family: 'Material Symbols Outlined'; font-size: 2.5rem; color: #195de6; margin-bottom: 12px;">
					<?php echo esc_html( $stat_icon ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $stat_value ) : ?>
				<div class="wp-block-stitch-stats__value" style="font-size: 2rem; font-weight: 900; color: #FFFFFF; margin-bottom: 8px;">
					<?php echo wp_kses_post( $stat_value ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $stat_label ) : ?>
				<div class="wp-block-stitch-stats__label" style="font-size: 0.95rem; color: #A3A3A3;">
					<?php echo wp_kses_post( $stat_label ); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
