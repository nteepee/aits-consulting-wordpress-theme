import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		quote,
		author,
		role,
		authorImageUrl,
		rating
	} = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-stitch-testimonial'
	});

	const renderStars = (value) => {
		return '★'.repeat(Math.round(value)) + '☆'.repeat(5 - Math.round(value));
	};

	return (
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
						className="wp-block-stitch-testimonial__image"
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
					<div className="wp-block-stitch-testimonial__rating" style={{
						fontSize: '1.25rem',
						color: '#195de6',
						marginBottom: '12px',
						lineHeight: 1
					}}>
						{renderStars(rating)}
					</div>
					{quote && (
						<RichText.Content
							tagName="blockquote"
							className="wp-block-stitch-testimonial__quote"
							value={quote}
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
					)}
					<div>
						{author && (
							<RichText.Content
								tagName="p"
								className="wp-block-stitch-testimonial__author"
								value={author}
								style={{
									fontSize: '0.95rem',
									fontWeight: 600,
									marginBottom: '4px',
									marginTop: 0,
									color: '#FFFFFF'
								}}
							/>
						)}
						{role && (
							<RichText.Content
								tagName="p"
								className="wp-block-stitch-testimonial__role"
								value={role}
								style={{
									fontSize: '0.85rem',
									marginBottom: 0,
									marginTop: 0,
									color: '#A3A3A3'
								}}
							/>
						)}
					</div>
				</div>
			</div>
		</div>
	);
}
