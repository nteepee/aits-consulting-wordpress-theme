import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const {
		fields,
		submitButtonText,
		successMessage,
		formAction
	} = attributes;

	const blockProps = useBlockProps.save({
		className: 'wp-block-stitch-form'
	});

	return (
		<div {...blockProps} style={{
			padding: '32px',
			backgroundColor: '#141414',
			borderRadius: '12px',
			border: '1px solid #262626'
		}}>
			<form
				method="POST"
				className="wp-block-stitch-form__form"
				style={{
					display: 'flex',
					flexDirection: 'column',
					gap: '16px'
				}}
			>
				{fields.map((field) => (
					<div key={field.id} style={{
						display: 'flex',
						flexDirection: 'column',
						gap: '4px'
					}}>
						<label
							htmlFor={field.id}
							style={{
								fontSize: '0.875rem',
								fontWeight: 600,
								color: '#A3A3A3'
							}}
						>
							{field.label}
							{field.required && <span style={{ color: '#EF4444' }}>*</span>}
						</label>
						{field.type === 'textarea' ? (
							<textarea
								id={field.id}
								name={field.id}
								placeholder={field.placeholder}
								required={field.required}
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
								id={field.id}
								name={field.id}
								placeholder={field.placeholder}
								required={field.required}
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
				<input
					type="hidden"
					name="action"
					value={`stitch_form_${formAction}`}
				/>
				<input
					type="hidden"
					name="success_message"
					value={successMessage}
				/>
				<button
					type="submit"
					className="wp-block-stitch-form__button"
					style={{
						padding: '12px 32px',
						borderRadius: '6px',
						backgroundColor: '#195de6',
						color: '#FFFFFF',
						fontSize: '1rem',
						fontWeight: 700,
						border: 'none',
						cursor: 'pointer',
						marginTop: '8px',
						transition: 'all 0.3s ease'
					}}
				>
					{submitButtonText}
				</button>
			</form>
		</div>
	);
}
