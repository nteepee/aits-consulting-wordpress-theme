import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck
} from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	RangeControl,
	TextControl
} from '@wordpress/components';
import './edit.css';

export default function Edit({ attributes, setAttributes }) {
	const {
		heading,
		subheading,
		backgroundImageUrl,
		backgroundImageId,
		overlayOpacity,
		primaryButtonText,
		primaryButtonUrl,
		secondaryButtonText,
		secondaryButtonUrl,
		minHeight
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wp-block-stitch-hero',
		style: {
			backgroundImage: backgroundImageUrl ? `url(${backgroundImageUrl})` : 'none',
			backgroundSize: 'cover',
			backgroundPosition: 'center',
			minHeight: minHeight,
			position: 'relative'
		}
	});

	const onSelectImage = (media) => {
		setAttributes({
			backgroundImageId: media.id,
			backgroundImageUrl: media.url
		});
	};

	const onRemoveImage = () => {
		setAttributes({
			backgroundImageId: 0,
			backgroundImageUrl: ''
		});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Hero Settings', 'stitch')} initialOpen={true}>
					<TextControl
						label={__('Heading', 'stitch')}
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
					/>
					<TextControl
						label={__('Subheading', 'stitch')}
						value={subheading}
						onChange={(value) => setAttributes({ subheading: value })}
					/>
					<RangeControl
						label={__('Overlay Opacity', 'stitch')}
						value={overlayOpacity}
						onChange={(value) => setAttributes({ overlayOpacity: value })}
						min={0}
						max={1}
						step={0.1}
					/>
					<TextControl
						label={__('Minimum Height', 'stitch')}
						value={minHeight}
						onChange={(value) => setAttributes({ minHeight: value })}
						help={__('e.g., 600px, 80vh', 'stitch')}
					/>
				</PanelBody>

				<PanelBody title={__('Primary Button', 'stitch')} initialOpen={false}>
					<TextControl
						label={__('Button Text', 'stitch')}
						value={primaryButtonText}
						onChange={(value) => setAttributes({ primaryButtonText: value })}
					/>
					<TextControl
						label={__('Button URL', 'stitch')}
						value={primaryButtonUrl}
						onChange={(value) => setAttributes({ primaryButtonUrl: value })}
						type="url"
					/>
				</PanelBody>

				<PanelBody title={__('Secondary Button', 'stitch')} initialOpen={false}>
					<TextControl
						label={__('Button Text', 'stitch')}
						value={secondaryButtonText}
						onChange={(value) => setAttributes({ secondaryButtonText: value })}
					/>
					<TextControl
						label={__('Button URL', 'stitch')}
						value={secondaryButtonUrl}
						onChange={(value) => setAttributes({ secondaryButtonUrl: value })}
						type="url"
					/>
				</PanelBody>

				<PanelBody title={__('Background Image', 'stitch')} initialOpen={false}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={onSelectImage}
							allowedTypes={['image']}
							value={backgroundImageId}
							render={({ open }) => (
								<div>
									{backgroundImageUrl ? (
										<>
											<img
												src={backgroundImageUrl}
												alt={__('Hero background', 'stitch')}
												style={{ maxWidth: '100%', height: 'auto' }}
											/>
											<Button
												onClick={onRemoveImage}
												variant="secondary"
												isDestructive
												style={{ marginTop: '10px' }}
											>
												{__('Remove Image', 'stitch')}
											</Button>
										</>
									) : (
										<Button
											onClick={open}
											variant="primary"
										>
											{__('Upload Image', 'stitch')}
										</Button>
									)}
									{backgroundImageUrl && (
										<Button
											onClick={open}
											variant="secondary"
											style={{ marginTop: '10px' }}
										>
											{__('Change Image', 'stitch')}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div
					className="wp-block-stitch-hero__overlay"
					style={{
						position: 'absolute',
						top: 0,
						left: 0,
						right: 0,
						bottom: 0,
						backgroundColor: `rgba(10, 10, 10, ${overlayOpacity})`,
						zIndex: 1
					}}
				/>
				<div
					style={{
						position: 'relative',
						zIndex: 2,
						padding: '80px 40px',
						display: 'flex',
						flexDirection: 'column',
						justifyContent: 'center',
						alignItems: 'center',
						textAlign: 'center',
						color: '#fff',
						minHeight: minHeight
					}}
				>
					<RichText
						tagName="h1"
						value={heading}
						onChange={(value) => setAttributes({ heading: value })}
						placeholder={__('Enter hero heading', 'stitch')}
						className="wp-block-stitch-hero__heading"
						style={{
							fontSize: '3.75rem',
							fontWeight: 900,
							lineHeight: 1.2,
							marginBottom: '20px',
							maxWidth: '800px'
						}}
					/>
					<RichText
						tagName="p"
						value={subheading}
						onChange={(value) => setAttributes({ subheading: value })}
						placeholder={__('Enter subheading', 'stitch')}
						className="wp-block-stitch-hero__subheading"
						style={{
							fontSize: '1.25rem',
							lineHeight: 1.6,
							marginBottom: '40px',
							maxWidth: '600px',
							color: '#E9ECEF'
						}}
					/>
					<div
						style={{
							display: 'flex',
							gap: '16px',
							justifyContent: 'center',
							flexWrap: 'wrap'
						}}
					>
						<Button
							isPrimary
							href={primaryButtonUrl}
							style={{
								backgroundColor: '#195de6',
								color: '#fff',
								padding: '12px 32px',
								fontSize: '1rem',
								fontWeight: 700,
								borderRadius: '8px',
								textDecoration: 'none'
							}}
						>
							{primaryButtonText}
						</Button>
						<Button
							isSecondary
							href={secondaryButtonUrl}
							style={{
								backgroundColor: 'transparent',
								color: '#fff',
								border: '2px solid #fff',
								padding: '10px 30px',
								fontSize: '1rem',
								fontWeight: 700,
								borderRadius: '8px',
								textDecoration: 'none'
							}}
						>
							{secondaryButtonText}
						</Button>
					</div>
				</div>
			</div>
		</>
	);
}
