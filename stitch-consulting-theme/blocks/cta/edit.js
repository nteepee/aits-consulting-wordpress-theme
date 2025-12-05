import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl
} from '@wordpress/components';
import './edit.css';

export default function Edit({ attributes, setAttributes }) {
	const {
		heading,
		description,
		buttonText,
		buttonUrl,
		backgroundColor,
		textAlignment
	} = attributes;

	const blockProps = useBlockProps({
		className: `wp-block-stitch-cta wp-block-stitch-cta--${backgroundColor}`
	});

	const backgroundColorMap = {
		primary: '#195de6',
		dark: '#0A0A0A',
		surface: '#141414'
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('CTA Settings', 'stitch')} initialOpen={true}>
					<TextControl
						label={__('Heading', 'stitch')}
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
					/>
					<TextControl
						label={__('Description', 'stitch')}
						value={description}
						onChange={(value) => setAttributes({ description: value })}
					/>
					<SelectControl
						label={__('Background Color', 'stitch')}
						value={backgroundColor}
						options={[
							{ label: 'Primary Blue', value: 'primary' },
							{ label: 'Dark', value: 'dark' },
							{ label: 'Surface', value: 'surface' }
						]}
						onChange={(value) => setAttributes({ backgroundColor: value })}
					/>
					<SelectControl
						label={__('Text Alignment', 'stitch')}
						value={textAlignment}
						options={[
							{ label: 'Left', value: 'left' },
							{ label: 'Center', value: 'center' },
							{ label: 'Right', value: 'right' }
						]}
						onChange={(value) => setAttributes({ textAlignment: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Button Settings', 'stitch')} initialOpen={false}>
					<TextControl
						label={__('Button Text', 'stitch')}
						value={buttonText}
						onChange={(value) => setAttributes({ buttonText: value })}
					/>
					<TextControl
						label={__('Button URL', 'stitch')}
						value={buttonUrl}
						onChange={(value) => setAttributes({ buttonUrl: value })}
						type="url"
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps} style={{ backgroundColor: backgroundColorMap[backgroundColor] }}>
				<div
					className="wp-block-stitch-cta__content"
					style={{
						padding: '60px 40px',
						textAlign: textAlignment,
						color: backgroundColor === 'primary' ? '#fff' : '#E9ECEF',
						maxWidth: '800px',
						margin: '0 auto'
					}}
				>
					<RichText
						tagName="h2"
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
						placeholder={__('Enter heading', 'stitch')}
						className="wp-block-stitch-cta__heading"
						style={{
							fontSize: '2.25rem',
							fontWeight: 700,
							marginBottom: '20px'
						}}
					/>
					<RichText
						tagName="p"
						value={description}
						onChange={(value) => setAttributes({ description: value })}
						placeholder={__('Enter description', 'stitch')}
						className="wp-block-stitch-cta__description"
						style={{
							fontSize: '1rem',
							lineHeight: 1.6,
							marginBottom: '30px',
							opacity: 0.9
						}}
					/>
					<a
						href={buttonUrl}
						className="wp-block-stitch-cta__button"
						style={{
							display: 'inline-block',
							backgroundColor: '#fff',
							color: '#195de6',
							padding: '12px 32px',
							fontSize: '1rem',
							fontWeight: 700,
							borderRadius: '8px',
							textDecoration: 'none',
							transition: 'all 0.3s ease'
						}}
					>
						{buttonText}
					</a>
				</div>
			</div>
		</>
	);
}
