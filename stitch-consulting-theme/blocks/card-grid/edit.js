import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	TextControl
} from '@wordpress/components';
import './edit.css';

const ALLOWED_BLOCKS = ['stitch/feature-card'];
const TEMPLATE = [
	['stitch/feature-card'],
	['stitch/feature-card'],
	['stitch/feature-card']
];

export default function Edit({ attributes, setAttributes }) {
	const {
		columns,
		gap,
		backgroundColor
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wp-block-stitch-card-grid'
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Grid Settings', 'stitch')} initialOpen={true}>
					<RangeControl
						label={__('Columns', 'stitch')}
						value={columns}
						onChange={(value) => setAttributes({ columns: value })}
						min={1}
						max={4}
					/>
					<TextControl
						label={__('Gap (spacing)', 'stitch')}
						value={gap}
						onChange={(value) => setAttributes({ gap: value })}
						help={__('e.g., 16px, 2rem', 'stitch')}
					/>
					<TextControl
						label={__('Background Color', 'stitch')}
						value={backgroundColor}
						onChange={(value) => setAttributes({ backgroundColor: value })}
						help={__('e.g., #141414, transparent', 'stitch')}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps} style={{
				display: 'grid',
				gridTemplateColumns: `repeat(${columns}, 1fr)`,
				gap: gap,
				backgroundColor: backgroundColor,
				padding: '0'
			}}>
				<InnerBlocks
					allowedBlocks={ALLOWED_BLOCKS}
					template={TEMPLATE}
					orientation="vertical"
				/>
			</div>
		</>
	);
}
