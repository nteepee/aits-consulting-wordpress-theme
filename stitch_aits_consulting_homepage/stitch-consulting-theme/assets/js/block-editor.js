/**
 * Stitch Consulting Theme - Block Editor Script
 * Advanced block editor features and utilities
 */

(function() {
	'use strict';

	const wp = window.wp || {};
	const blocks = wp.blocks || {};
	const editor = wp.editor || {};
	const element = wp.element || {};
	const components = wp.components || {};

	/**
	 * Initialize block editor features
	 */
	wp.domReady(function() {
		initEditorSettings();
		initBlockFiltering();
		initCustomEditorStyles();
	});

	/**
	 * Configure editor settings
	 */
	function initEditorSettings() {
		// Get editor settings from theme
		const editorSettings = window.stitchTheme || {};

		// Configure max width
		wp.data.dispatch('core/editor').updateEditorSettings({
			maxWidth: 960,
		});
	}

	/**
	 * Filter allowed blocks
	 */
	function initBlockFiltering() {
		// Define allowed blocks
		const allowedBlocks = [
			'core/paragraph',
			'core/heading',
			'core/image',
			'core/gallery',
			'core/list',
			'core/quote',
			'core/button',
			'core/buttons',
			'core/columns',
			'core/column',
			'core/group',
			'core/spacer',
			'core/separator',
			'core/html',
			'core/code',
			'core/media-text',
		];

		// Custom Stitch blocks (will be added by Phase 02)
		// allowedBlocks.push('stitch/hero');
		// allowedBlocks.push('stitch/cta');
	}

	/**
	 * Apply custom editor styles
	 */
	function initCustomEditorStyles() {
		// Add custom styles for editor
		const style = document.createElement('style');
		style.textContent = `
			.editor-styles-wrapper {
				background-color: #0A0A0A;
				color: #FFFFFF;
			}
			
			.block-editor-writing-flow {
				font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
			}
			
			.wp-block {
				border-radius: 4px;
			}
			
			.wp-block:focus {
				outline: 2px solid #195de6;
			}
		`;
		
		document.head.appendChild(style);
	}

	/**
	 * Expose utilities to global scope
	 */
	window.StitchBlockEditor = {
		version: '1.0.0',
		allowedBlocks: [],
	};

})();
