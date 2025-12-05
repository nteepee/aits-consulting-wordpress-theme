import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		stats,
		columns,
		gap
	} = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-stitch-stats'
	});

	return (
		<div {...blockProps} style={{
			display: 'grid',
			gridTemplateColumns: `repeat(${columns}, 1fr)`,
			gap: gap,
			padding: '0'
		}}>
			{stats.map((stat) => (
				<div key={stat.id} className="wp-block-stitch-stats__stat" style={{
					textAlign: 'center',
					padding: '24px'
				}}>
					<div className="wp-block-stitch-stats__icon" style={{
						fontFamily: '"Material Symbols Outlined"',
						fontSize: '2.5rem',
						color: '#195de6',
						marginBottom: '12px'
					}}>
						{stat.icon}
					</div>
					<div className="wp-block-stitch-stats__value" style={{
						fontSize: '2rem',
						fontWeight: 900,
						color: '#FFFFFF',
						marginBottom: '8px'
					}}>
						{stat.value}
					</div>
					<div className="wp-block-stitch-stats__label" style={{
						fontSize: '0.95rem',
						color: '#A3A3A3'
					}}>
						{stat.label}
					</div>
				</div>
			))}
		</div>
	);
}
