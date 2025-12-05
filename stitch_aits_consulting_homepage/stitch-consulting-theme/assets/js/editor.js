/**
 * Stitch Consulting Theme - Editor Script
 * Gutenberg block editor customizations
 */

const { registerBlockStyle, unregisterBlockStyle } = wp.blocks;
const { dispatch } = wp.data;

/**
 * Register custom block styles
 */
document.addEventListener('DOMContentLoaded', function() {
	// Unregister default block styles if needed
	// unregisterBlockStyle('core/button', 'default');
	
	// Register custom button styles
	try {
		registerBlockStyle('core/button', {
			name: 'primary',
			label: 'Primary',
			isDefault: true,
		});

		registerBlockStyle('core/button', {
			name: 'secondary',
			label: 'Secondary',
		});

		registerBlockStyle('core/button', {
			name: 'outline',
			label: 'Outline',
		});
	} catch (e) {
		console.warn('Block styles already registered:', e);
	}
});

/**
 * Editor enhancements
 */
wp.domReady(function() {
	// Hide core block patterns
	wp.blocks.unregisterBlockType('core/legacy-widget');
	
	// Add editor-specific features
	console.log('Stitch Consulting Theme editor initialized');
});
