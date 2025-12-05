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
	TextControl,
	Button,
	RangeControl
} from '@wordpress/components';
import './edit.css';

export default function Edit({ attributes, setAttributes }) {
	const {
		quote,
		author,
		role,
		authorImageId,
		authorImageUrl,
		rating
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wp-block-stitch-testimonial'
	});

	const onSelectImage = (media) => {
		setAttributes({
			authorImageId: media.id,
			authorImageUrl: media.url
		});
	};

	const onRemoveImage = () => {
		setAttributes({
			authorImageId: 0,
			authorImageUrl: ''
		});
	};

	const renderStars = (value) => {
		return '★'.repeat(value) + '☆'.repeat(5 - value);
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Testimonial Settings', 'stitch')} initialOpen={true}>
					<TextControl
						label={__('Quote', 'stitch')}
						value={quote}
						onChange={(value) => setAttributes({ quote: value })}
					/>
					<TextControl
						label={__('Author Name', 'stitch')}
						value={author}
						onChange={(value) => setAttributes({ author: value })}
					/>
					<TextControl
						label={__('Author Role/Company', 'stitch')}
						value={role}
						onChange={(value) => setAttributes({ role: value })}
					/>
					<RangeControl
						label={__('Rating', 'stitch')}
						value={rating}
						onChange={(value) => setAttributes({ rating: value })}
						min={0}
						max={5}
						step={0.5}
					/>
				</PanelBody>

				<PanelBody title={__('Author Image', 'stitch')} initialOpen={false}>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={onSelectImage}
							allowedTypes={['image']}
							value={authorImageId}
							render={({ open }) => (
								<div>
									{authorImageUrl ? (
										<>
											<img
												src={authorImageUrl}
												alt={__('Author', 'stitch')}
												style={{ maxWidth: '100%', height: 'auto', borderRadius: '8px', marginBottom: '10px' }}
											/>
											<Button
												onClick={onRemoveImage}
												variant="secondary"
												isDestructive
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
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps} style={{
				padding: '32px',
				backgroundColor: '#141414',
				borderRadius: '12px',
				border: '1px solid #262626'
			}}>
				<div style={{ display: 'flex', gap: '20px', alignItems: 'flex-start' }}>
					{authorImageUrl && (
						<img
							src={authorImageUrl}
							alt={author}
							style={{
								width: '60px',
								height: '60px',
								borderRadius: '50%',
								objectFit: 'cover',
								flexShrink: 0
							}}
						/>
					)}
					<div style={{ flex: 1 }}>
						<div style={{
							fontSize: '1.25rem',
							color: '#195de6',
							marginBottom: '12px',
							lineHeight: 1
						}}>
							{renderStars(rating)}
						</div>
						<RichText
							tagName="blockquote"
							value={quote}
							onChange={(value) => setAttributes({ quote: value })}
							placeholder={__('Enter testimonial quote', 'stitch')}
							style={{
								fontSize: '1rem',
								lineHeight: 1.6,
								marginBottom: '16px',
								marginTop: 0,
								marginLeft: 0,
								marginRight: 0,
								fontStyle: 'italic',
								color: '#E9ECEF'
							}}
						/>
						<div>
							<RichText
								tagName="p"
								value={author}
								onChange={(value) => setAttributes({ author: value })}
								placeholder={__('Author name', 'stitch')}
								style={{
									fontSize: '0.95rem',
									fontWeight: 600,
									marginBottom: '4px',
									marginTop: 0,
									color: '#FFFFFF'
								}}
							/>
							<RichText
								tagName="p"
								value={role}
								onChange={(value) => setAttributes({ role: value })}
								placeholder={__('Author role', 'stitch')}
								style={{
									fontSize: '0.85rem',
									marginBottom: 0,
									marginTop: 0,
									color: '#A3A3A3'
								}}
							/>
						</div>
					</div>
				</div>
			</div>
		</>
	);
}
