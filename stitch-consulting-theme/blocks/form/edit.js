import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Button
} from '@wordpress/components';
import './edit.css';

export default function Edit({ attributes, setAttributes }) {
	const {
		fields,
		submitButtonText,
		successMessage,
		formAction
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wp-block-stitch-form'
	});

	const handleFieldChange = (index, key, value) => {
		const newFields = [...fields];
		newFields[index] = { ...newFields[index], [key]: value };
		setAttributes({ fields: newFields });
	};

	const addField = () => {
		const newFields = [
			...fields,
			{
				id: `field-${Date.now()}`,
				type: 'text',
				label: 'New Field',
				placeholder: 'Placeholder',
				required: false
			}
		];
		setAttributes({ fields: newFields });
	};

	const removeField = (index) => {
		const newFields = fields.filter((_, i) => i !== index);
		setAttributes({ fields: newFields });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Form Settings', 'stitch')} initialOpen={true}>
					<SelectControl
						label={__('Form Action', 'stitch')}
						value={formAction}
						options={[
							{ label: 'Email', value: 'email' },
							{ label: 'HubSpot', value: 'hubspot' },
							{ label: 'Webhook', value: 'webhook' }
						]}
						onChange={(value) => setAttributes({ formAction: value })}
					/>
					<TextControl
						label={__('Submit Button Text', 'stitch')}
						value={submitButtonText}
						onChange={(value) => setAttributes({ submitButtonText: value })}
					/>
					<TextControl
						label={__('Success Message', 'stitch')}
						value={successMessage}
						onChange={(value) => setAttributes({ successMessage: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Form Fields', 'stitch')} initialOpen={true}>
					{fields.map((field, index) => (
						<div key={field.id} style={{
							border: '1px solid #262626',
							padding: '12px',
							marginBottom: '12px',
							borderRadius: '4px'
						}}>
							<TextControl
								label={__('Label', 'stitch')}
								value={field.label}
								onChange={(value) => handleFieldChange(index, 'label', value)}
							/>
							<SelectControl
								label={__('Type', 'stitch')}
								value={field.type}
								options={[
									{ label: 'Text', value: 'text' },
									{ label: 'Email', value: 'email' },
									{ label: 'Phone', value: 'tel' },
									{ label: 'Textarea', value: 'textarea' }
								]}
								onChange={(value) => handleFieldChange(index, 'type', value)}
							/>
							<TextControl
								label={__('Placeholder', 'stitch')}
								value={field.placeholder}
								onChange={(value) => handleFieldChange(index, 'placeholder', value)}
							/>
							<Button
								isDestructive
								onClick={() => removeField(index)}
								variant="secondary"
							>
								{__('Remove Field', 'stitch')}
							</Button>
						</div>
					))}
					<Button
						isPrimary
						onClick={addField}
					>
						{__('Add Field', 'stitch')}
					</Button>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps} style={{
				padding: '32px',
				backgroundColor: '#141414',
				borderRadius: '12px',
				border: '1px solid #262626'
			}}>
				<h3 style={{ color: '#FFFFFF', marginTop: 0 }}>{__('Contact Form', 'stitch')}</h3>
				<form style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
					{fields.map((field) => (
						<div key={field.id} style={{ display: 'flex', flexDirection: 'column', gap: '4px' }}>
							<label style={{
								fontSize: '0.875rem',
								fontWeight: 600,
								color: '#A3A3A3'
							}}>
								{field.label}
								{field.required && <span style={{ color: '#EF4444' }}>*</span>}
							</label>
							{field.type === 'textarea' ? (
								<textarea
									placeholder={field.placeholder}
									disabled
									style={{
										padding: '12px',
										borderRadius: '6px',
										border: '1px solid #262626',
										backgroundColor: '#0A0A0A',
										color: '#FFFFFF',
										fontSize: '0.95rem',
										fontFamily: 'Inter, sans-serif',
										minHeight: '100px',
										resize: 'vertical'
									}}
								/>
							) : (
								<input
									type={field.type}
									placeholder={field.placeholder}
									disabled
									style={{
										padding: '12px',
										borderRadius: '6px',
										border: '1px solid #262626',
										backgroundColor: '#0A0A0A',
										color: '#FFFFFF',
										fontSize: '0.95rem',
										fontFamily: 'Inter, sans-serif',
										height: '44px'
									}}
								/>
							)}
						</div>
					))}
					<button
						type="submit"
						disabled
						style={{
							padding: '12px 32px',
							borderRadius: '6px',
							backgroundColor: '#195de6',
							color: '#FFFFFF',
							fontSize: '1rem',
							fontWeight: 700,
							border: 'none',
							cursor: 'pointer',
							marginTop: '8px'
						}}
					>
						{submitButtonText}
					</button>
				</form>
			</div>
		</>
	);
}
