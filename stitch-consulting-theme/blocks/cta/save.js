import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		heading,
		description,
		buttonText,
		buttonUrl,
		backgroundColor,
		textAlignment
	} = attributes;

	const blockProps = useBlockProps.save({
		className: `wp-block-stitch-cta wp-block-stitch-cta--${backgroundColor}`
	});

	const backgroundColorMap = {
		primary: '#195de6',
		dark: '#0A0A0A',
		surface: '#141414'
	};

	return (
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
				{heading && (
					<RichText.Content
						tagName="h2"
						className="wp-block-stitch-cta__heading"
						value={heading}
						style={{
							fontSize: '2.25rem',
							fontWeight: 700,
							marginBottom: '20px'
						}}
					/>
				)}
				{description && (
					<RichText.Content
						tagName="p"
						className="wp-block-stitch-cta__description"
						value={description}
						style={{
							fontSize: '1rem',
							lineHeight: 1.6,
							marginBottom: '30px',
							opacity: 0.9
						}}
					/>
				)}
				{buttonText && buttonUrl && (
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
				)}
			</div>
		</div>
	);
}
