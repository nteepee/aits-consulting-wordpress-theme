import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		columns,
		gap,
		backgroundColor
	} = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-stitch-card-grid'
	});

	return (
		<div {...blockProps} style={{
			display: 'grid',
			gridTemplateColumns: `repeat(${columns}, 1fr)`,
			gap: gap,
			backgroundColor: backgroundColor,
			padding: '0'
		}}>
			<InnerBlocks.Content />
		</div>
	);
}
