import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		heading,
		subheading,
		backgroundImageUrl,
		overlayOpacity,
		primaryButtonText,
		primaryButtonUrl,
		secondaryButtonText,
		secondaryButtonUrl,
		minHeight
	} = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-stitch-hero'
	});

	return (
		<div {...blockProps}>
			<div
				className="wp-block-stitch-hero__container"
				style={{
					backgroundImage: backgroundImageUrl ? `url(${backgroundImageUrl})` : 'none',
					backgroundSize: 'cover',
					backgroundPosition: 'center',
					minHeight: minHeight,
					position: 'relative'
				}}
			>
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
					className="wp-block-stitch-hero__content"
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
					{heading && (
						<RichText.Content
							tagName="h1"
							className="wp-block-stitch-hero__heading"
							value={heading}
							style={{
								fontSize: '3.75rem',
								fontWeight: 900,
								lineHeight: 1.2,
								marginBottom: '20px',
								maxWidth: '800px'
							}}
						/>
					)}
					{subheading && (
						<RichText.Content
							tagName="p"
							className="wp-block-stitch-hero__subheading"
							value={subheading}
							style={{
								fontSize: '1.25rem',
								lineHeight: 1.6,
								marginBottom: '40px',
								maxWidth: '600px',
								color: '#E9ECEF'
							}}
						/>
					)}
					<div
						className="wp-block-stitch-hero__buttons"
						style={{
							display: 'flex',
							gap: '16px',
							justifyContent: 'center',
							flexWrap: 'wrap'
						}}
					>
						{primaryButtonText && primaryButtonUrl && (
							<a
								href={primaryButtonUrl}
								className="wp-block-stitch-hero__button wp-block-stitch-hero__button--primary"
								style={{
									backgroundColor: '#195de6',
									color: '#fff',
									padding: '12px 32px',
									fontSize: '1rem',
									fontWeight: 700,
									borderRadius: '8px',
									textDecoration: 'none',
									display: 'inline-block',
									transition: 'all 0.3s ease'
								}}
							>
								{primaryButtonText}
							</a>
						)}
						{secondaryButtonText && secondaryButtonUrl && (
							<a
								href={secondaryButtonUrl}
								className="wp-block-stitch-hero__button wp-block-stitch-hero__button--secondary"
								style={{
									backgroundColor: 'transparent',
									color: '#fff',
									border: '2px solid #fff',
									padding: '10px 30px',
									fontSize: '1rem',
									fontWeight: 700,
									borderRadius: '8px',
									textDecoration: 'none',
									display: 'inline-block',
									transition: 'all 0.3s ease'
								}}
							>
								{secondaryButtonText}
							</a>
						)}
					</div>
				</div>
			</div>
		</div>
	);
}
