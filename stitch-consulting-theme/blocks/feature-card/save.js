import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		icon,
		title,
		description,
		linkText,
		linkUrl,
		variant
	} = attributes;

	const blockProps = useBlockProps.save({
		className: `wp-block-stitch-feature-card wp-block-stitch-feature-card--${variant}`
	});

	return (
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

			{title && (
				<RichText.Content
					tagName="h3"
					className="wp-block-stitch-feature-card__title"
					value={title}
					style={{
						fontSize: '1.25rem',
						fontWeight: 700,
						marginBottom: '12px',
						marginTop: 0,
						color: '#FFFFFF'
					}}
				/>
			)}

			{description && (
				<RichText.Content
					tagName="p"
					className="wp-block-stitch-feature-card__description"
					value={description}
					style={{
						fontSize: '0.95rem',
						lineHeight: 1.6,
						marginBottom: '16px',
						marginTop: 0,
						color: '#A3A3A3'
					}}
				/>
			)}

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
	);
}
