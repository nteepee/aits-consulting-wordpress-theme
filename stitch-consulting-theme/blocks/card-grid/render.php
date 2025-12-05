<?php
/**
 * Card Grid Block Render
 *
 * @var array  $attributes Block attributes
 * @var string $content    Block content
 * @var object $block      Block object
 */

$columns = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
$gap = isset( $attributes['gap'] ) ? sanitize_text_field( $attributes['gap'] ) : '24px';
$background_color = isset( $attributes['backgroundColor'] ) ? sanitize_text_field( $attributes['backgroundColor'] ) : 'transparent';

$block_classes = isset( $attributes['className'] ) ? esc_attr( $attributes['className'] ) : '';
?>

<div class="wp-block-stitch-card-grid <?php echo $block_classes; ?>" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr); gap: <?php echo esc_attr( $gap ); ?>; background-color: <?php echo esc_attr( $background_color ); ?>; padding: 0;">
	<?php echo $content; ?>
</div>
