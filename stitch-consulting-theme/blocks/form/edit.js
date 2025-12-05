import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
	Button,
	ToggleControl
} from '@wordpress/components';
import './edit.css';

export default function Edit({ attributes, setAttributes }) {
	const {
		formLabel,
		fields,
		submitButtonText,
		successMessage,
		formAction
	} = attributes;

	const blockProps = useBlockProps({
		className: 'wp-block-stitch-form'
	});

	// Handle individual field changes
	const handleFieldChange = (index, key, value) => {
		const newFields = [...fields];
		newFields[index] = { ...newFields[index], [key]: value };
		setAttributes({ fields: newFields });
	};

	// Add new field to form
	const addField = () => {
		const newFields = [
			...fields,
			{
				id: `field-${Date.now()}`,
				type: 'text',
				label: 'New Field',
				placeholder: 'Placeholder',
				required: false,
				errorMessage: 'This field is required'
			}
		];
		setAttributes({ fields: newFields });
	};

	// Remove field from form
	const removeField = (index) => {
		const newFields = fields.filter((_, i) => i !== index);
		setAttributes({ fields: newFields });
	};

	return (
		<>
			{/* Block Inspector Controls */}
			<InspectorControls>
				{/* Form Settings Panel */}
				<PanelBody title={__('Form Settings', 'stitch')} initialOpen={true}>
					<TextControl
						label={__('Form Label (ARIA)', 'stitch')}
						value={formLabel}
						onChange={(value) => setAttributes({ formLabel: value })}
						help={__('Accessible label for screen readers and form identification', 'stitch')}
					/>
					<SelectControl
						label={__('Form Action', 'stitch')}
						value={formAction}
						options={[
							{ label: 'Email', value: 'email' },
							{ label: 'HubSpot', value: 'hubspot' },
							{ label: 'Webhook', value: 'webhook' }
						]}
						onChange={(value) => setAttributes({ formAction: value })}
						help={__('Where to send form submissions', 'stitch')}
					/>
					<TextControl
						label={__('Submit Button Text', 'stitch')}
						value={submitButtonText}
						onChange={(value) => setAttributes({ submitButtonText: value })}
						help={__('Text displayed on submit button', 'stitch')}
					/>
					<TextControl
						label={__('Success Message', 'stitch')}
						value={successMessage}
						onChange={(value) => setAttributes({ successMessage: value })}
						help={__('Message shown after successful submission', 'stitch')}
					/>
				</PanelBody>

				{/* Form Fields Panel */}
				<PanelBody title={__('Form Fields', 'stitch')} initialOpen={true}>
					{fields.map((field, index) => (
						<div
							key={field.id}
							style={{
								border: '1px solid #262626',
								padding: '12px',
								marginBottom: '12px',
								borderRadius: '4px',
								backgroundColor: '#0A0A0A'
							}}
						>
							{/* Field Label */}
							<TextControl
								label={__('Field Label', 'stitch')}
								value={field.label}
								onChange={(value) => handleFieldChange(index, 'label', value)}
								help={__('Label displayed to users (ARIA label)', 'stitch')}
								placeholder="e.g., Your Name"
							/>

							{/* Field Type */}
							<SelectControl
								label={__('Input Type', 'stitch')}
								value={field.type}
								options={[
									{ label: 'Text', value: 'text' },
									{ label: 'Email', value: 'email' },
									{ label: 'Phone', value: 'tel' },
									{ label: 'URL', value: 'url' },
									{ label: 'Number', value: 'number' },
									{ label: 'Textarea', value: 'textarea' }
								]}
								onChange={(value) => handleFieldChange(index, 'type', value)}
								help={__('Type of input field for validation and keyboard support', 'stitch')}
							/>

							{/* Placeholder Text */}
							<TextControl
								label={__('Placeholder Text', 'stitch')}
								value={field.placeholder}
								onChange={(value) => handleFieldChange(index, 'placeholder', value)}
								help={__('Hint text shown inside input (not a substitute for labels)', 'stitch')}
								placeholder="e.g., John Doe"
							/>

							{/* Error Message for Accessibility */}
							<TextControl
								label={__('Error Message (ARIA)', 'stitch')}
								value={field.errorMessage || ''}
								onChange={(value) => handleFieldChange(index, 'errorMessage', value)}
								help={__('Error message displayed to screen readers and users when validation fails', 'stitch')}
								placeholder="e.g., Name is required"
							/>

							{/* Required Field Toggle */}
							<ToggleControl
								label={__('Required Field', 'stitch')}
								checked={field.required || false}
								onChange={(value) => handleFieldChange(index, 'required', value)}
								help={__('If checked, users must fill this field. Marked with * for accessibility.', 'stitch')}
							/>

							{/* Field Settings Summary */}
							<div style={{
								padding: '8px',
								backgroundColor: '#141414',
								borderRadius: '4px',
								fontSize: '0.85rem',
								color: '#A3A3A3',
								marginTop: '8px'
							}}>
								<strong>Field ID:</strong> {field.id}
								<br />
								<strong>Field Type:</strong> {field.type}
								{field.required && <><br /><strong style={{ color: '#EF4444' }}>Required</strong></>}
							</div>

							{/* Remove Field Button */}
							<Button
								isDestructive
								onClick={() => removeField(index)}
								variant="secondary"
								style={{ marginTop: '8px', width: '100%' }}
							>
								{__('Remove Field', 'stitch')}
							</Button>
						</div>
					))}

					{/* Add Field Button */}
					<Button
						isPrimary
						onClick={addField}
						style={{ width: '100%', marginTop: '8px' }}
					>
						{__('Add Field', 'stitch')}
					</Button>
				</PanelBody>

				{/* Accessibility Notes Panel */}
				<PanelBody title={__('Accessibility Features', 'stitch')} initialOpen={false}>
					<div style={{
						padding: '12px',
						backgroundColor: '#195de6',
						borderRadius: '4px',
						color: '#FFFFFF',
						fontSize: '0.9rem',
						lineHeight: '1.5'
					}}>
						<strong>WCAG 2.1 AA Compliant:</strong>
						<ul style={{ margin: '8px 0 0 0', paddingLeft: '20px' }}>
							<li>Full ARIA label support for all fields</li>
							<li>Error containers with aria-live regions</li>
							<li>Required field indicators with aria-required</li>
							<li>Field descriptions via aria-describedby</li>
							<li>Focus management and keyboard navigation</li>
							<li>Semantic HTML with fieldset/legend</li>
							<li>Success/error status announcements</li>
						</ul>
					</div>
				</PanelBody>
			</InspectorControls>

			{/* Editor Preview - Shows form appearance with ARIA attributes */}
			<div {...blockProps} style={{
				padding: '32px',
				backgroundColor: '#141414',
				borderRadius: '12px',
				border: '1px solid #262626'
			}}>
				<form
					role="form"
					aria-label={formLabel}
					style={{
						display: 'flex',
						flexDirection: 'column',
						gap: '16px'
					}}
					onSubmit={(e) => e.preventDefault()}
				>
					{/* Editor Form Label - Semantic fieldset */}
					<fieldset style={{
						border: 'none',
						padding: '0',
						margin: '0'
					}}>
						<legend style={{
							fontSize: '1.125rem',
							fontWeight: '700',
							color: '#FFFFFF',
							marginBottom: '16px'
						}}>
							{formLabel}
						</legend>

						{/* Render Preview Fields */}
						{fields.map((field) => (
							<div
								key={field.id}
								style={{
									display: 'flex',
									flexDirection: 'column',
									gap: '4px',
									marginBottom: '12px'
								}}
							>
								{/* Field Label with Required Indicator */}
								<label
									htmlFor={`field-${field.id}`}
									style={{
										fontSize: '0.875rem',
										fontWeight: 600,
										color: '#A3A3A3'
									}}
								>
									{field.label}
									{field.required && (
										<span
											aria-label="required"
											style={{
												color: '#EF4444',
												marginLeft: '4px'
											}}
										>
											*
										</span>
									)}
								</label>

								{/* Field Input Preview - Disabled in editor */}
								{field.type === 'textarea' ? (
									<textarea
										id={`field-${field.id}`}
										placeholder={field.placeholder}
										disabled
										aria-required={field.required}
										aria-describedby={`error-${field.id}`}
										style={{
											padding: '12px',
											borderRadius: '6px',
											border: '1px solid #262626',
											backgroundColor: '#0A0A0A',
											color: '#FFFFFF',
											fontSize: '0.95rem',
											fontFamily: 'Inter, sans-serif',
											minHeight: '100px',
											resize: 'vertical',
											cursor: 'not-allowed',
											opacity: '0.7'
										}}
									/>
								) : (
									<input
										id={`field-${field.id}`}
										type={field.type}
										placeholder={field.placeholder}
										disabled
										aria-required={field.required}
										aria-describedby={`error-${field.id}`}
										style={{
											padding: '12px',
											borderRadius: '6px',
											border: '1px solid #262626',
											backgroundColor: '#0A0A0A',
											color: '#FFFFFF',
											fontSize: '0.95rem',
											fontFamily: 'Inter, sans-serif',
											height: '44px',
											cursor: 'not-allowed',
											opacity: '0.7'
										}}
									/>
								)}

								{/* Field Error Container - Shows in editor when errors exist */}
								<div
									id={`error-${field.id}`}
									role="alert"
									style={{
										color: '#d32f2f',
										fontSize: '0.875rem',
										marginTop: '0.25rem',
										minHeight: '1.25rem',
										display: field.errorMessage ? 'block' : 'none'
									}}
								>
									{field.errorMessage && (
										<small>{field.errorMessage}</small>
									)}
								</div>
							</div>
						))}
					</fieldset>

					{/* Submit Button Preview */}
					<button
						type="submit"
						disabled
						aria-label={submitButtonText}
						aria-disabled="false"
						style={{
							padding: '12px 32px',
							borderRadius: '6px',
							backgroundColor: '#195de6',
							color: '#FFFFFF',
							fontSize: '1rem',
							fontWeight: 700,
							border: 'none',
							cursor: 'not-allowed',
							marginTop: '8px',
							opacity: '0.7'
						}}
					>
						{submitButtonText}
					</button>
				</form>

				{/* Editor Hint */}
				{fields.length === 0 && (
					<div style={{
						padding: '16px',
						backgroundColor: 'rgba(25, 93, 230, 0.1)',
						border: '1px solid #195de6',
						borderRadius: '6px',
						color: '#195de6',
						fontSize: '0.9rem',
						marginTop: '16px',
						textAlign: 'center'
					}}>
						{__('Add fields using the inspector panel on the right', 'stitch')}
					</div>
				)}
			</div>
		</>
	);
}
