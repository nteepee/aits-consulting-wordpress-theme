import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl
} from '@wordpress/components';
import './edit.css';

export default function Edit({ attributes, setAttributes }) {
	const {
		icon,
		title,
		description,
		linkText,
		linkUrl,
		variant
	} = attributes;

	const blockProps = useBlockProps({
		className: `wp-block-stitch-feature-card wp-block-stitch-feature-card--${variant}`
	});

	const materialIcons = [
		{ label: 'Star', value: 'star' },
		{ label: 'Check Circle', value: 'check_circle' },
		{ label: 'Lightning', value: 'flash_on' },
		{ label: 'Settings', value: 'settings' },
		{ label: 'Rocket', value: 'rocket_launch' },
		{ label: 'Shield', value: 'shield' },
		{ label: 'Target', value: 'target' },
		{ label: 'Light Bulb', value: 'lightbulb' },
		{ label: 'Code', value: 'code' },
		{ label: 'Package', value: 'packages' }
	];

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Feature Card Settings', 'stitch')} initialOpen={true}>
					<SelectControl
						label={__('Icon', 'stitch')}
						value={icon}
						options={materialIcons}
						onChange={(value) => setAttributes({ icon: value })}
					/>
					<TextControl
						label={__('Title', 'stitch')}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
					/>
					<TextControl
						label={__('Description', 'stitch')}
						value={description}
						onChange={(value) => setAttributes({ description: value })}
					/>
					<SelectControl
						label={__('Variant', 'stitch')}
						value={variant}
						options={[
							{ label: 'Default', value: 'default' },
							{ label: 'Bordered', value: 'bordered' }
						]}
						onChange={(value) => setAttributes({ variant: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Link Settings', 'stitch')} initialOpen={false}>
					<TextControl
						label={__('Link Text', 'stitch')}
						value={linkText}
						onChange={(value) => setAttributes({ linkText: value })}
					/>
					<TextControl
						label={__('Link URL', 'stitch')}
						value={linkUrl}
						onChange={(value) => setAttributes({ linkUrl: value })}
						type="url"
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps} style={{
				padding: '24px',
				backgroundColor: '#141414',
				border: variant === 'bordered' ? '1px solid #262626' : 'none',
				borderRadius: '12px',
				transition: 'all 0.3s ease'
			}}>
				<div className="wp-block-stitch-feature-card__icon" style={{
					fontSize: '2rem',
					marginBottom: '16px',
					fontFamily: '"Material Symbols Outlined"',
					color: '#195de6'
				}}>
					{icon}
				</div>

				<RichText
					tagName="h3"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Feature title', 'stitch')}
					className="wp-block-stitch-feature-card__title"
					style={{
						fontSize: '1.25rem',
						fontWeight: 700,
						marginBottom: '12px',
						color: '#FFFFFF'
					}}
				/>

				<RichText
					tagName="p"
					value={description}
					onChange={(value) => setAttributes({ description: value })}
					placeholder={__('Feature description', 'stitch')}
					className="wp-block-stitch-feature-card__description"
					style={{
						fontSize: '0.95rem',
						lineHeight: 1.6,
						marginBottom: '16px',
						color: '#A3A3A3'
					}}
				/>

				{linkText && linkUrl && (
					<a href={linkUrl} className="wp-block-stitch-feature-card__link" style={{
						color: '#195de6',
						textDecoration: 'none',
						fontWeight: 600,
						transition: 'all 0.3s ease'
					}}>
						{linkText} →
					</a>
				)}
			</div>
		</>
	);
}
