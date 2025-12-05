import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	Button
} from '@wordpress/components';
import './edit.css';

const MATERIAL_ICONS = [
	'star', 'check_circle', 'flash_on', 'settings', 'rocket_launch',
	'shield', 'target', 'lightbulb', 'code', 'packages',
	'people', 'storage', 'trending_up', 'verified', 'auto_awesome'
];

export default function Edit({ attributes, setAttributes }) {
	const {
		stats,
		columns,
		gap
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wp-block-stitch-stats'
	});

	const handleStatChange = (index, key, value) => {
		const newStats = [...stats];
		newStats[index] = { ...newStats[index], [key]: value };
		setAttributes({ stats: newStats });
	};

	const addStat = () => {
		const newStats = [
			...stats,
			{
				id: `stat-${Date.now()}`,
				value: 'Value',
				label: 'Label',
				icon: 'star'
			}
		];
		setAttributes({ stats: newStats });
	};

	const removeStat = (index) => {
		const newStats = stats.filter((_, i) => i !== index);
		setAttributes({ stats: newStats });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Stats Grid Settings', 'stitch')} initialOpen={true}>
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
						help={__('e.g., 24px, 2rem', 'stitch')}
					/>
				</PanelBody>

				<PanelBody title={__('Statistics', 'stitch')} initialOpen={true}>
					{stats.map((stat, index) => (
						<div key={stat.id} style={{
							border: '1px solid #262626',
							padding: '12px',
							marginBottom: '12px',
							borderRadius: '4px'
						}}>
							<TextControl
								label={__('Value', 'stitch')}
								value={stat.value}
								onChange={(value) => handleStatChange(index, 'value', value)}
								help={__('e.g., 1000+, 99.9%', 'stitch')}
							/>
							<TextControl
								label={__('Label', 'stitch')}
								value={stat.label}
								onChange={(value) => handleStatChange(index, 'label', value)}
							/>
							<TextControl
								label={__('Icon', 'stitch')}
								value={stat.icon}
								onChange={(value) => handleStatChange(index, 'icon', value)}
							/>
							<Button
								isDestructive
								onClick={() => removeStat(index)}
								variant="secondary"
							>
								{__('Remove Stat', 'stitch')}
							</Button>
						</div>
					))}
					<Button
						isPrimary
						onClick={addStat}
					>
						{__('Add Statistic', 'stitch')}
					</Button>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps} style={{
				display: 'grid',
				gridTemplateColumns: `repeat(${columns}, 1fr)`,
				gap: gap,
				padding: '0'
			}}>
				{stats.map((stat) => (
					<div key={stat.id} style={{
						textAlign: 'center',
						padding: '24px'
					}}>
						<div style={{
							fontFamily: '"Material Symbols Outlined"',
							fontSize: '2.5rem',
							color: '#195de6',
							marginBottom: '12px'
						}}>
							{stat.icon}
						</div>
						<div style={{
							fontSize: '2rem',
							fontWeight: 900,
							color: '#FFFFFF',
							marginBottom: '8px'
						}}>
							{stat.value}
						</div>
						<div style={{
							fontSize: '0.95rem',
							color: '#A3A3A3'
						}}>
							{stat.label}
						</div>
					</div>
				))}
			</div>
		</>
	);
}
